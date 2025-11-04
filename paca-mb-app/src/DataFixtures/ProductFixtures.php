<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugger = new AsciiSlugger();


        for ($i = 1; $i <= 50; $i++) {
            $product = new Product();

            $title = $faker->words(3, true);
            $slug = strtolower($slugger->slug($title));

            $product
                ->setTitle($title)
                ->setSlug($slug)
                
                ->setDescription($faker->paragraph())
                ->setPrice($faker->randomFloat(2, 50, 1500))
                ->setIsUsed($faker->boolean(30))
                ->setUpdatedAt(new \DateTimeImmutable());

            // 🔹 Génération explicite d'une référence unique
            $productReference = strtoupper(substr($slug, 0, 5)) . '-' . strtoupper(substr(uniqid(), -5));
            $reflection = new \ReflectionClass($product);
            $property = $reflection->getProperty('reference');
            $property->setAccessible(true);
            $property->setValue($product, $productReference);

            $manager->persist($product);
        }


        $manager->flush();
    }
}
