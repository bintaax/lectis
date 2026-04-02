<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour cgv.
final class CgvController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/cgv', name: 'app_cgv')]
    public function index(): Response
    {
        return $this->render('legales/cgv.html.twig', [
            'controller_name' => 'CgvController',
        ]);
    }
}
