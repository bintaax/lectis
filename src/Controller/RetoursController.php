<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RetoursController extends AbstractController
{
    #[Route('/retours', name: 'app_retours')]
    public function index(): Response
    {
        return $this->render('retours/index.html.twig', [
            'controller_name' => 'RetoursController',
        ]);
    }
}
