<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Construit le formulaire de contact affiche sur la page publique.
class ContactType extends AbstractType
{
    // Declare les champs visibles dans le formulaire de contact.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Centralise les classes CSS pour garder le meme style sur tous les champs.
        $inputClass = 'mt-1 block w-full bg-[#F7F9FC] border-[#E1E5EB] rounded-xl px-4 py-3 text-[#2E3A45] focus:ring-2 focus:ring-[#7B8A9A] focus:border-transparent transition-all outline-none';

        $builder
            ->add('field_name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => $inputClass, 'placeholder' => 'Dupont']
            ])
            ->add('field_surname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => $inputClass, 'placeholder' => 'Jean']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'attr' => ['class' => $inputClass, 'placeholder' => 'jean@exemple.fr']
            ])
            ->add('objet', TextType::class, [
                'label' => 'Objet de votre demande',
                'attr' => ['class' => $inputClass, 'placeholder' => 'Ma commande #12345']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'class' => $inputClass . ' h-32 resize-none', 
                    'placeholder' => 'Dites-nous tout...'
                ],
            ]);
    }

    // Ce formulaire n'est rattache a aucune entite Doctrine.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
