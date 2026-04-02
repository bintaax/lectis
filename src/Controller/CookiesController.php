<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour cookies.
final class CookiesController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/cookies', name: 'app_cookies')]
    public function index(): Response
    {
        return $this->render('legales/cookies.html.twig', [
            'controller_name' => 'CookiesController',
        ]);
    }
}
