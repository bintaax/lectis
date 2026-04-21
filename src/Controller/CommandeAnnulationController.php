<?php

namespace App\Controller;

use App\Entity\Commandes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Gere l'annulation d'une commande depuis l'espace client.
class CommandeAnnulationController extends AbstractController
{
    // Verifie les droits puis annule la commande si elle est encore eligible.
    #[Route('/commande/annuler/{id}', name: 'app_commande_annuler')]
    public function annuler(Commandes $commande, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Verifie que l'utilisateur courant est bien proprietaire de la commande.
        if ($commande->getUtilisateurs() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas annuler cette commande.");
        }

        // Applique la regle metier qui limite l'annulation dans le temps.
        if (!$commande->isAnnulable()) {
            $this->addFlash('error', "Vous ne pouvez plus annuler cette commande.");
            return $this->redirectToRoute('app_commande_detail', ['numeroCommande' => $commande->getNumeroCommande()]);
        }

        $commande->setStatut(\App\Enum\Statut::ANNULEE);

        $em->flush();

        $this->addFlash('success', "La commande a été annulée avec succès.");

        return $this->redirectToRoute('app_compte');
    }
}
