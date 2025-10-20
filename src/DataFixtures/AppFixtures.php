<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        $dnepr = new Conference();
        $dnepr->setCity('Dnepr');
        $dnepr->setYear(2025);
        $dnepr->setInternational(false);
        $manager->persist($dnepr);

        $kiev = new Conference();
        $kiev->setCity('Kiev');
        $kiev->setYear(2025);
        $kiev->setInternational(true);
        $manager->persist($kiev);

        $comment1 = new Comment();
        $comment1->setAuthor('John');
        $comment1->setText('This is a comment');
        $comment1->setEmail('jhony@gmail.com');
        $comment1->setCreatedAtValue();
        $comment1->setConference($dnepr);
        $manager->persist($comment1);

        $manager->flush();
    }
}
