<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('field_name', TextType::class, [
            'label' => 'Nom',
            'attr' => ['class' => 'border px-3 py-2 rounded w-full']
        ])
        ->add('field_surname', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['class' => 'border px-3 py-2 rounded w-full']
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['class' => 'border px-3 py-2 rounded w-full']
        ])
        
                ->add('objet', TextType::class, [
                'label' => 'Objet',
                'attr' => ['class' => 'border px-3 py-2 rounded w-full'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['class' => 'border px-3 py-2 rounded w-full h-40'],
            ]);

    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
