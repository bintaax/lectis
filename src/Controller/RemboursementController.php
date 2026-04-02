<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour remboursement.
final class RemboursementController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/remboursement', name: 'app_remboursement')]
    public function index(): Response
    {
        return $this->render('legales/remboursement.html.twig', [
            'controller_name' => 'RemboursementController',
        ]);
    }
}
