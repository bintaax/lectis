<?php

namespace App\Enum;

// Centralise les differents etats possibles d'une commande.
enum Statut: string
{
    case EN_ATTENTE = 'en_attente';
    case PASSEE = 'passee';
    case PREPARATION = 'preparation';
    case LIVRAISON = 'livraison';
    case LIVREE = 'livree';
    case ANNULEE = 'annulee';

    // Fournit un libelle lisible pour l'interface utilisateur.
    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::PASSEE => 'Commande passée',
            self::PREPARATION => 'En préparation',
            self::LIVRAISON => 'En cours de livraison',
            self::LIVREE => 'Livrée',
            self::ANNULEE => 'Annulée',
        };
    }

    // Retourne les classes CSS utilisees pour afficher le badge du statut.
    public function badgeClass(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'bg-gray-100 text-gray-700',
            self::PASSEE => 'bg-blue-100 text-blue-700',
            self::PREPARATION => 'bg-indigo-100 text-indigo-700',
            self::LIVRAISON => 'bg-yellow-100 text-yellow-800',
            self::LIVREE => 'bg-green-100 text-green-700',
            self::ANNULEE => 'bg-red-100 text-red-700',
        };
    }
}
