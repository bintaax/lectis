<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FideliteController extends AbstractController
{
    #[Route('/fidelite', name: 'app_fidelite')]
    public function index(): Response
    {
        return $this->render('fidelite/index.html.twig', [
            'controller_name' => 'FideliteController',
        ]);
    }
}
