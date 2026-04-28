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
{
    $usuariosData = [
        ['nick' => 'lolamento', 'email' => 'lolamento@traveltour.com'],
        ['nick' => 'fermin_trujillo', 'email' => 'fermintrujillo@traveltour.com'],
        ['nick' => 'ines_table', 'email' => 'inestable95@traveltour.com'],
    ];

    foreach ($usuariosData as $data) {
        $user = new Usuario();
        $user->setNickname($data['nick']);
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);
        $user->setFechaRegistro(new \DateTimeImmutable());
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
    }

    $manager->flush();
    }
}
