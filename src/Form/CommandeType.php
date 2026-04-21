<?php

namespace App\Form;

use App\Enum\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

// Construit le formulaire utilise pour collecter l'adresse et le paiement de la commande.
class CommandeType extends AbstractType
{
    // Declare les champs affiches lors de l'etape de validation de commande.
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Regroupe les champs d'adresse de livraison.
            ->add('adresse', TextType::class, [
                'label' => 'Adresse de livraison',
                'attr' => ['class' => 'border px-3 py-2 rounded w-full']

            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['class' => 'border px-3 py-2 rounded w-full']
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => ['class' => 'border px-3 py-2 rounded w-full']
            ])

          ->add('paiement', ChoiceType::class, [
    'choices' => [
        'Carte bancaire' => 'carte_bancaire',
        'PayPal' => 'paypal',
    ],
    'expanded' => true,   // Affiche les moyens de paiement sous forme de boutons radio.
    'multiple' => false,
]);

}}
