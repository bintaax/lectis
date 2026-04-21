<?php

namespace App\Controller\Admin;

use App\Entity\Livres;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

// Configure le CRUD des livres dans l'administration.
final class LivresCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Livres::class;
    }

    // Ameliore la lisibilite de la section admin des livres.
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Livre')
            ->setEntityLabelInPlural('Livres')
            ->setDefaultSort(['id' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPaginatorPageSize(20);
    }

    // Limite chaque vue aux informations utiles.
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('titre', 'Titre');
        yield TextField::new('auteur', 'Auteur')->hideOnIndex();
        yield AssociationField::new('genre', 'Genre')->hideOnIndex();
        yield TextField::new('editeur', 'Editeur')->hideOnIndex();

        yield MoneyField::new('prix', 'Prix')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->setStoredAsCents(false);

        yield TextField::new('datePublication', 'Publication')
            ->hideOnIndex();

        yield IntegerField::new('nbPages', 'Pages')->hideOnIndex();
        yield IntegerField::new('ageAutorise', 'Age')->hideOnIndex();

        yield BooleanField::new('disponibilite', 'Disponible')->hideOnIndex();
        yield BooleanField::new('isBestSeller', 'Best seller')->hideOnIndex();

        yield TextField::new('slug', 'Slug')
            ->hideOnIndex()
            ->setHelp('Le slug est genere automatiquement a partir du titre si besoin.');

        yield TextareaField::new('resume', 'Resume')
            ->hideOnIndex();

        yield ImageField::new('photo', 'Couverture')
            ->setBasePath('/assets/img')
            ->setUploadDir('public/assets/img')
            ->setUploadedFileNamePattern('[uuid].[extension]')
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms();

        yield ImageField::new('photo', 'Couverture')
            ->setBasePath('/assets/img')
            ->onlyOnIndex();
    }

    // Ajoute la vue detail et garde les actions essentielles.
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
