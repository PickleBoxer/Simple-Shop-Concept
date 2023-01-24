<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    public const DEFAULT_CAT_ID = null;

    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category
            ->setName('Default')
            ->setActive(1);

        $manager->persist($category);

        $manager->flush();

        // other fixtures can get this object using the CategoryFixtures::DEFAULT_CAT_ID constant

        $this->setReference(self::DEFAULT_CAT_ID, $category);
    }
}
