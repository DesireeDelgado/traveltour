<?php

namespace App\Repository;

use App\Entity\Viaje;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Viaje>
 */
class ViajeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Viaje::class);
    }

    //    /**
    //     * @return Viaje[] Returns an array of Viaje objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Viaje
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    //Ordenar por los que mas favoritos tienen
    public function findTopPopulares(int $limit = 3): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.id_usuario', 'u')
            ->where('u.deletedAt IS NULL')
            ->leftJoin('v.favoritos', 'f')
            ->groupBy('v.id')
            ->orderBy('COUNT(f.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(?float $presupuesto, ?int $dias, ?string $lugar): array
    {
        $qb = $this->createQueryBuilder('v')
            ->join('v.id_usuario', 'u')
            ->andWhere('u.deletedAt IS NULL');

        if ($presupuesto !== null) {
            $qb->andWhere('v.presupuesto <= :presupuesto')
               ->setParameter('presupuesto', $presupuesto);
        }

        if ($dias !== null) {
            $qb->andWhere('v.duracion = :dias')
               ->setParameter('dias', $dias);
        }

        if ($lugar !== null && $lugar !== '') {
            // Buscamos si coincide con el destino o el nombre
            $qb->andWhere('LOWER(v.destino) LIKE LOWER(:lugar) OR LOWER(v.titulo) LIKE LOWER(:lugar)')
               ->setParameter('lugar', '%' . $lugar . '%');
        }

        return $qb->orderBy('v.id', 'DESC')->getQuery()->getResult();
    }
    // Método para obtener todos los destinos únicos de los viajes
    public function findAllDestinos(): array
    {
        $resultados = $this->createQueryBuilder('v')
            ->select('DISTINCT v.destino')
            ->join('v.id_usuario', 'u')
            ->where('v.destino IS NOT NULL')
            ->andWhere('u.deletedAt IS NULL')
            ->orderBy('v.destino', 'ASC')
            ->getQuery()
            ->getScalarResult();
        
        return array_column($resultados, 'destino');
    }

    /**
     * Busca destinos cuyo nombre contenga la cadena dada (para autocompletado).
     *
     * @return string[]
     */
    public function findDestinosByQuery(string $q, int $limit = 6): array
    {
        $resultados = $this->createQueryBuilder('v')
            ->select('DISTINCT v.destino')
            ->join('v.id_usuario', 'u')
            ->where('v.destino IS NOT NULL')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('LOWER(v.destino) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $q . '%')
            ->orderBy('v.destino', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getScalarResult();

        return array_column($resultados, 'destino');
    }
}

