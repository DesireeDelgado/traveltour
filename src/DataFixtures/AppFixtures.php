<?php

namespace App\DataFixtures;

use App\Entity\Usuario; // Asegúrate de que tu entidad se llame Usuario
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {   //FIXTURES USUARIOS TO WAPOS
        $user = new Usuario();
        $user->setNickname('lolamento');
        $user->setEmail('lolamento@traveltour.com');
        $user->setRoles(['ROLE_USER']);
        $user->setFechaRegistro(new \DateTimeImmutable());

        // Encriptacion cont
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            '123456'
        );
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}
