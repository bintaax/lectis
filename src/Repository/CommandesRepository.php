<?php

namespace App\Repository;

use App\Entity\Commandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commandes>
 */
class CommandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandes::class);
    }
public function searchAdmin(?string $statut, ?string $q): array
{
    $qb = $this->createQueryBuilder('c')
        ->leftJoin('c.utilisateurs', 'u')
        ->addSelect('u')
        ->orderBy('c.createdAt', 'DESC');

    if ($statut) {
        $qb->andWhere('c.statut = :statut')
           ->setParameter('statut', $statut); // Doctrine gère enumType
    }

    if ($q) {
        $qb->andWhere('c.numeroCommande LIKE :q OR u.email LIKE :q')
           ->setParameter('q', '%'.$q.'%');
    }

    return $qb->getQuery()->getResult();
}


//    /**
//     * @return Commandes[] Returns an array of Commandes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commandes
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
