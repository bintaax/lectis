<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour confirmation.
final class ConfirmationController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/confirmation', name: 'app_confirmation')]
    public function index(): Response
    {
        return $this->render('commande/confirmation.html.twig', [
            'controller_name' => 'ConfirmationController',
        ]);
    }
}
