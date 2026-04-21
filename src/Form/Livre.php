<?php

namespace App\Form;

use App\Entity\Livres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Construit le formulaire d'administration d'un livre.
class Livre extends AbstractType
{
    // Declare les champs utilisables pour creer ou modifier un livre.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class)
            ->add('auteur', TextType::class)
            ->add('genre', TextType::class)
            ->add('slug', TextType::class)
            ->add('maisonEdition', TextType::class)
            ->add('langue', TextType::class)
            ->add('nbPages', IntegerType::class)
            ->add('ageAutorise', IntegerType::class)
            ->add('description', TextareaType::class)
            ->add('photo', FileType::class, [
                'mapped' => false,   // Le fichier est traite a part et n'est pas hydrate directement sur l'entite.
                'required' => false,
            ])
        ;
    }

    // Lie ce formulaire a l'entite Livres.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livres::class,
        ]);
    }
}
