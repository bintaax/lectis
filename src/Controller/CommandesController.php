<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\LigneCommande;
use App\Enum\Statut;
use App\Form\CommandeType;
use App\Repository\LignePanierRepository;
use App\Repository\LivresRepository;
use App\Repository\PanierRepository;
use App\Service\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

// Gere le tunnel de commande depuis le panier jusqu'a la creation de la commande.
class CommandesController extends AbstractController
{
    use TargetPathTrait;

    // Affiche l'etape de saisie commande ou redirige si le panier est vide.
    #[Route('/commande', name: 'app_commandes')]
    public function commande(
        PanierRepository $panierRepository,
        LignePanierRepository $lignePanierRepository,
        LivresRepository $livresRepository,
        EntityManagerInterface $em,
        CartManager $cartManager,
        Request $request
    ): Response {
        $user = $this->getUser();
        $session = $request->getSession();

        if (!$user) {
            if ($cartManager->getGuestCount($session) <= 0) {
                $this->addFlash('error', 'Votre panier est vide.');
                return $this->redirectToRoute('app_panier');
            }

            $redirectPath = $this->generateUrl('app_commandes');
            $this->saveTargetPath($session, 'main', $redirectPath);

            return $this->render('commande/access_prompt.html.twig', [
                'redirect_path' => $redirectPath,
            ]);
        }

        $cartManager->mergeGuestCartIntoUser($session, $user, $panierRepository, $lignePanierRepository, $livresRepository, $em);

        $panier = $panierRepository->findOneBy(['utilisateur' => $user]);

        if (!$panier || $panier->getLignePaniers()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier');
        }

        // Recalcule le total en appliquant le tarif Lectis+ si l'utilisateur est adherent.
        $total = 0;
        foreach ($panier->getLignePaniers() as $ligne) {
            $livre = $ligne->getLivre();
            // Choisit le bon prix unitaire selon le statut d'adhesion.
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
            'total' => $total, 
            'form' => $form->createView()
        ]);
    }

    // Transforme le panier courant en commande persistée puis vide le panier.
    #[Route('/commande/valider/{data}', name: 'app_commande_valider')]
    public function valider(
        string $data,
        PanierRepository $panierRepository,
        LignePanierRepository $lignePanierRepository,
        LivresRepository $livresRepository,
        CartManager $cartManager,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $cartManager->mergeGuestCartIntoUser($request->getSession(), $user, $panierRepository, $lignePanierRepository, $livresRepository, $em);

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
            
            // Conserve le prix reellement facture au moment de la validation.
            $prixApplique = ($user->isAdherent()) ? $livre->getPrixFidelite() : $livre->getPrix();

            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setLivre($livre);
            $ligneCommande->setQuantite($lignePanier->getQuantite());
            $ligneCommande->setPrixUnitaire($prixApplique); // Stocke le prix facture pour garder un historique fiable.

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
