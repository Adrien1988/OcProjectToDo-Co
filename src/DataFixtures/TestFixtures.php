<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{


    public static function getGroups(): array
    {
        return ['test'];
    }


    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }


    public function load(ObjectManager $om): void
    {
        /* --- Admin --- */
        $admin = (new User())
            ->setUsername('admin')
            ->setEmail('a@a.fr')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'root'));
        $om->persist($admin);

        /* --- Utilisateurs simples --- */
        $u1 = (new User())->setUsername('user1')->setEmail('u1@ex.fr');
        $u1->setPassword($this->hasher->hashPassword($u1, 'password'));
        $u2 = (new User())->setUsername('user2')->setEmail('u2@ex.fr');
        $u2->setPassword($this->hasher->hashPassword($u2, 'password'));
        $om->persist($u1);
        $om->persist($u2);

        /* --- Tâche de user1 --- */
        $t1 = new Task();
        $t1->setTitle('Ma tâche');
        $t1->setContent('Contenu tâche user1');
        $t1->setAuthor($u1);
        $om->persist($t1);

        /* --- Tâche “anonyme” --- */
        $anon = (new User())->setUsername('anonyme')->setEmail('anon@ex.fr');
        $anon->setPassword($this->hasher->hashPassword($anon, 'x'));
        $om->persist($anon);

        $t2 = new Task();
        $t2->setTitle('Orphan');
        $t2->setContent('Contenu tâche anonyme');
        $t2->setAuthor($anon);
        $om->persist($t2);

        $om->flush();
    }


}
