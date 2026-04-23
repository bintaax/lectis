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

    // Construit la requete de base du catalogue avec filtre de genre optionnel.
    private function createCatalogueQueryBuilder(?int $genreId = null)
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.genre', 'g')
            ->addSelect('g');

        if ($genreId !== null) {
            $qb->andWhere('l.genre = :genre')
                ->setParameter('genre', $genreId);
        }

        return $qb;
    }

    // Retourne une page du catalogue avec le total complet.
    public function paginateCatalogue(int $page, int $perPage, ?int $genreId = null): array
    {
        $qb = $this->createCatalogueQueryBuilder($genreId)
            ->orderBy('l.titre', 'ASC');

        $total = (clone $qb)
            ->select('COUNT(l.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $items = $qb
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return [
            'items' => $items,
            'total' => (int) $total,
        ];
    }

    // Recherche dans le catalogue par titre, auteur ou editeur avec pagination.
    public function paginateSearchCatalogue(string $q, int $page, int $perPage, ?int $genreId = null): array
    {
        $qb = $this->createCatalogueQueryBuilder($genreId)
            ->andWhere('l.titre LIKE :q OR l.auteur LIKE :q OR l.editeur LIKE :q')
            ->setParameter('q', '%'.$q.'%')
            ->orderBy('l.titre', 'ASC');

        $total = (clone $qb)
            ->select('COUNT(l.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $items = $qb
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return [
            'items' => $items,
            'total' => (int) $total,
        ];
    }
}
