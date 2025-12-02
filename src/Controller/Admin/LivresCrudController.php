<?php

namespace App\Controller\Admin;

use App\Entity\Livres;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class LivresCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Livres::class;
    }

    public function configureFields(string $pageName): iterable
{
    return [
        TextField::new('titre'),
        TextField::new('auteur'),
        AssociationField::new('genre'),
        TextField::new('slug'),
        TextField::new('editeur'),
        MoneyField::new('prix')
    ->setCurrency('EUR')
    ->setNumDecimals(2)
    ->setStoredAsCents(false)
    ->setLabel('Prix (€)'),
    BooleanField::new('disponibilite')
    ->setLabel('Disponible ?'),

        TextField::new('datePublication')
    ->setLabel('Date de publication (AAAA-MM-JJ)'),

        IntegerField::new('nbPages'),
        IntegerField::new('ageAutorise'),
        TextareaField::new('resume'),

        ImageField::new('photo')
            ->setBasePath('/assets/img')
            ->setUploadDir('public/assets/img')
            ->setUploadedFileNamePattern('[uuid].[extension]')
            ->setRequired(false),
        
       
    ];
}
}
