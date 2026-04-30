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


// Gere les pages du compte client : profil, mot de passe, suppression et adhesion Lectis+.
final class CompteController extends AbstractController
{
    // Affiche le tableau de bord du compte avec l'historique des commandes et les points de lecture.
    #[Route('/compte', name: 'app_compte')]
public function index(
    CommandesRepository $commandeRepository
): Response {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();

    // Recupere toutes les commandes liees a l'utilisateur connecte.
    $commandes = $commandeRepository->findBy(
        ['utilisateurs' => $user],
       
    );

    $pointsLecture = 0;

if ($user->isAdherent() && $user->getAdherentAt()) {
    foreach ($commandes as $commande) {

        // 🔥 On ignore les commandes AVANT l'adhésion
        if ($commande->getCreatedAt() >= $user->getAdherentAt()) {

            // ✅ On compte les centimes
            $pointsLecture += (int) round($commande->getTotal() * 10);
        }
    }
}

    // Fournit aussi des entrees de formulaire nulles pour conserver la structure Twig actuelle.
    return $this->render('compte/index.html.twig', [
        'profilForm' => null,
        'adresseForm' => null,
        'passwordForm' => null,
        'commandes' => $commandes,
        'pointsLecture' => $pointsLecture,
    ]);
}

// Affiche le formulaire de modification du profil puis enregistre les changements.
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

// Affiche le formulaire de changement de mot de passe et verifie l'ancien mot de passe.
#[Route('/compte/password', name: 'app_compte_password')]
public function password(
    Request $request, 
    UserPasswordHasherInterface $hasher, 
    EntityManagerInterface $em
): Response {

    $user = $this->getUser(); // Recupere le compte actuellement authentifie.

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



// Supprime logiquement le compte, nettoie les donnees reliees et deconnecte l'utilisateur.
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

    // Verifie le token CSRF envoye par le formulaire de suppression.
    if (!$this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    // Supprime les paniers persistés avant d'anonymiser le compte.
    foreach ($user->getPaniers() as $panier) {
        $em->remove($panier);
    }

    // Supprime aussi les commandes reliees a ce compte.
    foreach ($user->getCommandes() as $commande) {
        $em->remove($commande);
    }

    // Anonymise ensuite le compte pour ne plus conserver de donnees personnelles actives.
    $user->anonymizeAndDeactivate();
    $em->flush();

    // Retire le jeton de securite pour que l'utilisateur ne soit plus considere comme connecte.
    $tokenStorage->setToken(null);

    // Vide puis invalide la session courante.
    $session = $requestStack->getSession();
    if ($session) {
        $session->clear();
        $session->invalidate();
    }

    $this->addFlash('success', 'Votre compte a bien été supprimé.');

    // Redirige vers la route de logout Symfony pour finaliser proprement la deconnexion.
    return $this->redirectToRoute('app_logout');
}

// Affiche la page de presentation de l'offre Lectis+.
#[Route('/lectis-plus', name: 'app_compte_lectis_plus')]
public function lectisPlus(): Response
{
    return $this->render('compte/lectis_plus.html.twig');
}

#[Route('/compte/lectis-plus', name: 'app_compte_lectis_plus_legacy')]
public function lectisPlusLegacy(): Response
{
    return $this->redirectToRoute('app_compte_lectis_plus', [], Response::HTTP_MOVED_PERMANENTLY);
}

// Traite la demande d'adhesion a Lectis+ pour l'utilisateur courant.
#[Route('/compte/lectis-plus/adherer', name: 'app_compte_lectis_plus_adherer', methods: ['POST'])]
public function lectisPlusAdherer(EntityManagerInterface $em, Request $request): Response
{
    /** @var \App\Entity\Utilisateurs $user */
    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('info', 'Connectez-vous ou créez un compte pour adhérer à Lectis+.');

        return $this->redirectToRoute('app_login', [
            'redirect' => $this->generateUrl('app_compte_lectis_plus'),
        ]);
    }

    if (!$this->isCsrfTokenValid('lectis_plus_join', $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    if (!$user->isAdherent()) {
        $user->becomeAdherent(); // Active l'adhesion et memorise sa date de debut.
        $em->flush();
        $this->addFlash('success', 'Bienvenue dans Lectis+ 📚💙');
    } else {
        $this->addFlash('info', 'Vous êtes déjà adhérent Lectis+ 🙂');
    }

    return $this->redirectToRoute('app_compte');
}

// Retire l'adhesion Lectis+ apres verification du token CSRF.
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
