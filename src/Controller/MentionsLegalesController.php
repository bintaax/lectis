<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour mentions legales.
final class MentionsLegalesController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/mentions/legales', name: 'app_mentions_legales')]
    public function index(): Response
    {
        return $this->render('legales/mentions_legales.html.twig', [
            'controller_name' => 'MentionsLegalesController',
        ]);
    }
}
