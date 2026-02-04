<?php

namespace App\Controller\Admin;

use App\Entity\Commandes;
use App\Enum\Statut;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

final class CommandesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commandes::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        $statutChoices = [];
        foreach (Statut::cases() as $case) {
            $statutChoices[$case->value] = $case;
        }


        yield TextField::new('numeroCommande', 'N°')->onlyOnIndex();
        yield DateTimeField::new('createdAt', 'Date');

        yield TextField::new('numeroCommande', 'N°')->onlyOnIndex();
yield DateTimeField::new('createdAt', 'Date');

// ✅ Client en clair
yield TextField::new('utilisateurs.nom', 'Nom')->onlyOnIndex();
yield TextField::new('utilisateurs.prenom', 'Prénom')->onlyOnIndex();
yield TextField::new('utilisateurs.email', 'Email')->onlyOnIndex();


        yield MoneyField::new('total', 'Total')
            ->setCurrency('EUR')
            ->setStoredAsCents(false); // ✅ IMPORTANT


        yield ChoiceField::new('statut', 'Statut')
    ->setChoices(array_combine(
        array_map(fn($s) => $s->label(), \App\Enum\Statut::cases()),
        \App\Enum\Statut::cases()
    ))
    ->formatValue(fn ($value, $entity) => $value?->label() ?? '')
    ->renderExpanded(false);



        yield TextField::new('paiement', 'Paiement')->onlyOnIndex();
        yield TextField::new('adresseLivraison', 'Adresse')->hideOnIndex();

        // ✅ Afficher les articles sur la page détail
        yield CollectionField::new('ligneCommandes', 'Articles')
            ->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW)
            ->disable(Action::DELETE);
    }

   
}
