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

    //    /**
    //     * @return Livres[] Returns an array of Livres objects
    //     */
    public function findBestSellers(): array
{
    return $this->createQueryBuilder('l')
        ->andWhere('l.isBestSeller = :val')
        ->setParameter('val', true)
        ->getQuery()
        ->getResult();
}
public function findByGenre($genreId): array
{
    return $this->createQueryBuilder('l')
        ->andWhere('l.genre = :genre')
        ->setParameter('genre', $genreId)
        ->getQuery()
        ->getResult();
}


}
