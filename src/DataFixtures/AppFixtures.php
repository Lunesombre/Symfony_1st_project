<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const NB_ARTICLES = 50;
    private const NB_CATEGORY = 10;
    private const NB_TEST_USERS = 4;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0, $categories = []; $i < self::NB_CATEGORY; $i++) {
            $category = new Category();
            $category
                ->setName($faker->word())
                ->setDescription($faker->realTextBetween(100, 200));
            $categories[] = $category;

            $manager->persist($category);
        }

        for ($i = 0, $regularUsers = []; $i < self::NB_TEST_USERS; $i++) {
            $regularUser = new User();
            $regularUser
                ->setPassword('regular')
                ->setEmail($faker->userName() . '@test.com');
            $regularUsers[] = $regularUser;
            $manager->persist($regularUser);
        }

        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $article = new Article();
            $article
                ->setTitle($faker->realText(35))
                ->setDateCreated($faker->dateTimeBetween('-2 years'))
                ->setVisible($faker->boolean(80))
                ->setContent($faker->realTextBetween(200, 500))
                ->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)])
                ->setAuthor($regularUsers[$faker->numberBetween(0, count($regularUsers) - 1)]);

            $manager->persist($article);
        }

        $admin = new User();
        $admin
            ->setPassword('testAdmin')
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@test.com');

        $manager->persist($admin);


        $manager->flush();
    }
}
