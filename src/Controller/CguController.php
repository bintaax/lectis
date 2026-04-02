<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour cgu.
final class CguController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/cgu', name: 'app_cgu')]
    public function index(): Response
    {
        return $this->render('legales/cgu.html.twig', [
            'controller_name' => 'CguController',
        ]);
    }
}
