<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\RequestStack;

use App\Entity\Livres;
use App\Controller\Admin\LivresCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
      public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }
    public function index(): Response
    {
        // Redirection propre vers le CRUD Livres
        $url = $this->adminUrlGenerator
            ->setController(LivresCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
      
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lectis')
            ->setLocales(['fr']);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Livres', 'fas fa-book', Livres::class);

    }
}
