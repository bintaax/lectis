<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(LivresRepository $repo): Response
{
    $bestSellers = $repo->findBestSellers(); // ta méthode personnalisée

    return $this->render('home/index.html.twig', [
        'bestSellers' => $bestSellers
    ]);
}
}
