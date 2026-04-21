<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page de presentation de la librairie.
final class AProposController extends AbstractController
{
    // Rend simplement la page statique "A propos".
    #[Route('/a/propos', name: 'app_a_propos')]
    public function index(): Response
    {
        return $this->render('pages/a_propos.html.twig', [
            'controller_name' => 'AProposController',
        ]);
    }
}
