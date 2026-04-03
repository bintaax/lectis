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

// Contrôleur pour panier.
final class PanierController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
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

        // Variables par défaut pour Twig
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

                    // -10% pour adhérents (selon ton code)
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

        // ✅ Si requête AJAX => on renvoie uniquement le fragment
        if ($request->isXmlHttpRequest()) {
            return $this->render('panier/_panier_container.html.twig', $vars);
        }

        // ✅ Sinon page complète
        return $this->render('panier/index.html.twig', $vars);
    }

    // Charge les données nécessaires et rend la vue.
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

        // Récupérer ou créer le panier
        $panier = $panierRepo->findOneBy(['utilisateur' => $user]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setUtilisateur($user);
            $em->persist($panier);
            $em->flush();
        }

        // Chercher la ligne existante
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

        // Recalcul du count total (quantités)
        $count = 0;
        foreach ($panier->getLignePaniers() as $l) {
            $count += $l->getQuantite();
        }

        return $this->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    // Charge les données nécessaires et rend la vue.
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

        // ✅ Sécurité : la ligne doit appartenir au panier du user
        if ($ligne->getPanier()?->getUtilisateur()?->getId() !== $user->getId()) {
            return $this->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        if ($qtt <= 0) {
            $em->remove($ligne);
        } else {
            $ligne->setQuantite($qtt);
        }

        $em->flush();

        // Recalcul du count total (quantités)
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

    // Charge les données nécessaires et rend la vue.
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
            return $this->json(['success' => true, 'count' => 0]); // ligne déjà absente
        }

        // ✅ Sécurité : la ligne doit appartenir au panier du user
        if ($ligne->getPanier()?->getUtilisateur()?->getId() !== $user->getId()) {
            return $this->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $panier = $ligne->getPanier();

        $em->remove($ligne);
        $em->flush();

        // Recalcul du count total (quantités)
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
