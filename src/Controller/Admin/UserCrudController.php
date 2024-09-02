<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')->onlyOnIndex()->setColumns('col-md-6'),
            TextField::new('firstname')->setColumns('col-md-3'),
            TextField::new('lastname')->setColumns('col-md-3'),
            TextField::new('email')->setColumns('col-md-4'),
            TextField::new('pseudo')->setColumns('col-md-2'),
            TextField::new('city')->setColumns('col-md-4'),
            TextField::new('country')->setColumns('col-md-4'),
            TextField::new('opption')->setColumns('col-md-4'),
            TextField::new('password'),
            ImageField::new('avatar')
                ->setUploadDir('public/divers/avatars')
                ->setBasePath('divers/avatars')
                ->setSortable(false)
                ->setFormTypeOption('required', false)->setColumns('col-md-2'),

            DateField::new('createdAt')->onlyOnIndex(),

            $roles = ChoiceField::new('roles')->setColumns('col-md-4')->setChoices([
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_EDITOR' => 'ROLE_EDITOR',
                'ROLE_MODO' => 'ROLE_MODO'

            ])->allowMultipleChoices(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('firstname')
            ->add('lastname')
            ->add('city')
            ->add('country')
            ->add('roles')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }
   
}
