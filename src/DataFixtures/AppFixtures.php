<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use App\Entity\Viaje;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Definimos todos los datos en un solo array maestro
        $usuariosData = [
            'lolamento' => [
                'email' => 'lolamento@traveltour.com',
                'bio' => 'Apasionada por descubrir ciudades con historia, mercados locales y rutas tranquilas para perderme sin prisa.',
                'viajes' => [
                    [
                        'titulo' => 'Escapada lenta a Lisboa',
                        'destino' => 'Lisboa',
                        'duracion' => 5,
                        'presupuesto' => 620.00,
                        'contenido' => 'Un viaje relajado por barrios con alma, tranvías amarillos, miradores y cafés.',
                        'alojamiento' => 'Hotel boutique en Chiado, bien conectado.',
                        'gastronomia' => 'Bacalao, pasteles de nata y marisco fresco.',
                        'fecha' => new \DateTimeImmutable('-12 days'),
                    ],
                    [
                        'titulo' => 'Ruta cultural por Florencia',
                        'destino' => 'Florencia',
                        'duracion' => 4,
                        'presupuesto' => 890.00,
                        'contenido' => 'Arte, arquitectura y paseos al atardecer por una de las ciudades más bonitas de Italia.',
                        'alojamiento' => 'Alojamiento céntrico cerca del Duomo.',
                        'gastronomia' => 'Pasta fresca, bistecca alla fiorentina y helado artesanal.',
                        'fecha' => new \DateTimeImmutable('-8 days'),
                    ],
                    [
                        'titulo' => 'Naturaleza y calma en Kioto',
                        'destino' => 'Kioto',
                        'duracion' => 7,
                        'presupuesto' => 1450.00,
                        'contenido' => 'Templos, jardines, bosques de bambú y una experiencia muy cuidada con ritmo sereno.',
                        'alojamiento' => 'Ryokan tradicional para vivir una estancia más auténtica.',
                        'gastronomia' => 'Ramen, tofu y cocina kaiseki en restaurantes de barrio.',
                        'fecha' => new \DateTimeImmutable('-3 days'),
                    ],
                ]
            ],
            'fermin_trujillo' => [
                'email' => 'fermintrujillo@traveltour.com',
                'bio' => 'Viajar para mí significa comer bien, caminar mucho y volver con mil fotos y mejores anécdotas.',
                'viajes' => [] // Sin viajes por ahora
            ],
            'ines_table' => [
                'email' => 'inestable95@traveltour.com',
                'bio' => 'Busco destinos con encanto, alojamientos acogedores y experiencias auténticas que contar.',
                'viajes' => []
            ],
        ];

        foreach ($usuariosData as $nick => $uData) {
            // 1. Creamos y persistimos el Usuario
            $user = new Usuario();
            $user->setNickname($nick);
            $user->setEmail($uData['email']);
            $user->setRoles(['ROLE_USER']);
            $user->setFechaRegistro(new \DateTimeImmutable());
            $user->setBiografia($uData['bio']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            // 2. Creamos los Viajes de este usuario (si tiene)
            foreach ($uData['viajes'] as $vData) {
                $viaje = new Viaje();
                $viaje->setTitulo($vData['titulo']);
                $viaje->setDestino($vData['destino']);
                $viaje->setDuracion($vData['duracion']);
                $viaje->setPresupuesto($vData['presupuesto']);
                $viaje->setContenido($vData['contenido']);
                $viaje->setAlojamiento($vData['alojamiento']);
                $viaje->setGastronomia($vData['gastronomia']);
                $viaje->setFechaCreacion($vData['fecha']);
                
                // Asignamos el objeto usuario que acabamos de crear arriba
                $viaje->setIdUsuario($user);

                $manager->persist($viaje);
            }
        }

        // 3. Guardamos todo en la base de datos de una sola vez
        $manager->flush();
    }
}