<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use App\Service\TeamStatistics;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SortOrder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends AbstractCrudController<Team>
 */
class TeamCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TeamStatistics $teamStatistics,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Team::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['name' => SortOrder::ASC])
            ->setSearchFields(['name', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextEditorField::new('description')->hideOnIndex(),
            AssociationField::new('members'),
            AssociationField::new('projects')->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $dashboard = Action::new('dashboard', 'team.dashboard.title', 'fa fa-chart-simple')
            ->linkToCrudAction('dashboard');

        return $actions->add(Crud::PAGE_INDEX, $dashboard);
    }

    #[AdminRoute('/{id}/dashboard', name: 'dashboard')]
    public function dashboard(Team $team): Response
    {
        return $this->render('admin/team/dashboard.html.twig', [
            'team' => $team,
            'stats' => $this->teamStatistics->forTeam($team),
        ]);
    }
}
