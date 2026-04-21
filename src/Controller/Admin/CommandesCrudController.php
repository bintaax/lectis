<?php

namespace App\Controller\Admin;

use App\Entity\Commandes;
use App\Enum\Statut;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

// Configure l'affichage des commandes dans l'interface d'administration.
final class CommandesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commandes::class;
    }

    // Regle les labels et le tri par defaut du CRUD commandes.
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPaginatorPageSize(20);
    }

    // Declare les champs visibles selon la page EasyAdmin.
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('numeroCommande', 'Commande');
        yield DateTimeField::new('createdAt', 'Date');
        yield TextField::new('clientLabel', 'Client')
            ->onlyOnIndex();
        yield MoneyField::new('total', 'Total')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);
        yield ChoiceField::new('statut', 'Statut')
            ->setChoices(array_combine(
                array_map(static fn (Statut $statut) => $statut->label(), Statut::cases()),
                Statut::cases()
            ))
            ->formatValue(static fn ($value, ?Commandes $entity) => $entity?->getStatutAdminLabel() ?? '')
            ->renderExpanded(false);
        yield TextField::new('paiement', 'Paiement')->hideOnIndex();
        yield TextField::new('adresseLivraison', 'Adresse')->hideOnIndex();
        yield TextField::new('utilisateurs.email', 'Email client')->hideOnIndex();

        // Affiche les articles de la commande sur la page detail.
        yield CollectionField::new('ligneCommandes', 'Articles')
            ->onlyOnDetail();
    }

    // Active seulement les actions utiles sur les commandes.
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW)
            ->disable(Action::DELETE);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $queryBuilder
            ->innerJoin('entity.utilisateurs', 'u')
            ->andWhere('u.deletedAt IS NULL');
    }

   
}
