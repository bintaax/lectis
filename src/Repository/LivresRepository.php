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
    // Relie ce repository a l'entite Livres pour executer les requetes Doctrine.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }

    // Recupere uniquement les livres marques comme best-sellers.
    public function findBestSellers(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.isBestSeller = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult();
    }

    // Recupere les livres associes a un genre donne.
    public function findByGenre($genreId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.genre = :genre')
            ->setParameter('genre', $genreId)
            ->getQuery()
            ->getResult();
    }

    // Recherche dans le catalogue par titre, auteur ou editeur.
    public function searchCatalogue(string $q): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.titre LIKE :q OR l.auteur LIKE :q OR l.editeur LIKE :q')
            ->setParameter('q', '%'.$q.'%')
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
