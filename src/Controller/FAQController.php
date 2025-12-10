<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FAQController extends AbstractController
{
    #[Route('/f/a/q', name: 'app_f_a_q')]
    public function index(): Response
    {
        return $this->render('faq/index.html.twig', [
            'controller_name' => 'FAQController',
        ]);
    }
}
