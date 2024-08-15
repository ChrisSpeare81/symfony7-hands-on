<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        for ($i = 0; $i < 1000; $i++) {

            $microPost = new MicroPost();
            $microPost->setTitle($faker->words(3, true));
            $microPost->setText($faker->text);
            $microPost->setCreated($faker->dateTime);

            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
