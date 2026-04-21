<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page de politique de confidentialite.
final class ConfidentialiteController extends AbstractController
{
    // Rend simplement le contenu legal de confidentialite.
    #[Route('/confidentialite', name: 'app_confidentialite')]
    public function index(): Response
    {
        return $this->render('legales/confidentialite.html.twig', [
            'controller_name' => 'ConfidentialiteController',
        ]);
    }
}
