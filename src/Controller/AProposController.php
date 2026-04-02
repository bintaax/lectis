<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour a propos.
final class AProposController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/a/propos', name: 'app_a_propos')]
    public function index(): Response
    {
        return $this->render('pages/a_propos.html.twig', [
            'controller_name' => 'AProposController',
        ]);
    }
}
