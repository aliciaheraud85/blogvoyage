<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')->onlyOnIndex(),
            TextField::new('title')->setColumns('col-md-6'),
            TextField::new('abstract')->setColumns('col-md-6'),
            TextareaField::new('content')->setColumns('col-md-12'),
            TextareaField::new('content2')->setColumns('col-md-12'),

            $image = Imagefield::new('image')
                ->setUploadDir('public/divers/images')
                ->setBasePath('divers/image')
                ->setSortable(false) //l'image ne peut pas être null
                ->setFormTypeOption('required', false)->setColumns('col-md-2'),

            $image2 = Imagefield::new('image2')
            ->setUploadDir('public/divers/images')
            ->setBasePath('divers/image')
            ->setFormTypeOption('required', false)->setColumns('col-md-2'),
            
            AssociationField::new('rubrik')->setColumns('col-md-2'),

            AssociationField::new('user')->setColumns('col-md-6'),

            DateField::new('createdAt')->OnlyOnIndex(),

            $isPublished = BooleanField::new('isPublished')->setColumns('col-md-1')->setLabel('Publié'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Post')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(10)
        ;
    }
   
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
         ->add('user')
         ->add('title')
         ->add('rubrik')
         ->add('createdAt')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }
}
