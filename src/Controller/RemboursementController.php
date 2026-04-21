<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page de politique de remboursement.
final class RemboursementController extends AbstractController
{
    // Rend simplement le contenu legal sur les remboursements.
    #[Route('/remboursement', name: 'app_remboursement')]
    public function index(): Response
    {
        return $this->render('legales/remboursement.html.twig', [
            'controller_name' => 'RemboursementController',
        ]);
    }
}
