<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{    
    /**
     * load
     *
     * @param  mixed $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $today = new \DateTimeImmutable('now');
            $product
                ->setName('Product ' . $i)
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua')
                ->setPrice(mt_rand(10, 600))
                ->setCreatedAt($today)
                ->setActive(1)
                ->setIdCategory('1')
                ;

            $manager->persist($product);
        }

        $manager->flush();
    }
}