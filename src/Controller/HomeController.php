<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly SubjectRepository $subjectRepository
    )
    {
    }

    #[Route('/')]
    #[IsGranted("IS_AUTHENTICATED")]
    public function index(): Response
    {
        $subjects = $this->subjectRepository->findAllByCreationDate();

        return $this->render('home/index.html.twig', [
            'subjects' => $subjects,
        ]);
    }
}
