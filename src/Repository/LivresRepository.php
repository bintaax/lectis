<?php

namespace App\Repository;

use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livres>
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }


    /* Fonction pour sélectionner UNIQUEMENT les best-sellers */
    public function findBestSellers(): array
{
    return $this->createQueryBuilder('l')
        ->andWhere('l.isBestSeller = :val')
        ->setParameter('val', true)
        ->getQuery()
        ->getResult();
}

/* Fonction pour sélectionner les livres PAR GENRE */
public function findByGenre($genreId): array
{
    return $this->createQueryBuilder('l')
        ->andWhere('l.genre = :genre')
        ->setParameter('genre', $genreId)
        ->getQuery()
        ->getResult();
}

}

