<?php

namespace App\Repository;

use App\Entity\ServerStatistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServerStatistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerStatistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerStatistics[]    findAll()
 * @method ServerStatistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerStatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerStatistics::class);
    }

    // /**
    //  * @return ServerStatistics[] Returns an array of ServerStatistics objects
    //  */

    public function stats($value)
    {
         $du = new \DateTime(
                    date(
                        "Y-m-d H:i:s", 
                        time() - ( 60 * 60 * 24)
                    )
                );
        
        $u =  $this->createQueryBuilder('s')
            ->andWhere('s.server = :val')
            ->setParameter('val', $value)
            ->andWhere('s.createdAt > :v')
            ->setParameter('v', $du)
            ->select(['s.createdAt', 's.players'])
            ->getQuery()
            ->getArrayResult()
        ;
        return json_encode($u);
    }


    /*
    public function findOneBySomeField($value): ?ServerStatistics
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
