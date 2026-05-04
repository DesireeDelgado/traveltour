<?php

namespace App\Command;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:purge-users',
    description: 'Elimina definitivamente los usuarios que solicitaron borrar su cuenta hace más de 30 días.',
)]
class AppPurgeUsersCommand extends Command
{
    public function __construct(
        private UsuarioRepository $usuarioRepository,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $params
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();

        // Calculamos la fecha límite: hace 30 días
        $fechaLimite = new \DateTimeImmutable('-30 days');

        // Obtenemos los usuarios cuyo deletedAt sea anterior o igual a la fecha límite
        $qb = $this->usuarioRepository->createQueryBuilder('u');
        $usuariosABorrar = $qb->where('u.deletedAt IS NOT NULL')
                              ->andWhere('u.deletedAt <= :fechaLimite')
                              ->setParameter('fechaLimite', $fechaLimite)
                              ->getQuery()
                              ->getResult();

        if (empty($usuariosABorrar)) {
            $io->success('No hay usuarios para purgar hoy.');
            return Command::SUCCESS;
        }

        $projectDir = $this->params->get('kernel.project_dir');
        $contador = 0;

        foreach ($usuariosABorrar as $usuario) {
            $urlFoto = $usuario->getUrlFotoPerfil();
            
            // Si el usuario tiene foto, obtenemos el nombre del archivo y la eliminamos físicamente
            if ($urlFoto) {
                // El formato de la URL es /imagen/perfil/nombrearchivo.ext, por lo que extraemos el nombre
                $filename = basename($urlFoto);
                $filepath = $projectDir . '/storage/profiles/' . $filename;
                
                if ($filesystem->exists($filepath)) {
                    $filesystem->remove($filepath);
                }
            }

            $this->entityManager->remove($usuario);
            $contador++;
        }

        // Aplicamos la eliminación dura en base de datos
        $this->entityManager->flush();

        $io->success(sprintf('Se han eliminado definitivamente %d usuario(s) y sus archivos asociados.', $contador));

        return Command::SUCCESS;
    }
}
