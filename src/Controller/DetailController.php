<?php

namespace App\Controller;

use App\DataFixtures\LivreFixtures;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


// Contrôleur pour detail.
final class DetailController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/livre/{slug}', name: 'app_detail')]
    public function index(string $slug, LivresRepository $livresRepo): Response
    {
         // On cherche le livre
        $livre = $livresRepo->findOneBy(['slug' => $slug ]);

    

        // Si le livre n'existe pas → erreur 404
        if (!$livre) {
            throw $this->createNotFoundException("Livre introuvable");
        }

        // On envoie le livre à la vue
        return $this->render('livres/detail.html.twig', [
            'livre' => $livre,
        ]);
    }
}
