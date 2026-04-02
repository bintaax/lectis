<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour retours.
final class RetoursController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/retours', name: 'app_retours')]
    public function index(): Response
    {
        return $this->render('legales/retours.html.twig', [
            'controller_name' => 'RetoursController',
        ]);
    }
}
