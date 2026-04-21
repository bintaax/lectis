<?php

namespace App\Controller;

use App\DataFixtures\LivreFixtures;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


// Gere la fiche detail d'un livre a partir de son slug.
final class DetailController extends AbstractController
{
    // Charge le livre demande puis affiche sa fiche detail.
    #[Route('/livre/{slug}', name: 'app_detail')]
    public function index(string $slug, LivresRepository $livresRepo): Response
    {
         // Recherche le livre correspondant au slug present dans l'URL.
        $livre = $livresRepo->findOneBy(['slug' => $slug ]);

    

        // Retourne une 404 si aucun livre ne correspond au slug demande.
        if (!$livre) {
            throw $this->createNotFoundException("Livre introuvable");
        }

        // Envoie les donnees du livre a la vue Twig.
        return $this->render('livres/detail.html.twig', [
            'livre' => $livre,
        ]);
    }
}
