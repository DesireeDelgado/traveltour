<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Usuario>
 */
class UsuarioRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Usuario) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Busca usuarios cuyo nickname contenga la cadena dada (para autocompletado).
     * Excluye usuarios con soft-delete.
     *
     * @return Usuario[]
     */
    public function findByNicknameQuery(string $q, int $limit = 6): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.nickname) LIKE LOWER(:q)')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('u.baneado = false')
            ->setParameter('q', '%' . $q . '%')
            ->orderBy('u.nickname', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
