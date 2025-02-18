<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/comment")]
class CommentController extends AbstractController
{
    public function __construct(
        private readonly CommentRepository $commentRepository
    )
    {
    }

    #[Route('/{id}/delete', name: "app_comment_delete")]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $id = $request->get("id");
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            $this->addFlash("error", "Le commentaire n'existe pas.");
            return $this->redirectToRoute("app_home_index");
        }

        if ($comment->getAuthor() !== $this->getUser() && !in_array("ADMIN", $comment->getAuthor()->getRoles(), true)) {
            $this->addFlash("danger", "Vous n'avez pas la permission de supprimer ce commentaire.");
            return $this->redirectToRoute("app_subject_detail", [
                "id" => $comment->getSubject()->getId(),
            ]);
        }

        $subjectId = $comment->getSubject()->getId();

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash("danger", "Le commentaire a été supprimé avec succès");
        return $this->redirectToRoute("app_subject_detail", [
            "id" => $subjectId,
        ]);
    }

    #[Route("/{id}/edit", name: "app_comment_edit")]
    public function edit(Request $request, EntityManagerInterface $manager): Response {
        $id = $request->get("id");
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            $this->addFlash("error", "Le commentaire n'existe pas.");
            return $this->redirectToRoute("app_home_index");
        }

        if ($comment->getAuthor() !== $this->getUser() && !in_array("ADMIN", $comment->getAuthor()->getRoles(), true)) {
            $this->addFlash("danger", "Vous n'avez pas la permission de supprimer ce commentaire.");
            return $this->redirectToRoute("app_subject_detail", [
                "id" => $comment->getSubject()->getId(),
            ]);
        }

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success", "le commentaire a été modifié avec succès.");
            return $this->redirectToRoute("app_subject_detail", [
                "id" => $comment->getSubject()->getId(),
            ]);
        }
        return $this->render('comment/edit.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route("/{id}/report", name: "app_comment_edit")]
    public function report(Request $request, EntityManagerInterface $manager): Response {
        $id = $request->get("id");
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            $this->addFlash("error", "Le commentaire n'existe pas.");
            return $this->redirectToRoute("app_home_index");
        }

        if ($comment->getAuthor() === $this->getUser() || in_array("ADMIN", $comment->getAuthor()->getRoles(), true)) {
            $this->addFlash("danger", "Vous ne pouvez pas signaler ce commentaire.");
            return $this->redirectToRoute("app_subject_detail", [
                "id" => $comment->getSubject()->getId(),
            ]);
        }

        if ($comment->isReported()) {
            $comment->setReported(true);
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success", "Le message a été signalé avec succès");
            return $this->redirectToRoute("app_subject_detail", [
                "id" => $comment->getSubject()->getId(),
            ]);
        }

        return $this->redirectToRoute("app_subject_detail", [
            "id" => $comment->getSubject()->getId(),
        ]);
    }
}
