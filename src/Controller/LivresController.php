<?php

namespace App\Controller;

use App\Repository\GenresRepository;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Gere l'affichage du catalogue, par recherche et par genre.
final class LivresController extends AbstractController
{
    // Construit les donnees du catalogue et les resultats de recherche.
    #[Route('/livres', name: 'app_livres')]
    public function index(
        Request $request,
        LivresRepository $livresRepo,
        GenresRepository $genreRepo
    ): Response {
        $q = trim((string) $request->query->get('q', ''));

        // Lance une recherche textuelle quand un terme est saisi.
        $resultats = [];
        if ($q !== '') {
            $resultats = $livresRepo->searchCatalogue($q);
        }

        // Reconstruit en parallele l'affichage du catalogue groupe par genre.
        $genres = $genreRepo->findAll();
        $livresParGenre = [];

        foreach ($genres as $genre) {
            $livresParGenre[$genre->getNom()] = $livresRepo->findByGenre($genre->getId());
        }

        return $this->render('livres/index.html.twig', [
            'livresParGenre' => $livresParGenre,
            'q' => $q,
            'resultats' => $resultats,
        ]);
    }
}               
    
