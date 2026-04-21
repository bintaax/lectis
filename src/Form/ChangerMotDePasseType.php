<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

// Construit le formulaire de mise a jour du mot de passe.
class ChangerMotDePasseType extends AbstractType
{
    // Declare les deux champs necessaires pour verifier puis remplacer le mot de passe.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ancienPassword', PasswordType::class, [
                'label' => 'Ancien mot de passe',
                'mapped' => false,
                'attr' => ['class' => 'border px-3 py-2 rounded w-full']
            ])
            ->add('nouveauPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'mapped' => false,
                'attr' => ['class' => 'border px-3 py-2 rounded w-full'],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
        ;
    }
}
