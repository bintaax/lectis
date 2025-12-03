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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    public function findOrCreateByUser($user, \Doctrine\ORM\EntityManagerInterface $em): Panier
{
    // Chercher le panier de l'utilisateur
    $panier = $this->findOneBy(['utilisateur' => $user]);

    // Si pas trouvé → on le crée
    if (!$panier) {
        $panier = new Panier();
        $panier->setUtilisateur($user);
        $panier->setCreatedAt(new \DateTime());

        $em->persist($panier);
        $em->flush();
    }

    return $panier;
}

// Badge du panier
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
