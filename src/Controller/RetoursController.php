<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page de politique de retours.
final class RetoursController extends AbstractController
{
    // Rend simplement le contenu legal sur les retours.
    #[Route('/retours', name: 'app_retours')]
    public function index(): Response
    {
        return $this->render('legales/retours.html.twig', [
            'controller_name' => 'RetoursController',
        ]);
    }
}
