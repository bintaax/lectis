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
    // Relie ce repository a l'entite Commandes pour les operations Doctrine.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandes::class);
    }

    // Filtre les commandes admin par statut et par recherche sur le numero ou l'email client.
    public function searchAdmin(?string $statut, ?string $q): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.utilisateurs', 'u')
            ->addSelect('u')
            ->orderBy('c.createdAt', 'DESC');

        if ($statut) {
            $qb->andWhere('c.statut = :statut')
                ->setParameter('statut', $statut); // Doctrine convertit l'enum vers sa valeur en base.
        }

        if ($q) {
            $qb->andWhere('c.numeroCommande LIKE :q OR u.email LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
