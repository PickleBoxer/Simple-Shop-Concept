<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
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

        for ($i = 1; $i <= 20; $i++) {
            $product = new Product();
            //$today = new \DateTimeImmutable('now');
            $product
                ->setName('Product ' . $i)
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua')
                ->setPrice(mt_rand(10, 600))
                //->setCreatedAt($today)
                ->setActive(1)
                // this reference returns the Category object created in CategoryFixtures
                ->setIdCategory($this->getReference(CategoryFixtures::DEFAULT_CAT_ID));;

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
