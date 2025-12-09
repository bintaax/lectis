<?php

namespace App\Controller;

use App\Entity\Commandes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeAnnulationController extends AbstractController
{
    #[Route('/commande/annuler/{id}', name: 'app_commande_annuler')]
    public function annuler(Commandes $commande, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Sécurité : la commande doit appartenir au user
        if ($commande->getUtilisateurs() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas annuler cette commande.");
        }

        // Vérifier si la commande est encore annulable
        $today = time();
        $created = $commande->getCreatedAt()->getTimestamp();
        $diffDays = floor(($today - $created) / 86400);

        if ($diffDays >= 3) {
            $this->addFlash('error', "Vous ne pouvez plus annuler cette commande.");
            return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
        }

        // Annuler la commande (choisis le statut que tu veux)
        $commande->setStatut(\App\Enum\Statut::ANNULEE ?? null); // si tu veux garder un enum
        // ou : $commande->setStatut(null);

        $em->flush();

        $this->addFlash('success', "La commande a été annulée avec succès.");

        return $this->redirectToRoute('app_compte');
    }
}
