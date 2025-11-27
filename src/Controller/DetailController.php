<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DetailController extends AbstractController
{
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
        return $this->render('detail/index.html.twig', [
            'livre' => $livre,
        ]);
    }
}
