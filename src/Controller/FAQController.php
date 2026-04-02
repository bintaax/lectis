<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour faq.
final class FAQController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/f/a/q', name: 'app_f_a_q')]
    public function index(): Response
    {
        return $this->render('pages/faq.html.twig', [
            'controller_name' => 'FAQController',
        ]);
    }
}
