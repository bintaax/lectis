<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page d'information sur les cookies.
final class CookiesController extends AbstractController
{
    // Rend simplement la page legale dediee aux cookies.
    #[Route('/cookies', name: 'app_cookies')]
    public function index(): Response
    {
        return $this->render('legales/cookies.html.twig', [
            'controller_name' => 'CookiesController',
        ]);
    }
}
