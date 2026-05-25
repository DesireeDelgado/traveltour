<?php

namespace App\DataFixtures;

use App\Entity\Imagen;
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
                'foto' => '/img/profiles/fotoPerfilLolamento.jpg',
                'viajes' => [
                    [
                        'titulo' => 'Y nos fuimos pa Madriiiiiiid',
                        'destino' => 'Madrid',
                        'duracion' => 4,
                        'presupuesto' => 520.00,
                        'contenido' => 'Nuestro viaje a Madrid empezó con un clásico indispensable: pasear por el Parque del Retiro. Alquilamos una barca en el estanque grande, un plan típico pero que siempre vale la pena, y luego visitamos el Palacio de Cristal, que estaba precioso con la luz de la mañana. Desde allí, fuimos a la Puerta del Sol para hacernos la foto con el Oso y el Madroño y el Kilómetro Cero.
                        Para estos dias decidimos buscar algo más original. Nos recomendaron visitar el Andén 0, la estación de metro fantasma de Chamberí. Está conservada tal cual estaba en los años 60, con los carteles publicitarios antiguos, y es una experiencia supercuriosa. Por la tarde, fuimos a ver el atardecer al Templo de Debod, un templo egipcio real en medio de Madrid. Las vistas de la Casa de Campo desde allí son increíbles cuando el sol se pone.',
                        'alojamiento' => 'Para nuestro alojamiento, elegimos el barrio de Chueca. Es una zona con muchísima vida, tiendas originales y muy cerca de todo. Nos quedamos en un hostal que se llama Hostal Boutique Luna Llena. Cómo llegar: Es superaccesible. La mejor opción es el metro: la estación Chueca(Línea 5) te deja a dos minutos andando de la puerta. Desde el aeropuerto, tardas unos 40 minutos combinando la línea 8 hasta Nuevos Ministerios y luego la 10 hasta Alonso Martínez, que está a una parada de Chueca.',
                        'gastronomia' => 'La gastronomía de Madrid nunca defrauda. Por supuesto, lo primero que hicimos fue ir a por un bocata de calamares a la Plaza Mayor (¡y no a Plaza España, que casi nos equivocamos!). Compramos uno en un bar mítico y nos lo comimos sentados en los bancos de la plaza, un imprescindible.
                              Para cenar, fuimos a Bestial By Rosi La Loca, un restaurante supercéntrico que está muy de moda. La decoración es una locura, como estar dentro de un sueño, y la comida estaba riquísima, muy original.
                                 Pero la joya del viaje fue encontrar Calle 365. Es un pub clandestino (speakeasy) al que se entra por la parte trasera de una tienda. Para conseguir la contraseña de entrada tuvimos que buscar un poco, pero valió la pena. El ambiente es increíble y los cócteles son de autor. ¡Toda una experiencia!',
                        'fecha' => new \DateTimeImmutable('-12 days'),
                        'imagenes' => [
                            '/img/viajes/lolamento/viaje1/viaje1_1.jpg',
                            '/img/viajes/lolamento/viaje1/viaje1_2.jpg',
                            '/img/viajes/lolamento/viaje1/viaje1_3.jpg',
                            '/img/viajes/lolamento/viaje1/viaje1_4.JPG',
                            '/img/viajes/lolamento/viaje1/viaje1_5.JPG',
                        ]
                    ],
                    [
                        'titulo' => 'Escapada a Brujas',
                        'destino' => 'Brujas',
                        'duracion' => 3,
                        'presupuesto' => 600.00,
                        'contenido' => 'Brujas es una ciudad que parece sacada de un cuento. De día es preciosa, con sus canales llenos de cisnes y las fachadas de colores de la Plaza Mayor (Grote Markt). Dimos un paseo en barco por los canales y subimos a la torre del Belfort para tener las mejores vistas. Pero lo mejor llega al caer el sol: de noche, con la iluminación estratégica en los puentes medievales y el muelle del Rosario (Rozenhoedkaai), la ciudad se vuelve completamente mágica y súper romántica.',
                        'alojamiento' => 'Nos alojamos en el Hotel Notre Dame, un hotelito familiar pequeño pero muy acogedor, situado justo al lado del Museo Groeninge. La ubicación era perfecta porque podías ir andando a cualquier sitio en menos de 10 minutos. La habitación tenía vigas de madera vistas, una cama comodísima y unas vistas preciosas a un canal lateral. Para llegar desde la estación de tren de Brujas, solo tuvimos que coger el autobús línea 1 y bajarnos en la parada del centro.',
                        'gastronomia' => 'En Bélgica es obligatorio probar los "moules-frites" (mejillones con patatas fritas), un plato espectacular y súper sabroso que cenamos en un restaurante local cerca de la plaza. Pero el verdadero paraíso fue la chocolatería The Chocolate Line; es una parada obligatoria si vas a Brujas. Tienen bombones con sabores súper originales (¡incluso de curry o bacon!) y el olor que hay al entrar a la tienda es una auténtica locura.',
                        'fecha' => new \DateTimeImmutable('-8 days'),
                        'imagenes' => [
                            '/img/viajes/lolamento/viaje2/viaje 2_1.jpg',
                            '/img/viajes/lolamento/viaje2/viaje2_2.jpg',
                            '/img/viajes/lolamento/viaje2/viaje2_3.jpg',
                            '/img/viajes/lolamento/viaje2/viaje2_4.jpg',
                        ]
                    ],
                    [
                        'titulo' => 'Viaje a Barcelona',
                        'destino' => 'Barcelona',
                        'duracion' => 4,
                        'presupuesto' => 700.00,
                        'contenido' => 'Barcelona nos ha enamorado con su arquitectura. Dedicamos el primer día entero a la obra de Antoni Gaudí, visitando el interior de la Sagrada Familia, que tiene una luz impresionante, y paseando por el Park Güell. El segundo día preferimos algo más relajado y caminamos por el barrio Gótico, perdiéndonos por sus callejones medievales hasta llegar al Born. Acabamos la tarde viendo el atardecer desde los Bunkers del Carmel, que ofrecen una panorámica espectacular de toda la ciudad con el mar de fondo.',
                        'alojamiento' => 'Elegimos el Generator Hostel Barcelona, un alojamiento moderno situado en el barrio de Gracia, una zona genial llena de plazas con terrazas y ambiente local. La habitación era compartida pero super amplia, limpia y con unas taquillas enormes para dejar las mochilas. Para llegar desde la estación de Sants fue facilísimo, solo tuvimos que coger la línea 5 del metro hasta la parada de Verdaguer y caminar unos cinco minutos por la calle Córcega.',
                        'gastronomia' => 'La experiencia gastronómica ha sido de diez. Desayunamos unos churros con chocolate brutales en una granja tradicional de la calle Petritxol. Para comer fuimos al Mercado de la Boquería a tapear unas croquetas y unas patatas bravas espectaculares en los puestos del fondo. La última noche nos dimos un capricho y cenamos una paella de marisco riquísima en un restaurante del paseo Joan de Borbó, justo al lado del puerto de la Barceloneta.',
                        'fecha' => new \DateTimeImmutable('-3 days'),
                        'imagenes' => [
                            '/img/viajes/lolamento/viaje3/viaje3_1.jpg',
                            '/img/viajes/lolamento/viaje3/viaje3_2.jpg',
                            '/img/viajes/lolamento/viaje3/viaje3_3.jpg',
                        ]
                    ],
                ]
            ],
            'fermin_trujillo' => [
                'email' => 'fermintrujillo@traveltour.com',
                'bio' => 'Viajar para mí significa comer bien, caminar mucho y volver con mil fotos y mejores anécdotas.',
                'viajes' => [
                    [
                        'titulo' => 'Sabor y calle en Ciudad de México',
                        'destino' => 'Ciudad de México',
                        'duracion' => 6,
                        'presupuesto' => 800.00,
                        'contenido' => 'Tacos al pastor, mercados coloridos y barrios con mucha vida para explorar a pie.',
                        'alojamiento' => 'Hostal céntrico con ambiente joven.',
                        'gastronomia' => 'Tacos, quesadillas, elote y aguas frescas.',
                        'fecha' => new \DateTimeImmutable('-10 days'),
                         'imagenes' => [
                            '/img/viajes/fermin/viaje6/viaje6_1.jpg',
                            '/img/viajes/fermin/viaje6/viaje6_2.jpg',
                            '/img/viajes/fermin/viaje6/viaje6_3.jpg',
                        ]
                    ],
                    [
                        'titulo' => 'Paseos y sabores en Barcelona',
                        'destino' => 'Barcelona',
                        'duracion' => 4,
                        'presupuesto' => 700.00,
                        'contenido' => 'Paseos por el Born, tapas en la Barceloneta y una mezcla perfecta de cultura y playa.',
                        'alojamiento' => 'Apartamento turístico cerca de la Sagrada Familia.',
                        'gastronomia' => 'Tapas, paella y vermut casero.',
                        'fecha' => new \DateTimeImmutable('-7 days'),
                          'imagenes' => [
                            '/img/viajes/fermin/viaje7/viaje7_1.jpg',
                            '/img/viajes/fermin/viaje7/viaje7_2.jpg',
                            '/img/viajes/fermin/viaje7/viaje7_3.jpg',
                        ]
                    ],
                ] 
            ],
            'ines_table' => [
                'email' => 'inestable95@traveltour.com',
                'bio' => 'Busco destinos con encanto, alojamientos acogedores y experiencias auténticas que contar.',
                'foto' => '/img/profiles/InesTableFotoPerfil.JPG',
                'viajes' => [
                    [
                        'titulo' => 'Encanto rural en la Toscana',
                        'destino' => 'Toscana',
                        'duracion' => 6,
                        'presupuesto' => 1100.00,
                        'contenido' => 'Pueblos medievales, viñedos y una escapada perfecta para desconectar.',
                        'alojamiento' => 'Casa rural con piscina y vistas a los campos.',
                        'gastronomia' => 'Vino Chianti, pasta casera y postres tradicionales.',
                        'fecha' => new \DateTimeImmutable('-15 days'),
                        'imagenes' => [
                            '/img/viajes/inestable/viaje5/viaje5_1.jpg',
                            '/img/viajes/inestable/viaje5/viaje5_2.jpg',
                            '/img/viajes/inestable/viaje5/viaje5_3.jpg',
                        ]
                    ],
                        [
                            'titulo' => 'Aventura urbana en Berlín',
                            'destino' => 'Berlín',
                            'duracion' => 4,
                            'presupuesto' => 750.00,
                            'contenido' => 'Historia, arte urbano y una escena cultural vibrante para explorar.',
                            'alojamiento' => 'Hostel moderno en el centro de la ciudad.',
                            'gastronomia' => 'Currywurst, doner kebab y cerveza artesanal.',
                            'fecha' => new \DateTimeImmutable('-5 days'),
                            'imagenes' => [
                            '/img/viajes/inestable/viaje4/viaje4_1.jpg',
                            '/img/viajes/inestable/viaje4/viaje4_2.jpg',
                            '/img/viajes/inestable/viaje4/viaje4_3.jpg',
                            ]
                        ],
                ]
            ],
            'admin' => [
                'email' => 'admin@traveltour.com',
                'roles' => ['ROLE_ADMIN'],
            ],
        ];

        foreach ($usuariosData as $nick => $uData) {
            // 1. Creamos y persistimos el Usuario
            $user = new Usuario();
            $user->setNickname($nick);
            $user->setEmail($uData['email']);
            $user->setRoles($uData['roles'] ?? ['ROLE_USER']);
            $user->setFechaRegistro(new \DateTimeImmutable());
            $user->setBiografia($uData['bio'] ?? null);

            if (isset($uData['foto'])) {
                $user->setUrlFotoPerfil($uData['foto']);
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            // 2. Creamos los Viajes de este usuario (si tiene)
            foreach ($uData['viajes'] ?? [] as $vData) {
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

                // Si tiene imagenes, se las asignamos
                if (isset($vData['imagenes'])) {
                    foreach ($vData['imagenes'] as $imgUrl) {
                        $filename = basename($imgUrl);
                        
                        // Copiamos la imagen de public a storage/viajes para simular que ha sido subida
                        $sourceFile = __DIR__ . '/../../public' . $imgUrl;
                        $targetDir = __DIR__ . '/../../storage/viajes';
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0777, true);
                        }
                        $targetFile = $targetDir . '/' . $filename;
                        
                        if (file_exists($sourceFile)) {
                            copy($sourceFile, $targetFile);
                        }

                        $imagen = new Imagen();
                        // Guardamos solo el nombre del archivo, que es lo que espera la ruta app_imagen_viaje
                        $imagen->setUrlPath($filename);
                        $imagen->setIdViaje($viaje);
                        $manager->persist($imagen);
                    }
                }
            }
        }

        // 3. Guardamos todo en la base de datos de una sola vez
        $manager->flush();
    }
}