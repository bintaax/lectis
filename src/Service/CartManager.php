<?php

namespace App\Service;

use App\Entity\LignePanier;
use App\Entity\Utilisateurs;
use App\Repository\LignePanierRepository;
use App\Repository\LivresRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartManager
{
    private const GUEST_CART_KEY = 'guest_cart';
    private const GUEST_CART_COUNT_KEY = 'guest_cart_count';

    public function getGuestCart(SessionInterface $session): array
    {
        $cart = $session->get(self::GUEST_CART_KEY, []);

        if (!is_array($cart)) {
            $cart = [];
        }

        $normalizedCart = [];
        foreach ($cart as $bookId => $quantity) {
            $bookId = (int) $bookId;
            $quantity = (int) $quantity;

            if ($bookId > 0 && $quantity > 0) {
                $normalizedCart[$bookId] = $quantity;
            }
        }

        return $normalizedCart;
    }

    public function getGuestCount(SessionInterface $session): int
    {
        return (int) $session->get(self::GUEST_CART_COUNT_KEY, 0);
    }

    public function addGuestItem(SessionInterface $session, int $bookId): int
    {
        $cart = $this->getGuestCart($session);
        $cart[$bookId] = ($cart[$bookId] ?? 0) + 1;

        $this->persistGuestCart($session, $cart);

        return $this->getGuestCount($session);
    }

    public function updateGuestItem(SessionInterface $session, int $bookId, int $quantity): int
    {
        $cart = $this->getGuestCart($session);

        if ($quantity <= 0) {
            unset($cart[$bookId]);
        } else {
            $cart[$bookId] = $quantity;
        }

        $this->persistGuestCart($session, $cart);

        return $this->getGuestCount($session);
    }

    public function deleteGuestItem(SessionInterface $session, int $bookId): int
    {
        $cart = $this->getGuestCart($session);
        unset($cart[$bookId]);

        $this->persistGuestCart($session, $cart);

        return $this->getGuestCount($session);
    }

    public function getGuestCartViewData(SessionInterface $session, LivresRepository $livresRepository): array
    {
        $lines = [];
        $total = 0.0;

        foreach ($this->getGuestCart($session) as $bookId => $quantity) {
            $livre = $livresRepository->find($bookId);
            if (!$livre) {
                continue;
            }

            $lines[] = [
                'id' => $bookId,
                'quantite' => $quantity,
                'livre' => $livre,
            ];

            $total += (float) $livre->getPrix() * $quantity;
        }

        return [
            'lignes' => $lines,
            'total' => $total,
        ];
    }

    public function mergeGuestCartIntoUser(
        SessionInterface $session,
        Utilisateurs $user,
        PanierRepository $panierRepository,
        LignePanierRepository $lignePanierRepository,
        LivresRepository $livresRepository,
        EntityManagerInterface $entityManager
    ): void {
        $guestCart = $this->getGuestCart($session);
        if ($guestCart === []) {
            return;
        }

        $panier = $panierRepository->findOrCreateByUser($user, $entityManager);

        foreach ($guestCart as $bookId => $quantity) {
            $livre = $livresRepository->find($bookId);
            if (!$livre) {
                continue;
            }

            $ligne = $lignePanierRepository->findOneBy([
                'panier' => $panier,
                'livre' => $livre,
            ]);

            if (!$ligne) {
                $ligne = new LignePanier();
                $ligne->setPanier($panier);
                $ligne->setLivre($livre);
                $ligne->setQuantite($quantity);
                $entityManager->persist($ligne);
                continue;
            }

            $ligne->setQuantite($ligne->getQuantite() + $quantity);
        }

        $entityManager->flush();
        $this->clearGuestCart($session);
    }

    public function clearGuestCart(SessionInterface $session): void
    {
        $session->remove(self::GUEST_CART_KEY);
        $session->remove(self::GUEST_CART_COUNT_KEY);
    }

    private function persistGuestCart(SessionInterface $session, array $cart): void
    {
        $session->set(self::GUEST_CART_KEY, $cart);
        $session->set(self::GUEST_CART_COUNT_KEY, array_sum($cart));
    }
}
