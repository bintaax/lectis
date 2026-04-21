<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Affiche la foire aux questions du site.
final class FAQController extends AbstractController
{
    // Rend simplement la page FAQ.
    #[Route('/f/a/q', name: 'app_f_a_q')]
    public function index(): Response
    {
        return $this->render('pages/faq.html.twig', [
            'controller_name' => 'FAQController',
        ]);
    }
}
