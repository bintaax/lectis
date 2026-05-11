<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Gere la page d'accueil et ses selections de livres.
final class HomeController extends AbstractController
{
    // Charge les livres a mettre en avant sur la page d'accueil.
    #[Route('/', name: 'app_home')]
    #[Route('/accueil', name: 'app_home_legacy')]
    public function index(LivresRepository $repo): Response
{
    $bestSellers = $repo->findBestSellers(); // ta méthode personnalisée

    return $this->render('home/index.html.twig', [
        'bestSellers' => $bestSellers
    ]);
}
}
