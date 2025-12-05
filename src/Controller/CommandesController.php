<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\LigneCommande;
use App\Enum\Statut;
use App\Enum\Paiement;
use App\Form\CommandeType;
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
    ){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

        if (!$panier || $panier->getLignePaniers()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier');
        }

        // 🔥 Calcule le total
        $total = 0;
        foreach ($panier->getLignePaniers() as $ligne) {
            $total += $ligne->getLivre()->getPrix() * $ligne->getQuantite();
        }

        // Formulaire global
        $form = $this->createForm(CommandeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

return $this->redirectToRoute('app_commande_valider', [
    'data' => json_encode([
        'adresse' => $data['adresse'],
        'codePostal' => $data['codePostal'],
        'ville' => $data['ville'],
        'paiement' => $data['paiement']->value, // valeur string pour passer dans l'URL
    ])
]);


        return $this->render('commandes/index.html.twig', [
            'lignes' => $panier->getLignePaniers(),
            'total' => $total,
            'form' => $form->createView()
        ]);
    }
}
    #[Route('/commande/valider/{data}', name: 'app_commande_valider')]
    public function valider(
        string $data,
        PanierRepository $panierRepository,
        EntityManagerInterface $em
    ): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);
        if (!$panier) return $this->redirectToRoute('app_panier');

        $data = json_decode($data, true);

        // 📦 Créer la commande
        $commande = new Commandes();
        $commande->setUtilisateurs($user);
        $commande->setStatut(Statut::EN_ATTENTE);
        $commande->setPaiement(Paiement::from($data['paiement']));
        $commande->setAdresseLivraison(
            $data['adresse'] . ' ' . $data['codePostal'] . ' ' . $data['ville']
        );

        $total = 0;
        $em->persist($commande);

        // 🔁 Copier les lignes du panier
        foreach ($panier->getLignePaniers() as $lignePanier) {

            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setLivre($lignePanier->getLivre());
            $ligneCommande->setQuantite($lignePanier->getQuantite());
            $ligneCommande->setPrixUnitaire($lignePanier->getLivre()->getPrix());

            $total += $lignePanier->getLivre()->getPrix() * $lignePanier->getQuantite();

            $em->persist($ligneCommande);
        }

        // 💶 Enregistrer le total final
        $commande->setTotal($total);

        // 🧹 Vider le panier
        foreach ($panier->getLignePaniers() as $lp) {
            $em->remove($lp);
        }

        $em->flush();

        return $this->redirectToRoute('app_confirmation', [
            'id' => $commande->getId()
        ]);
    }
}
