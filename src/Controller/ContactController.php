<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


// Gere la page de contact et la validation du formulaire.
final class ContactController extends AbstractController
{
    // Affiche le formulaire puis traite l'envoi si les champs sont valides.
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
          $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Ce bloc est l'endroit ou brancher l'envoi d'email ou l'enregistrement du message.

            $this->addFlash('success', 'Votre message a été envoyé avec succès !');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
