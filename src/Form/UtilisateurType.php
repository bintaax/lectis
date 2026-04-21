<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Construit le formulaire de modification du profil utilisateur.
class UtilisateurType extends AbstractType
{
    // Declare les champs modifiables depuis l'espace compte.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['class' => 'border px-3 py-2 rounded w-full']
        ])
            ->add('nom', TextType::class,[
            'label' => 'Nom',
            'attr' => ['class' => 'border px-3 py-2 rounded w-full']
        ])
            ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['class' => 'border px-3 py-2 rounded w-full']
        ])
        ;
    }

    // Lie ce formulaire a l'entite Utilisateurs.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
