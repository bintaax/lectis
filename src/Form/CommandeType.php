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
                'label' => 'Adresse de livraison'
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville'
            ])

            // Paiement : renvoie directement un Enum
            ->add('paiement', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => Paiement::cases(),  // 🔥 toutes les valeurs de l’Enum
                'choice_label' => function (?Paiement $choice) {
                    return match ($choice) {
                        Paiement::CB => 'Carte bancaire',
                        Paiement::PAYPAL => 'PayPal',
                        default => 'Autre'
                    };
                },
                'expanded' => true,   // boutons radio
                'multiple' => false,
            ]);
    }
}
