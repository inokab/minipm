<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SortOrder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Twig\Component\Option\BadgeVariant;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractCrudController<User>
 */
#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    public const array ROLE_BADGES = [
        UserRole::Admin->value => BadgeVariant::Danger->value,
        UserRole::Member->value => BadgeVariant::Secondary->value,
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['name' => SortOrder::ASC])
            ->setSearchFields(['name', 'email']);
    }

    public function configureFields(string $pageName): iterable
    {
        $isNew = Crud::PAGE_NEW === $pageName;

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
            ChoiceField::new('roles')
                ->setTranslatableChoices([
                    UserRole::Admin->value => UserRole::Admin,
                    UserRole::Member->value => UserRole::Member,
                ])
                ->allowMultipleChoices()
                ->renderAsBadges(self::ROLE_BADGES),
            TextField::new('plainPassword')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired($isNew)
                ->setFormTypeOption('constraints', $isNew ? [new NotBlank()] : []),
            AssociationField::new('teams')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $this->hashPlainPassword($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $this->hashPlainPassword($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPlainPassword(User $user): void
    {
        if (null === $user->getPlainPassword()) {
            return;
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->setPlainPassword(null);
    }
}
