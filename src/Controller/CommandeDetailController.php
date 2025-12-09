<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Repository\CommandesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CommandeDetailController extends AbstractController
{
    #[Route('/commande/{id}', name: 'app_commande_detail')]
    public function detail(int $id, CommandesRepository $repo): Response
    {
          $commande = $repo->find($id);


    if (!$commande) {
        throw $this->createNotFoundException("Commande introuvable.");
    }

    return $this->render('commande_detail/index.html.twig', [
        'commande' => $commande
    ]);

    }
}

