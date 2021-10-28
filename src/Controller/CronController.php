<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServersRepository;
use App\Entity\ServerStatistics;
use Psr\Log\LoggerInterface;
use GameQ\GameQ as GQ;

class CronController extends AbstractController
{
    protected $gq;
    protected $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, ServersRepository $servers)
    {

        $this->gq = new GQ();
        $this->entityManager = $entityManager;
        $this->servers = $servers;
    }
    
    #[Route('/cron', name: 'cron')]
    public function index(): Response
    {
        $servers = $this->servers->getSeversForUpdate();
        $this->gq->addFilter('secondstohuman');
        foreach ($servers as $key => $server) {
            $this->gq->addServer([
                'type' => $server->getGame(),
                'host' => $server->getAddress(),
            ]);
            
            $result = $this->gq->process()[$server->getAddress()];
            if ($result['gq_online'] === true) {
                $server->setHostname($result["gq_hostname"]);
                $server->setPlayers($result["gq_numplayers"]);
                $server->setMaxPlayers($result["gq_maxplayers"]);
                $server->setMap($result["gq_mapname"]);
                $server->setStatus($result["gq_online"]);
                $server->setGameq($result["players"]);
                $server->setDateUpdate(
                    new \DateTime(
                        date("Y-m-d H:i:s")
                    )
                );
                
                $stats = new ServerStatistics();
                $stats->setServer($server);
                $stats->setCreatedAt(new \DateTimeImmutable(
                        date("Y-m-d H:i:s")
                    ));
                $stats->setPlayers($result["gq_numplayers"]);    
                
                try {
                    $this->entityManager->persist($server);
                    $this->entityManager->flush();
                    $this->entityManager->persist($stats);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    $logger->error('Servers cron error: ' . $e->getMessage());
                }
                $this->gq->clearServers();
            }
        }
        
        return new Response();
    }
    
    #[Route('/single-gq/{sid}', name: 'sgq')]
    public function singleGqQuery(int $sid) {
        $server = $this->servers->find($sid);
        
        $this->gq->addServer([
            'type' => $server->getGame(),
            'host' => $server->getAddress(),
        ]);
        
        $result = $this->gq->process()[$server->getAddress()];
        
        if ($result['gq_online'] === true) {
            $server->setHostname($result["gq_hostname"]);
            $server->setPlayers($result["gq_numplayers"]);
            $server->setMaxPlayers($result["gq_maxplayers"]);
            $server->setMap($result["gq_mapname"]);
            $server->setStatus($result["gq_online"]);
            $server->setGameq($result["players"]);
            $server->setDateUpdate(
                new \DateTime(
                    date("Y-m-d H:i:s")
                )
            );
            $this->entityManager->persist($server);
            $this->entityManager->flush();
        }
       
        return new Response();
    }
}
