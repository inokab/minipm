<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SortOrder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Twig\Component\Option\BadgeVariant;

/**
 * @extends AbstractCrudController<Task>
 */
class TaskCrudController extends AbstractCrudController
{
    public const array PRIORITY_BADGES = [
        TaskPriority::Low->name => BadgeVariant::Light->value,
        TaskPriority::Medium->name => BadgeVariant::Warning->value,
        TaskPriority::High->name => BadgeVariant::Danger->value,
    ];

    public const array STATUS_BADGES = [
        TaskStatus::Todo->name => BadgeVariant::Secondary->value,
        TaskStatus::InProgress->name => BadgeVariant::Primary->value,
        TaskStatus::Done->name => BadgeVariant::Success->value,
    ];

    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => SortOrder::DESC])
            ->setSearchFields(['title', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextEditorField::new('description')->hideOnIndex(),
            AssociationField::new('project'),
            AssociationField::new('assignee'),
            ChoiceField::new('status')->renderAsBadges(self::STATUS_BADGES),
            ChoiceField::new('priority')->renderAsBadges(self::PRIORITY_BADGES),
            DateField::new('dueDate')->setFormat(DateTimeField::FORMAT_MEDIUM),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('project')
            ->add('assignee')
            ->add(ChoiceFilter::new('status')
                ->setTranslatableChoices(TaskStatus::cases()))
            ->add(ChoiceFilter::new('priority')
                ->setTranslatableChoices(TaskPriority::cases()))
            ->add('dueDate');
    }
}
