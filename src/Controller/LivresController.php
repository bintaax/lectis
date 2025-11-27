<?php

namespace App\Controller;

use App\Repository\GenresRepository;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class LivresController extends AbstractController
{
    #[Route('/livres', name: 'app_livres')]
    public function index(LivresRepository $livresRepo, GenresRepository $genreRepo): Response
    {
            // On récupère tous les genres
    $genres = $genreRepo->findAll();

    // Tableau final pour Twig
    $livresParGenre = [];

    foreach ($genres as $genre) {
        $livresParGenre[$genre->getNom()] = $livresRepo->findByGenre($genre->getId());
    }

    return $this->render('livres/index.html.twig', [
        'livresParGenre' => $livresParGenre,
    ]);

    }
}
