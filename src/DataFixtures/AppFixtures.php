<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const NB_ARTICLES = 50;
    private const NB_CATEGORY = 10;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

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

        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $article = new Article();
            $article
                ->setTitle($faker->realText(35))
                ->setDateCreated($faker->dateTimeBetween('-2 years'))
                ->setVisible($faker->boolean(80))
                ->setContent($faker->realTextBetween(200, 500))
                ->setCategory(($categories[$faker->numberBetween(0, count($categories) - 1)]));

            $manager->persist($article);
        }
        $manager->flush();

        $admin = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'testAdmin'
        );
        $admin
            ->setPassword($hashedPassword)
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@test.com');

        $manager->persist($admin);

        $regular_user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $regular_user,
            'testRegular'
        );
        $regular_user
            ->setPassword($hashedPassword)
            ->setEmail('regularusertest@test.com');

        $manager->persist($regular_user);

        $manager->flush();
    }
}
