<?php

namespace App\Controller;

use App\Entity\Commandes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CommandeDetailController extends AbstractController
{
    #[Route('/commande/{id}', name: 'app_commande_detail')]
    public function detail(Commandes $commande): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Sécurité : vérifier que la commande appartient bien à cet utilisateur
        if ($commande->getUtilisateurs() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas voir cette commande.");
        }

        // Suivi fictif (à adapter si tu veux le stocker en BDD)
        $trackingSteps = [
            'Commande validée',
            'Préparation en cours',
            'Colis expédié',
            'En cours de livraison',
            'Livré 📦'
        ];

        // Exemple : déduction automatique du statut sur base du createdAt
       /*  $currentIndex = min(
            floor((time() - $commande->getCreatedAt()->getTimestamp()) / 3600), // 1 étape / heure
            count($trackingSteps) - 1
        ); */

        return $this->render('commande_detail/index.html.twig', [
            'commande' => $commande,
            'trackingSteps' => $trackingSteps,
           /*  'currentIndex' => $currentIndex */
        ]);
    }
}

