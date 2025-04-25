<?php

// src/DataFixtures/AppFixture.php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixture extends Fixture
{

    private Generator $faker;

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
    }


    public function load(ObjectManager $manager): void
    {

        /* ---------- Utilisateurs ---------- */
        // 1. admin
        $admin = new User();
        $admin->setUsername('admin')
              ->setEmail('admin@mail.com')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($this->hasher->hashPassword($admin, 'root'));
        $manager->persist($admin);

        // 2. utilisateur classique
        $john = new User();
        $john->setUsername('john')
             ->setEmail('john@mail.com')
             ->setRoles(['ROLE_USER'])
             ->setPassword($this->hasher->hashPassword($john, 'user'));
        $manager->persist($john);

        // 3. utilisateur “anonyme” (aucun rôle spécial)
        $anon = new User();
        $anon->setUsername('anonyme')
             ->setEmail('anon@mail.com')
             ->setRoles(['ROLE_USER'])
             ->setPassword($this->hasher->hashPassword($anon, 'anon'));
        $manager->persist($anon);

        /* ---------- Tâches pour John ---------- */
        for ($i = 0; $i < 5; ++$i) {
            $task = new Task();
            $task->setTitle($this->faker->sentence(4));
            $task->setContent($this->faker->paragraph(2));
            $task->setAuthor($john);
            $task->isDone(false);
            $manager->persist($task);
        }

        /* ---------- Tâches “anonymes” ---------- */
        for ($i = 0; $i < 5; ++$i) {
            $task = new Task();
            $task->setTitle('[Anon] '.$this->faker->sentence(4));
            $task->setContent($this->faker->paragraph(2));
            $task->setAuthor($anon);
            $task->isDone(false);
            $manager->persist($task);
        }

        $manager->flush();
    }


}
