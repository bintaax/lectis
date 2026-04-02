<?php

namespace App\Controller;

use App\Repository\CommandesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Contrôleur pour commande detail.
final class CommandeDetailController extends AbstractController
{
// App\Controller\CommandeDetailController.php

#[Route('/commande/{numeroCommande}', name: 'app_commande_detail')]
public function detail(string $numeroCommande, CommandesRepository $repo): Response
{
    // 1. On change 'int' en 'string' pour accepter le format du numéro
    // 2. On utilise findOneBy() pour chercher dans la colonne 'numeroCommande'
    $commande = $repo->findOneBy(['numeroCommande' => $numeroCommande]);

    if (!$commande) {
        throw $this->createNotFoundException("Commande introuvable.");
    }

    // Sécurité : Vérifier que la commande appartient bien à l'utilisateur connecté
    if ($commande->getUtilisateurs() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'avez pas accès à cette commande.");
    }

    return $this->render('commande/detail.html.twig', [
        'commande' => $commande,
    ]);
}
}

