<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Classe permettant d'initialiser des utilisateurs standards dans la base de données
 */
class UserFixtures extends Fixture
{
    /**
     * L'objet permettant de hacher les mots de passes
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * Constructeur de la classe
     * @param UserPasswordHasherInterface $passwordHasher Injecté par Symfony
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Chargement d'un utilisateur admin dans la base de données
     * @param ObjectManager $manager Injecté par Symfony
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $password = 'admin';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->flush();
    }
}