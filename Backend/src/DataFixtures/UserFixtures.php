<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setName('User1');
        $user1->setSurname('TestNumber1');
        $user1->setEmail('email@email.com');
        $hashedPassword1 = $this->passwordHasher->hashPassword($user1, 'password1');
        $user1->setPassword($hashedPassword1);

        $user2 = new User();
        $user2->setName('User2');
        $user2->setSurname('TestNumber2');
        $user2->setEmail('test@test.com');
        $hashedPassword2 = $this->passwordHasher->hashPassword($user2, 'password2');
        $user2->setPassword($hashedPassword2);

        $manager->persist($user1);
        $manager->persist($user2);

        $manager->flush();
    }
}