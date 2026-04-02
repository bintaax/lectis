<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour confidentialite.
final class ConfidentialiteController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/confidentialite', name: 'app_confidentialite')]
    public function index(): Response
    {
        return $this->render('legales/confidentialite.html.twig', [
            'controller_name' => 'ConfidentialiteController',
        ]);
    }
}
