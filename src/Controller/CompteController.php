<?php

namespace App\Controller;

use App\Form\UtilisateurType;
use App\Repository\CommandesRepository;
use App\Form\ChangerMotDePasseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

#[Route('/compte/modifier', name: 'app_compte_modifier')]
public function modifier(Request $request, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    $form = $this->createForm(UtilisateurType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $em->flush();

        $this->addFlash('success', 'Vos informations ont été mises à jour.');
        return $this->redirectToRoute('app_compte');
    }

    return $this->render('compte/modifier.html.twig', [
        'form' => $form->createView()
    ]);
}

#[Route('/compte/password', name: 'app_compte_password')]
public function password(
    Request $request, 
    UserPasswordHasherInterface $hasher, 
    EntityManagerInterface $em
): Response {

    $user = $this->getUser(); // ✔️ l'utilisateur connecté

    $form = $this->createForm(ChangerMotDePasseType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $ancien = $form->get('ancienPassword')->getData();

        if (!$hasher->isPasswordValid($user, $ancien)) {
            $this->addFlash('error', 'Ancien mot de passe incorrect.');
        } else {
            $nouveau = $form->get('nouveauPassword')->getData();

            $user->setPassword(
                $hasher->hashPassword($user, $nouveau)
            );

            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été modifié.');
            return $this->redirectToRoute('app_compte');
        }
    }

    return $this->render('compte/password.html.twig', [
        'form' => $form->createView()
    ]);
}

#[Route('/compte/supprimer', name: 'app_compte_supprimer')]
public function supprimer(
    EntityManagerInterface $em,
    RequestStack $requestStack
): Response {
    $user = $this->getUser();

    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // Déconnexion : suppression du token de sécurité
    $this->container->get('security.token_storage')->setToken(null);

    // Invalidate session
    $session = $requestStack->getSession();
    if ($session) {
        $session->invalidate();
    }

    // Suppression du compte dans la BDD
    $em->remove($user);
    $em->flush();

    $this->addFlash('success', 'Votre compte a bien été supprimé.');
    return $this->redirectToRoute('app_home');
}




}
