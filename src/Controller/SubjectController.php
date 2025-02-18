<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Subject;
use App\Form\CommentFormType;
use App\Form\SubjectEditFormType;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subject')]
class SubjectController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly SubjectRepository $subjectRepository
    )
    {
    }

    #[Route('/create', name: 'app_subject_create')]
    public function create(Request $request): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectEditFormType::class, $subject);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setCreatedAt(new \DateTimeImmutable());
            $subject->setAuthor($this->getUser());
            $subject->setLikes(0);
            $subject->setDislikes(0);

            $this->manager->persist($subject);
            $this->manager->flush();

            $this->addFlash("successs", "Le sujet à été créé avec succès");

            return $this->redirectToRoute("app_subject_detail", ["id" => $subject->getId()]);
        }

        return $this->render('subject/create.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_subject_edit')]
    public function edit(Request $request): Response {
        $id = $request->get('id');

        if (!$id || intval($id) < 0) {
            $this->addFlash("danger", "Votre demande n'a pas pu être traitée");
            return $this->redirectToRoute('app_home_index');
        }

        $subject = $this->subjectRepository->find($id);

        if (!$subject || ($subject->getAuthor() !== $this->getUser() && !in_array("ADMIN", $subject->getAuthor()->getRoles(), true))) {
            $this->addFlash("danger", "Le sujet n'existe pas");
            return $this->redirectToRoute('app_home_index');
        }

        $form = $this->createForm(SubjectEditFormType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($subject);
            $this->manager->flush();

            $this->addFlash("success", "Le sujet a bien été modifié");
            return $this->redirectToRoute("app_subject_detail", ["id" => $subject->getId()]);
        }

        return $this->render('subject/edit.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_subject_detail')]
    public function detail(Request $request): Response {
        $id = $request->get('id');
        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        if (!$id || intval($id) < 0) {
            $this->addFlash("danger", "Votre demande n'a pas pu être traitée");
            return $this->redirectToRoute('app_home_index');
        }

        $subject = $this->subjectRepository->find($id);

        if (!$subject) {
            $this->addFlash("danger", "Le sujet n'existe pas");
            return $this->redirectToRoute('app_home_index');
        }

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setSubject($subject);
            $comment->setAuthor($this->getUser());
            $comment->setReported(false);

            $this->manager->persist($comment);
            $this->manager->flush();

            $this->addFlash("success", "Votre commentaire a été enregistré");
            return $this->redirectToRoute('app_subject_detail', ["id" => $subject->getId()]);
        }

        return $this->render('subject/details.html.twig', [
            "subject" => $subject,
            "commentsForm" => $commentForm->createView(),
        ]);
    }

    #[Route('/remove/{id}', name: 'app_subject_delete')]
    public function delete(Request $request): Response {
        $id = $request->get('id');
        if (!$id || intval($id) < 0) {
            $this->addFlash("danger", "Votre demande n'a pas pu être traitée");
            return $this->redirectToRoute('app_home_index');
        }

        $subject = $this->subjectRepository->find($id);

        if (!$subject || ($subject->getAuthor() !== $this->getUser() && !in_array("ADMIN", $subject->getAuthor()->getRoles(), true))) {
            $this->addFlash("danger", "Le sujet n'existe pas");
            return $this->redirectToRoute('app_home_index');
        }

        if (!$subject->getComments()->isEmpty()) {
            $this->addFlash("danger", "Un sujet avec des commentaires ne peut pas être supprimé");
            return $this->redirectToRoute('app_subject_detail', ["id" => $subject->getId()]);
        }

        $this->manager->remove($subject);
        $this->manager->flush();

        $this->addFlash("success", "Le sujet a bien été supprimé");
        return $this->redirectToRoute("app_home_index");
    }

    #[Route("/like/{id}/{mode}", name: 'app_subject_like')]
    public function likeOrDislike(Request $request): Response {
        $type = $request->get('mode');

        if ($type !== 'like' && $type !== 'dislike') {
            $this->addFlash("danger", "Votre demande n'a pas pu être traitée");
            return $this->redirectToRoute('app_home_index');
        }

        $id = $request->get('id');
        if (!$id || intval($id) < 0) {
            $this->addFlash("danger", "Votre demande n'a pas pu être traitée");
            return $this->redirectToRoute('app_home_index');
        }

        $subject = $this->subjectRepository->find($id);

        if ($subject->getAuthor() === $this->getUser()) {
            $this->addFlash("danger", "Votre demande n'a pas pu être traitée");
            return $this->redirectToRoute('app_home_index');
        }

        if ($type === 'dislike') {
            $subject->setDislikes($subject->getDislikes() + 1);
        } else {
            $subject->setLikes($subject->getLikes() + 1);
        }

        $this->manager->persist($subject);
        $this->manager->flush();

        $this->addFlash("success", "Votre avis a été pris en compte");
        return $this->redirectToRoute('app_home_index');
    }
}
