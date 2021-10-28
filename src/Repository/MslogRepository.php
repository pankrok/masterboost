<?php

namespace App\Repository;

use App\Entity\Mslog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mslog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mslog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mslog[]    findAll()
 * @method Mslog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MslogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mslog::class);
    }

    // /**
    //  * @return Mslog[] Returns an array of Mslog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mslog
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
