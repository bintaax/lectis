<?php

namespace App\Controller\Admin;

use App\Entity\Livres;
use App\Entity\Commandes;
use App\Controller\Admin\LivresCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

// Configure l'entree d'administration EasyAdmin et son menu principal.
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
    // Injecte le generateur d'URL admin utilise pour les redirections internes.
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    // Redirige l'arrivee sur /admin vers la liste des livres.
    public function index(): Response
    {
        // L'ecran d'accueil admin pointe directement vers le CRUD des livres.
        $url = $this->adminUrlGenerator
            ->setController(LivresCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    // Definis le titre et la locale de l'interface d'administration.
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lectis')
            ->setLocales(['fr']);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('assets/css/admin-responsive.css');
    }

    // Construit les entrees du menu lateral EasyAdmin.
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Livres', 'fas fa-book', Livres::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-receipt', Commandes::class);
    }
}
