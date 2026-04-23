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
        $selectedGenreId = $request->query->getInt('genre') ?: null;
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 20;
        $genres = $genreRepo->findBy([], ['nom' => 'ASC']);

        // Lance une recherche textuelle quand un terme est saisi, avec pagination.
        if ($q !== '') {
            $pagination = $livresRepo->paginateSearchCatalogue($q, $page, $perPage, $selectedGenreId);
        } else {
            $pagination = $livresRepo->paginateCatalogue($page, $perPage, $selectedGenreId);
        }

        $totalLivres = $pagination['total'];
        $totalPages = max(1, (int) ceil($totalLivres / $perPage));
        $page = min($page, $totalPages);

        if ($page !== max(1, $request->query->getInt('page', 1))) {
            if ($q !== '') {
                $pagination = $livresRepo->paginateSearchCatalogue($q, $page, $perPage, $selectedGenreId);
            } else {
                $pagination = $livresRepo->paginateCatalogue($page, $perPage, $selectedGenreId);
            }
        }

        $baseQuery = array_filter([
            'q' => $q !== '' ? $q : null,
            'genre' => $selectedGenreId,
        ], static fn ($value) => $value !== null && $value !== '');

        return $this->render('livres/index.html.twig', [
            'genres' => $genres,
            'selectedGenreId' => $selectedGenreId,
            'q' => $q,
            'livres' => $pagination['items'],
            'totalLivres' => $totalLivres,
            'page' => $page,
            'totalPages' => $totalPages,
            'baseQuery' => $baseQuery,
        ]);
    }
}               
    
