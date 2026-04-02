<?php

namespace App\Controller\Admin;

use App\Entity\Livres;
use App\Entity\Commandes;
use App\Controller\Admin\LivresCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

// Contrôleur pour admin dashboard.
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
    // Charge les données nécessaires et rend la vue.
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    // Charge les données nécessaires et rend la vue.
    public function index(): Response
    {
        // Redirection vers le CRUD Livres
        $url = $this->adminUrlGenerator
            ->setController(LivresCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    // Charge les données nécessaires et rend la vue.
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lectis')
            ->setLocales(['fr']);
    }

    // Charge les données nécessaires et rend la vue.
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Livres', 'fas fa-book', Livres::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-receipt', Commandes::class);
    }
}
