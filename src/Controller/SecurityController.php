<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

// Gere l'authentification et la sortie de session.
class SecurityController extends AbstractController
{
    use TargetPathTrait;

    // Affiche la page de connexion avec le dernier email et l'eventuelle erreur.
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $redirectPath = (string) $request->query->get('redirect', '');
        if ($redirectPath !== '' && str_starts_with($redirectPath, '/')) {
            $this->saveTargetPath($request->getSession(), 'main', $redirectPath);
        }
  
        // Recupere l'erreur de connexion precedente si elle existe.
        $error = $authenticationUtils->getLastAuthenticationError();
        // Recupere le dernier email saisi pour pre-remplir le formulaire.
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    // La route logout est interceptee par Symfony, la methode ne doit jamais etre executee.
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
     
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
