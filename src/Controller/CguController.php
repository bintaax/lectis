<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la page des conditions generales d'utilisation.
final class CguController extends AbstractController
{
    // Rend simplement le contenu CGU.
    #[Route('/cgu', name: 'app_cgu')]
    public function index(): Response
    {
        return $this->render('legales/cgu.html.twig', [
            'controller_name' => 'CguController',
        ]);
    }
}
