<?php

namespace App\Form;

use App\Enum\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Adresse
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
    'expanded' => true,   // si tu veux afficher des boutons radio
    'multiple' => false,
]);

}}
