<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use App\Repository\UserRepository;
use App\Repository\ServersRepository;
use App\Repository\PartnersRepository;
use Twig\Environment;

class StatsSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $statistics;
    private $partners;

    public function __construct(
        Environment $twig, 
        UserRepository $u,
        ServersRepository $s, 
        PartnersRepository $p,
    )
    {
        $this->twig = $twig;
        $cache = new FilesystemAdapter();
        $this->statistics = $cache->get('statistics', function (ItemInterface $item) use ($u, $s, $cache) {
            
            if (!$item->isHit()) {
                $item->expiresAfter(1800);
                $users = $u->countAll(); 
                $users = end($users);
                
                $sumPlayers = $s->sumPlayers();
                $sumPlayers = end($sumPlayers);
                $sumSlots = $s->sumSlots();
                $sumSlots = end($sumSlots);
                $countAds = $s->countAds();
                $countAds = end($countAds);
                $servers = $s->countAll();
                $servers = end($servers);

                $item->set([
                    'users' => $users,
                    'players' => $sumPlayers,
                    'slots' => $sumSlots,
                    'active_ads' => $countAds,
                    'servers' => $servers
                ]);
                $cache->save($item);
            }
            return $item->get();
            
        });
        
        $this->partners = $cache->get('partners', function (ItemInterface $item) use ($p, $cache) {
            
            if (!$item->isHit()) {
                $item->expiresAfter(3600);
                $item->set($p->getActive());
                $cache->save($item);
            }
            return $item->get();
            
        });
    }
    
    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('statistics', $this->statistics);
        $this->twig->addGlobal('partners', $this->partners);
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
