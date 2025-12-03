<?php

namespace App\Form;

use App\Enum\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('methodePaiement', ChoiceType::class, [
            'label' => 'Méthode de paiement',
            'choices' => [
                'Carte bancaire' => Paiement::CB,
                'PayPal' => Paiement::Paypal,
            ],
            'expanded' => true, // Affichage en boutons radio
            'multiple' => false,
        ]);
    }
}

