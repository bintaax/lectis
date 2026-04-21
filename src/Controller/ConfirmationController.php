<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page de confirmation apres validation d'une commande.
final class ConfirmationController extends AbstractController
{
    // Charge la commande confirmee puis rend la page de confirmation.
    #[Route('/confirmation', name: 'app_confirmation')]
    public function index(): Response
    {
        return $this->render('commande/confirmation.html.twig', [
            'controller_name' => 'ConfirmationController',
        ]);
    }
}
