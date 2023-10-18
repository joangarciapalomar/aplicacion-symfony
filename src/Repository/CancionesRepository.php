<?php

namespace App\Repository;

use App\Entity\Canciones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Canciones>
 *
 * @method Canciones|null find($id, $lockMode = null, $lockVersion = null)
 * @method Canciones|null findOneBy(array $criteria, array $orderBy = null)
 * @method Canciones[]    findAll()
 * @method Canciones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CancionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Canciones::class);
    }

    public function findByName($text): array{
        $qb = $this->createQueryBuilder('c')
        ->andWhere('c.nombre LIKE :text')
        ->setParameter('text', '%' . $text . '%')
        ->getQuery();
        return $qb->execute();
    }


//    /**
//     * @return Canciones[] Returns an array of Canciones objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Canciones
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
