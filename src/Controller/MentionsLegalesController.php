<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page des mentions legales.
final class MentionsLegalesController extends AbstractController
{
    // Rend simplement le contenu legal correspondant.
    #[Route('/mentions/legales', name: 'app_mentions_legales')]
    public function index(): Response
    {
        return $this->render('legales/mentions_legales.html.twig', [
            'controller_name' => 'MentionsLegalesController',
        ]);
    }
}
