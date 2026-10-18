<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Enum\ProjectStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SortOrder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Twig\Component\Option\BadgeVariant;

/**
 * @extends AbstractCrudController<Project>
 */
class ProjectCrudController extends AbstractCrudController
{
    public const array STATUS_BADGES = [
        ProjectStatus::Planned->name => BadgeVariant::Secondary->value,
        ProjectStatus::Active->name => BadgeVariant::Primary->value,
        ProjectStatus::Completed->name => BadgeVariant::Success->value,
    ];

    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['title' => SortOrder::ASC])
            ->setSearchFields(['title', 'description'])
            ->overrideTemplate('crud/detail', 'admin/project/detail.html.twig')
            ->setPageTitle(Crud::PAGE_DETAIL, fn (Project $project) => $project->getTitle());
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextEditorField::new('description')->hideOnIndex(),
            ChoiceField::new('status')->renderAsBadges(self::STATUS_BADGES),
            AssociationField::new('team'),
            AssociationField::new('tasks')->hideOnForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(ChoiceFilter::new('status')->setTranslatableChoices(ProjectStatus::cases()))
            ->add('team');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
