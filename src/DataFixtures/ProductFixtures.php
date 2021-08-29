<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i<=5; $i++){
            $product = new Product();
            $product->setName("Product $i");
            $manager->persist($product);
        }
        
        $manager->flush();
    }
}
