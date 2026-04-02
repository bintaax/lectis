<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\LigneCommande;
use App\Enum\Statut;
use App\Form\CommandeType;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Contrôleur pour commandes.
class CommandesController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
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

        // 🔥 CALCUL DU TOTAL AVEC REMISE -10% SI ADHÉRENT
        $total = 0;
        foreach ($panier->getLignePaniers() as $ligne) {
            $livre = $ligne->getLivre();
            // Utilise le prix réduit si l'user est adhérent
            $prixUnitaire = ($user->isAdherent()) ? $livre->getPrixFidelite() : $livre->getPrix();
            $total += $prixUnitaire * $ligne->getQuantite();
        }

        $form = $this->createForm(CommandeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute('app_commande_valider', [
                'data' => json_encode([
                    'adresse' => $data['adresse'],
                    'codePostal' => $data['codePostal'],
                    'ville' => $data['ville'],
                    'paiement' => $data['paiement'],
                ])
            ]);
        }

        return $this->render('commande/index.html.twig', [
            'lignes' => $panier->getLignePaniers(),
            'total' => $total, // Ce total est maintenant le bon !
            'form' => $form->createView()
        ]);
    }

    // Charge les données nécessaires et rend la vue.
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

        $commande = new Commandes();
        $commande->setUtilisateurs($user);
        $commande->setStatut(Statut::EN_ATTENTE);
        $commande->setPaiement($data['paiement']);
        $commande->setAdresseLivraison(
            $data['adresse'] . ' ' . $data['codePostal'] . ' ' . $data['ville']
        );

        $totalFinal = 0;
        $em->persist($commande);

        foreach ($panier->getLignePaniers() as $lignePanier) {
            $livre = $lignePanier->getLivre();
            
            // 🔥 APPLICATION DE LA REMISE LORS DE LA CRÉATION DE LA LIGNE DE COMMANDE
            $prixApplique = ($user->isAdherent()) ? $livre->getPrixFidelite() : $livre->getPrix();

            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setLivre($livre);
            $ligneCommande->setQuantite($lignePanier->getQuantite());
            $ligneCommande->setPrixUnitaire($prixApplique); // On enregistre le prix payé réellement

            $totalFinal += $prixApplique * $lignePanier->getQuantite();

            $em->persist($ligneCommande);
        }

        $commande->setTotal($totalFinal);

        foreach ($panier->getLignePaniers() as $lp) {
            $em->remove($lp);
        }

        $em->flush();

        return $this->redirectToRoute('app_confirmation', [
            'id' => $commande->getId()
        ]);
    }
}