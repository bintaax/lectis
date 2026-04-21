<?php

namespace App\Controller;

use App\Repository\CommandesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Affiche le detail d'une commande a partir de son numero lisible.
final class CommandeDetailController extends AbstractController
{
#[Route('/commande/{numeroCommande}', name: 'app_commande_detail')]
public function detail(string $numeroCommande, CommandesRepository $repo): Response
{
    // Recherche la commande via son numero metier plutot que son identifiant technique.
    $commande = $repo->findOneBy(['numeroCommande' => $numeroCommande]);

    if (!$commande) {
        throw $this->createNotFoundException("Commande introuvable.");
    }

    // Refuse l'acces si la commande n'appartient pas a l'utilisateur connecte.
    if ($commande->getUtilisateurs() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'avez pas accès à cette commande.");
    }

    return $this->render('commande/detail.html.twig', [
        'commande' => $commande,
    ]);
}
}
