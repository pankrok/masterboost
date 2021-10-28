<?php

namespace App\Repository;

use App\Entity\Servers;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Servers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Servers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Servers[]    findAll()
 * @method Servers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServersRepository extends ServiceEntityRepository
{
     public const PAGINATOR_PER_PAGE = 20;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Servers::class);
    }


    public function getServersPaginator(User $user, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.uid = :uid')
            ->setParameter('uid', $user)
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
    
    public function getSeversForUpdate()
    {
        $du = new \DateTime(
                    date(
                        "Y-m-d H:i:s", 
                        time() - ( 60 * 60  )
                    )
                );
        return $this->createQueryBuilder('s')
            ->andWhere('s.date_update < :val')
            ->setParameter('val', $du)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
        
    }
    // /**
    //  * @return Servers[] Returns an array of Servers objects
    //  */

    public function sumPlayers()
    {
        return $this->createQueryBuilder('s')
            ->select('SUM(s.players)')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function countAds()
    {
        $du = new \DateTime(
                    date(
                        "Y-m-d H:i:s", 
                        time() - ( 60 * 60 * 2 )
                    )
                );
        
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->andWhere('s.date_end_static > :val')
            ->orWhere('s.date_end_dynamic > :val')
            ->setParameter('val', $du)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function sumSlots()
    {
        return $this->createQueryBuilder('s')
            ->select('SUM(s.maxplayers)')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function countAll()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getStaticList()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.type = 2')
            ->andWhere('s.status = 1')
            ->orderBy('s.date_end_static', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getDynamicList()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.type = 1')
            ->andWhere('s.status = 1')
            ->orderBy('s.rate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
}
