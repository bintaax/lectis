<?php

namespace App\Controller;

use App\Repository\GenresRepository;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivresController extends AbstractController
{
    #[Route('/livres', name: 'app_livres')]
    public function index(
        Request $request,
        LivresRepository $livresRepo,
        GenresRepository $genreRepo
    ): Response {
        $q = trim((string) $request->query->get('q', ''));

        // ✅ Si recherche : on renvoie une liste de résultats
        $resultats = [];
        if ($q !== '') {
            $resultats = $livresRepo->searchCatalogue($q);
        }

        // ✅ On garde ta structure "par genre" (utile si pas de recherche)
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
    