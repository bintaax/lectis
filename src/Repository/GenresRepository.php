<?php

namespace App\Repository;

use App\Entity\Genres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Genres>
 */
class GenresRepository extends ServiceEntityRepository
{
    // Relie ce repository a l'entite Genres pour les operations Doctrine.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genres::class);
    }
}
