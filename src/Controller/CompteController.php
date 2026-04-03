<?php

namespace App\Controller;

use App\Form\UtilisateurType;
use App\Repository\CommandesRepository;
use App\Form\ChangerMotDePasseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


// Contrôleur pour compte.
final class CompteController extends AbstractController
{
    // Charge les données nécessaires et rend la vue.
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

// Charge les données nécessaires et rend la vue.
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

// Charge les données nécessaires et rend la vue.
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



// Charge les données nécessaires et rend la vue.
#[Route('/compte/supprimer', name: 'app_compte_supprimer', methods: ['POST'])]
public function supprimer(
    EntityManagerInterface $em,
    RequestStack $requestStack,
    Request $request,
    TokenStorageInterface $tokenStorage
): Response {
    /** @var \App\Entity\Utilisateurs|null $user */
    $user = $this->getUser();

    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // (si tu as mis le CSRF dans le form twig)
    if (!$this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    // ✅ supprimer les paniers en base
    foreach ($user->getPaniers() as $panier) {
        $em->remove($panier);
    }

    // ✅ supprimer aussi toutes les commandes de l'utilisateur
    foreach ($user->getCommandes() as $commande) {
        $em->remove($commande);
    }

    // ✅ anonymiser/désactiver le compte
    $user->anonymizeAndDeactivate();
    $em->flush();

    // ✅ couper le token de sécurité (app.user ne doit plus exister)
    $tokenStorage->setToken(null);

    // ✅ invalider la session
    $session = $requestStack->getSession();
    if ($session) {
        $session->clear();
        $session->invalidate();
    }

    $this->addFlash('success', 'Votre compte a bien été supprimé.');

    // ✅ logout Symfony (nettoie aussi remember_me si activé)
    return $this->redirectToRoute('app_logout');
}

// Charge les données nécessaires et rend la vue.
#[Route('/compte/lectis-plus', name: 'app_compte_lectis_plus')]
public function lectisPlus(): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    return $this->render('compte/lectis_plus.html.twig');
}

// Charge les données nécessaires et rend la vue.
#[Route('/compte/lectis-plus/adherer', name: 'app_compte_lectis_plus_adherer', methods: ['POST'])]
public function lectisPlusAdherer(EntityManagerInterface $em, Request $request): Response
{
    /** @var \App\Entity\Utilisateurs $user */
    $user = $this->getUser();
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    if (!$this->isCsrfTokenValid('lectis_plus_join', $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    if (!$user->isAdherent()) {
        $user->becomeAdherent(); // méthode dans Utilisateurs
        $em->flush();
        $this->addFlash('success', 'Bienvenue dans Lectis+ 📚💙');
    } else {
        $this->addFlash('info', 'Vous êtes déjà adhérent Lectis+ 🙂');
    }

    return $this->redirectToRoute('app_compte');
}

// Charge les données nécessaires et rend la vue.
#[Route('/compte/lectis-plus/quitter', name: 'app_compte_lectis_plus_quitter', methods: ['POST'])]
public function lectisPlusQuitter(EntityManagerInterface $em, Request $request): Response
{
    /** @var \App\Entity\Utilisateurs $user */
    $user = $this->getUser();
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    if (!$this->isCsrfTokenValid('lectis_plus_leave', $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    if ($user->isAdherent()) {
        $user->leaveAdherent();
        $em->flush();
        $this->addFlash('success', 'Votre abonnement Lectis+ a bien été retiré.');
    } else {
        $this->addFlash('info', 'Vous n’êtes pas adhérent Lectis+.');
    }

    return $this->redirectToRoute('app_compte');
}

}
