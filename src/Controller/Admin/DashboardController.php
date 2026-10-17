<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_team_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('MiniPM')
            ->useEntityTranslations();
    }

    public function configureCrud(): Crud
    {
        return Crud::new()->setFormOptions(['attr' => ['novalidate' => 'novalidate']]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('dashboard.menu.home', 'fa fa-home');
        yield MenuItem::linkTo(TeamCrudController::class, icon: 'fa fa-people-group');
        yield MenuItem::linkTo(ProjectCrudController::class, icon: 'fa fa-folder');
        yield MenuItem::linkTo(TaskCrudController::class, icon: 'fa fa-list-check');
        yield MenuItem::linkTo(UserCrudController::class, icon: 'fa fa-user');
    }
}
