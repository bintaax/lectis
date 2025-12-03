<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\LigneCommande;
use App\Enum\Statut;
use App\Enum\Paiement;
use App\Form\PaiementType;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandesController extends AbstractController
{
#[Route('/commande', name: 'app_commandes')]
public function commande(
    PanierRepository $panierRepository,
    Request $request
): Response {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();

    $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

    if (!$panier || $panier->getLignePaniers()->isEmpty()) {
        $this->addFlash('error', 'Votre panier est vide.');
        return $this->redirectToRoute('app_panier');
    }

    $lignes = $panier->getLignePaniers();

    // 🔥 Calcule le total
    $total = 0;
    foreach ($lignes as $ligne) {
        $total += $ligne->getLivre()->getPrix() * $ligne->getQuantite();
    }

    $form = $form = $this->createForm(PaiementType::class);

    return $this->render('commandes/index.html.twig', [
        'lignes' => $lignes,
        'form' => $form->createView(),
        'total' => $total, // 👉 essentiel
    ]);
}
#[Route('/commande/valider/{paiement}', name: 'app_commande_valider')]
public function valider(
    string $paiement,
    PanierRepository $panierRepository,
    EntityManagerInterface $em
): Response {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();

    $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

    if (!$panier || $panier->getLignePaniers()->isEmpty()) {
        return $this->redirectToRoute('app_panier');
    }

    // Création commande
    $commande = new Commandes();
    $commande->setUtilisateurs($user);
    $commande->setStatut(Statut::EN_ATTENTE);
    $commande->setPaiement(PAIEMENT::CB);
    $commande->setAdresseLivraison('Non définie');
    $commande->setTotal(0);

    $em->persist($commande);

    $total = 0;

    // COPY lignes panier → lignes commande
    foreach ($panier->getLignePaniers() as $lignePanier) {

        $ligneCommande = new LigneCommande();
        $ligneCommande->setCommande($commande);
        $ligneCommande->setLivre($lignePanier->getLivre());
        $ligneCommande->setQuantite($lignePanier->getQuantite());

        $prixUnitaire = $lignePanier->getLivre()->getPrix();
        $ligneCommande->setPrixUnitaire($prixUnitaire);

        $total += $prixUnitaire * $lignePanier->getQuantite();

        $em->persist($ligneCommande);
    }

    $commande->setTotal($total);

    // Purge du panier
    foreach ($panier->getLignePaniers() as $lp) {
        $em->remove($lp);
    }

    $em->flush();

    return $this->redirectToRoute('app_confirmation', [
        'id' => $commande->getId()
    ]);
}


}
