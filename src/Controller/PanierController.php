<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\LignePanier;
use App\Repository\LivresRepository;
use App\Repository\PanierRepository;
use App\Repository\LignePanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierRepository $panierRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Pas connecté → panier vide
            return $this->render('panier/index.html.twig', [
                'panier' => null,
                'lignes' => [],
                'total' => 0,
            ]);
        }

        $panier = $panierRepo->findOneBy(['utilisateur' => $user]);

        if (!$panier) {
            return $this->render('panier/index.html.twig', [
                'panier' => null,
                'lignes' => [],
                'total' => 0,
            ]);
        }

        $lignes = $panier->getLignePaniers();
        $total = 0;

        foreach ($lignes as $ligne) {
            $total += (float) $ligne->getLivre()->getPrix() * $ligne->getQuantite();
        }
        
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'lignes' => $lignes,
            'total' => $total,
        ]);
    }

    #[Route('/api/panier/add/{id}', methods: ['POST'], name: 'api_panier_add')]
    public function add(
        int $id,
        LivresRepository $livresRepo,
        PanierRepository $panierRepo,
        LignePanierRepository $ligneRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            // Pas connecté → on renvoie une erreur claire
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour ajouter au panier.'
            ], 401);
        }

        // On récupère ou crée le panier de l'utilisateur
        $panier = $panierRepo->findOneBy(['utilisateur' => $user]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setUtilisateur($user);
            $em->persist($panier);
            $em->flush();
        }

        // On récupère le livre
        $livre = $livresRepo->find($id);
        if (!$livre) {
            return $this->json([
                'success' => false,
                'message' => 'Livre introuvable.'
            ], 404);
        }

        // On cherche une ligne existante
        $ligne = $ligneRepo->findOneBy([
            'panier' => $panier,
            'livre' => $livre,
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

        // On recalcule le nombre total d'articles dans le panier
        $count = 0;
        foreach ($panier->getLignePaniers() as $l) {
            $count += $l->getQuantite();
        }

        return $this->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    #[Route('/api/panier/update/{id}', methods: ['POST'], name: 'api_panier_update')]
    public function update(
        int $id,
        \Symfony\Component\HttpFoundation\Request $request,
        LignePanierRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $ligne = $repo->find($id);
        if (!$ligne) {
            return $this->json(['success' => false, 'message' => 'Ligne introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $qtt = (int)($data['quantite'] ?? 1);

        if ($qtt <= 0) {
            $em->remove($ligne);
        } else {
            $ligne->setQuantite($qtt);
        }

        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/api/panier/delete/{id}', methods: ['POST'], name: 'api_panier_delete')]
    public function delete(
        int $id,
        LignePanierRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $ligne = $repo->find($id);
        if ($ligne) {
            $em->remove($ligne);
            $em->flush();
        }

        return $this->json(['success' => true]);
    }
}
