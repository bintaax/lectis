<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Form\RegistrationFormType;
use App\Security\ConnexionAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

// Gere l'inscription, l'attribution des roles et la verification d'email.
class RegistrationController extends AbstractController
{
    use TargetPathTrait;

    // Injecte le service qui envoie et valide les emails de confirmation.
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    

    // Affiche le formulaire d'inscription puis cree le compte si les donnees sont valides.
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $redirectPath = (string) $request->query->get('redirect', '');
        if ($redirectPath !== '' && str_starts_with($redirectPath, '/')) {
            $this->saveTargetPath($request->getSession(), 'main', $redirectPath);
        }

        $user = new Utilisateurs();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hash le mot de passe saisi avant de le stocker.
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Attribue le role administrateur uniquement a l'adresse email prevue.
if ($user->getEmail() === "admin@lectis.org") {
    $user->setRoles(['ROLE_ADMIN']);
} else {
    $user->setRoles(['ROLE_USER']);
}

            $entityManager->persist($user);
            $entityManager->flush();

            // Genere un lien signe puis envoie l'email de verification.
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('admin@lectis.org', 'Administration Lectis'))
                    ->to((string) $user->getEmail())
                    ->subject('Veuillez confirmer votre email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $security->login($user, ConnexionAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    // Valide le lien de verification et active le compte.
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Verifie la signature du lien puis marque l'utilisateur comme verifie.
        try {
            /** @var Utilisateurs $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse mail a bien été vérifiée');

        return $this->redirectToRoute('app_register');
    }
}
