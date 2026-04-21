<?php

namespace App\Repository;

use App\Entity\Panier;
use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Panier>
 */
class PanierRepository extends ServiceEntityRepository
{
    // Relie ce repository a l'entite Panier pour les operations Doctrine.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    // Retourne le panier de l'utilisateur ou en cree un si aucun panier n'existe encore.
    public function findOrCreateByUser($user, \Doctrine\ORM\EntityManagerInterface $em): Panier
    {
        // Cherche d'abord un panier deja rattache a l'utilisateur.
        $panier = $this->findOneBy(['utilisateur' => $user]);

        // Cree un nouveau panier seulement si aucun resultat n'a ete trouve.
        if (!$panier) {
            $panier = new Panier();
            $panier->setUtilisateur($user);
            $panier->setCreatedAt(new \DateTime());

            $em->persist($panier);
            $em->flush();
        }

        return $panier;
    }

    // Calcule le total des quantites pour alimenter le badge du panier.
    public function countByUser($user): int
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.quantite)')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
