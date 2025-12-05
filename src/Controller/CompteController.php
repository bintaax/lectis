<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
public function index(
    CommandesRepository $commandeRepository
): Response {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();

    // 🔥 Récupérer les commandes du user
    $commandes = $commandeRepository->findBy(
        ['utilisateurs' => $user],
       
    );

    // On envoie des valeurs "vides" pour les formulaires si tu ne les utilises pas encore
    return $this->render('compte/index.html.twig', [
        'profilForm' => null,
        'adresseForm' => null,
        'passwordForm' => null,
        'commandes' => $commandes
    ]);
}

}
