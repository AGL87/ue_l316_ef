<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Generate one user
        $user = new User();
        $user->setEmail("user@demo.com");
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "123"));
        $user->setRoles(["USER"]);
        $manager->persist($user);

        $admin = new User();
        $admin->setEmail("admin@demo.com");
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, "123"));
        $admin->setRoles(["ADMIN"]);
        $manager->persist($admin);

        $manager->flush();
    }
}
