<?php
namespace App\Enum;

enum Statut: string
{
    case EN_ATTENTE = 'en_attente';
    case PASSEE = 'passee';
    case PREPARATION = 'preparation';
    case EXPEDIEE = 'expediee';
    case LIVRAISON = 'livraison';
    case LIVREE = 'livree';
    case ANNULEE = 'annulee';


    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::PASSEE => 'Commande passée',
            self::PREPARATION => 'En préparation',
            self::EXPEDIEE => 'Colis expédié',
            self::LIVRAISON => 'En cours de livraison',
            self::LIVREE => 'Livrée',
            self::ANNULEE => 'Annulée'
        };
    }
}
