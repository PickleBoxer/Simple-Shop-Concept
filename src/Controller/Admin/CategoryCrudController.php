<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    
    public function configureFields(string $pageName): iterable
    {

            yield TextField::new('name');
            yield BooleanField::new('active');
            yield TextField::new('slug')
                ->setHelp('use - for auto generation');
    }

    // default value set based on the entity
    public function createEntity(string $entityFqcn) {
        $category = new Category();
        $category->setSlug('-');
        return $category;
    }
    
}
