<?php

namespace App\Controller;

use App\Entity\Commandes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Contrôleur pour commande annulation.
class CommandeAnnulationController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
    #[Route('/commande/annuler/{id}', name: 'app_commande_annuler')]
    public function annuler(Commandes $commande, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Sécurité : la commande doit appartenir au user
        if ($commande->getUtilisateurs() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas annuler cette commande.");
        }

        // Vérifier si la commande est encore annulable
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
