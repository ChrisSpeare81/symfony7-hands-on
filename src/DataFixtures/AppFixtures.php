<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {

    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        for ($i = 0; $i < rand(10, 50); $i++) {

            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $manager->persist($user);

        }

        $manager->flush();
        
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < rand(1, 500); $i++) {

            $microPost = new MicroPost();
            $microPost->setTitle($faker->words(3, true));
            $microPost->setText($faker->text);
            $microPost->setCreated($faker->dateTime);
            $microPost->setAuthor($faker->randomElement($users));
            $manager->persist($microPost);

            for ($y = 0; $y < rand(0, 20); $y++) {

                $comment = new Comment();
                $comment->setText($faker->text);
                $comment->setPost($microPost);
                $comment->setAuthor($faker->randomElement($users));
                $manager->persist($comment);

            }
        }

        $manager->flush();
    }
}
