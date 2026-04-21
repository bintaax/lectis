<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\LignePanier;
use App\Repository\LivresRepository;
use App\Repository\PanierRepository;
use App\Repository\LignePanierRepository;
use App\Service\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Gere l'affichage du panier et les operations AJAX d'ajout, mise a jour et suppression.
final class PanierController extends AbstractController
{
    // Construit les donnees du panier pour un utilisateur connecte ou invite.
    #[Route('/panier', name: 'app_panier', methods: ['GET'])]
    public function index(
        PanierRepository $panierRepo,
        LivresRepository $livresRepo,
        LignePanierRepository $ligneRepo,
        EntityManagerInterface $em,
        CartManager $cartManager,
        Request $request
    ): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();

        // Initialise les variables envoyees a Twig avant de charger le panier reel.
        $panier = null;
        $lignes = [];
        $total = 0;

        if ($user) {
            $cartManager->mergeGuestCartIntoUser($session, $user, $panierRepo, $ligneRepo, $livresRepo, $em);
            $panier = $panierRepo->findOneBy(['utilisateur' => $user]);

            if ($panier) {
                $lignes = $panier->getLignePaniers();

                foreach ($lignes as $ligne) {
                    $livre = $ligne->getLivre();

                    // Applique le tarif adherent quand l'utilisateur beneficie de Lectis+.
                    $prixApplique = ($user->isAdherent())
                        ? $livre->getPrixFidelite()
                        : $livre->getPrix();

                    $total += (float) $prixApplique * $ligne->getQuantite();
                }
            }
        } else {
            $guestCart = $cartManager->getGuestCartViewData($session, $livresRepo);
            $lignes = $guestCart['lignes'];
            $total = $guestCart['total'];
        }

        $vars = [
            'panier' => $panier,
            'lignes' => $lignes,
            'total'  => $total,
        ];

        // Renvoie seulement le fragment HTML quand le panier est rafraichi en AJAX.
        if ($request->isXmlHttpRequest()) {
            return $this->render('panier/_panier_container.html.twig', $vars);
        }

        // Renvoie la page complete lors d'un affichage classique.
        return $this->render('panier/index.html.twig', $vars);
    }

    // Ajoute un livre au panier de l'utilisateur ou au panier invite en session.
    #[Route('/api/panier/add/{id}', methods: ['POST'], name: 'api_panier_add')]
    public function add(
        int $id,
        LivresRepository $livresRepo,
        PanierRepository $panierRepo,
        LignePanierRepository $ligneRepo,
        EntityManagerInterface $em,
        Request $request,
        CartManager $cartManager
    ): Response {
        $user = $this->getUser();
        $livre = $livresRepo->find($id);

        if (!$livre) {
            return $this->json([
                'success' => false,
                'message' => 'Livre introuvable.'
            ], 404);
        }

        if (!$user) {
            $count = $cartManager->addGuestItem($request->getSession(), $id);

            return $this->json([
                'success' => true,
                'count'   => $count,
            ]);
        }

        // Recupere le panier existant ou en cree un nouveau pour l'utilisateur.
        $panier = $panierRepo->findOneBy(['utilisateur' => $user]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setUtilisateur($user);
            $em->persist($panier);
            $em->flush();
        }

        // Verifie si le livre est deja present dans une ligne du panier.
        $ligne = $ligneRepo->findOneBy([
            'panier' => $panier,
            'livre'  => $livre,
        ]);

        if (!$ligne) {
            $ligne = new LignePanier();
            $ligne->setPanier($panier);
            $ligne->setLivre($livre);
            $ligne->setQuantite(1);
            $em->persist($ligne);
        } else {
            $ligne->setQuantite($ligne->getQuantite() + 1);
        }

        $em->flush();

        // Recalcule le nombre total d'articles pour remettre a jour le badge.
        $count = 0;
        foreach ($panier->getLignePaniers() as $l) {
            $count += $l->getQuantite();
        }

        return $this->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    // Met a jour la quantite d'une ligne du panier puis renvoie le nouveau compteur.
    #[Route('/api/panier/update/{id}', methods: ['POST'], name: 'api_panier_update')]
    public function update(
        int $id,
        Request $request,
        LignePanierRepository $repo,
        EntityManagerInterface $em,
        CartManager $cartManager
    ): Response {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $qtt = (int)($data['quantite'] ?? 1);

        if (!$user) {
            $count = $cartManager->updateGuestItem($request->getSession(), $id, $qtt);

            return $this->json([
                'success' => true,
                'count' => $count,
            ]);
        }

        $ligne = $repo->find($id);
        if (!$ligne) {
            return $this->json(['success' => false, 'message' => 'Ligne introuvable'], 404);
        }

        // Bloque toute modification d'une ligne qui n'appartient pas au panier de l'utilisateur courant.
        if ($ligne->getPanier()?->getUtilisateur()?->getId() !== $user->getId()) {
            return $this->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        if ($qtt <= 0) {
            $em->remove($ligne);
        } else {
            $ligne->setQuantite($qtt);
        }

        $em->flush();

        // Recalcule le nombre total d'articles apres la mise a jour.
        $panier = $ligne->getPanier();
        $count = 0;
        if ($panier) {
            foreach ($panier->getLignePaniers() as $l) {
                $count += $l->getQuantite();
            }
        }

        return $this->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    // Supprime une ligne du panier puis renvoie le nouveau compteur.
    #[Route('/api/panier/delete/{id}', methods: ['POST'], name: 'api_panier_delete')]
    public function delete(
        int $id,
        LignePanierRepository $repo,
        EntityManagerInterface $em,
        Request $request,
        CartManager $cartManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            $count = $cartManager->deleteGuestItem($request->getSession(), $id);

            return $this->json([
                'success' => true,
                'count' => $count,
            ]);
        }

        $ligne = $repo->find($id);
        if (!$ligne) {
            return $this->json(['success' => true, 'count' => 0]); // La ligne a deja disparu, on renvoie simplement un panier vide.
        }

        // Verifie que l'utilisateur essaie bien de supprimer une ligne de son propre panier.
        if ($ligne->getPanier()?->getUtilisateur()?->getId() !== $user->getId()) {
            return $this->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $panier = $ligne->getPanier();

        $em->remove($ligne);
        $em->flush();

        // Recalcule le nombre total d'articles apres la suppression.
        $count = 0;
        if ($panier) {
            foreach ($panier->getLignePaniers() as $l) {
                $count += $l->getQuantite();
            }
        }

        return $this->json([
            'success' => true,
            'count'   => $count,
        ]);
    }
}
