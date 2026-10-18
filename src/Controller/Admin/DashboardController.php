<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Service\TeamStatistics;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly TeamStatistics $teamStatistics,
    ) {
    }

    public function index(): Response
    {
        $teams = $this->teamRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/dashboard.html.twig', [
            'teams' => array_map(fn (Team $team) => [
                'team' => $team,
                'stats' => $this->teamStatistics->forTeam($team),
            ], $teams),
        ]);
    }

    #[AdminRoute('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('@EasyAdmin/page/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin'),
            'page_title' => 'MiniPM - Login',
        ]);
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
        yield MenuItem::linkTo(UserCrudController::class, icon: 'fa fa-user')->setPermission('ROLE_ADMIN');
    }
}
