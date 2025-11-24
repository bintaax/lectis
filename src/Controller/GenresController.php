<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GenresController extends AbstractController
{
    #[Route('/genres', name: 'app_genres')]
    public function index(): Response
    {
        return $this->render('genres/index.html.twig', [
            'controller_name' => 'GenresController',
        ]);
    }
}
