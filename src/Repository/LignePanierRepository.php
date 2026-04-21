<?php

namespace App\Repository;

use App\Entity\LignePanier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LignePanier>
 */
class LignePanierRepository extends ServiceEntityRepository
{
    // Relie ce repository a l'entite LignePanier pour les operations Doctrine.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LignePanier::class);
    }
}
