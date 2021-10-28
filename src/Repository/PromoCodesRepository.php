<?php

namespace App\Repository;

use App\Entity\PromoCodes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PromoCodes|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoCodes|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoCodes[]    findAll()
 * @method PromoCodes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoCodesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoCodes::class);
    }

    // /**
    //  * @return PromoCodes[] Returns an array of PromoCodes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PromoCodes
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
