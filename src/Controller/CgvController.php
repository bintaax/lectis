<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page des conditions generales de vente.
final class CgvController extends AbstractController
{
    // Rend simplement le contenu CGV.
    #[Route('/cgv', name: 'app_cgv')]
    public function index(): Response
    {
        return $this->render('legales/cgv.html.twig', [
            'controller_name' => 'CgvController',
        ]);
    }
}
