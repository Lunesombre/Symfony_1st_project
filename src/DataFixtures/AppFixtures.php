<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const NB_ARTICLES = 50;
    private const NB_CATEGORY = 10;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        for ($i = 0, $categories=[]; $i < self::NB_CATEGORY; $i++) {
            $category = new Category();
            $category
                ->setName($faker->word())
                ->setDescription($faker->realTextBetween(100, 200));
            $categories[]=$category;

            $manager->persist($category);
        }

        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $article = new Article();
            $article
                ->setTitle($faker->realText(35))
                ->setDateCreated($faker->dateTimeBetween('-2 years'))
                ->setVisible($faker->boolean(80))
                ->setContent($faker->realTextBetween(200, 500))
                ->setCategory(($categories[$faker->numberBetween(0,count($categories)-1)]));

            $manager->persist($article);
        }
        $manager->flush();
    }
}
