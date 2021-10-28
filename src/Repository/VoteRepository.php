<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function getServersByVotes($max = 20) {
            // $datatime = new \Datetime();
            // $datatime->setTimestamp(0);
            
            return $this->createQueryBuilder('v')
            ->groupby('v.sid')
            ->orderBy('count(:ca)', 'DESC')
            ->setParameter('ca', '*')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    public function getTop()
    {
        $time = time() - (60*60*24*7);
        $datatime = new \Datetime();
        $datatime->setTimestamp($time);

        return $this->createQueryBuilder('v')
            ->where('v.createdAt > :ca')
            ->groupBy('v.sid')
            ->setParameter('ca', $datatime)
            ->orderBy('count(:co)', 'DESC')
            ->setParameter('co', '*')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }
    public function checkVote(int $time, $uid,string $ip,int $sid): ?Vote
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :uid')
            ->setParameter('uid', $uid)
            ->orWhere('v.vote_ip = :ip')
            ->setParameter('ip', $ip)
            ->andWhere('v.createdAt > :time')
            ->setParameter('time', date("Y-m-d H:i:s", $time))
            ->andWhere('v.sid = :sid')
            ->setParameter('sid', $sid)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
