<?php

// we don't have a way to inject the slugger (service is a "global" object) to CategoryEntity (data objects - Doctrine entity)

// the slug property should be set automatically whenever the category is updated by calling the computeSlug() method.
// this method depends on a SluggerInterface implementation

namespace App\EntityListener;

use App\Entity\Category;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

// Here, because our class doesn't implement any interface nor doesn't extend any base class, 
// symfony doesn't know how to auto-configure it. Instead, we can use an attribute to tell the Symfony container how to wire it:
#[AsEntityListener(event: Events::prePersist, entity: Category::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Category::class)]
class CategoryEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    // slug is updated when a new category is created
    public function prePersist(Category $category, LifecycleEventArgs $event)
    {
        $category->computeSlug($this->slugger);
    }

    // slug is updated whenever it is updated 
    public function preUpdate(Category $category, LifecycleEventArgs $event)
    {
        $category->computeSlug($this->slugger);
    }
}