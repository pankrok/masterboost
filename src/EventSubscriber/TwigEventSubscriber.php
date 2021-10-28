<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use App\Repository\PagesRepository;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $pageRepository;

    public function __construct(Environment $twig, PagesRepository $pageRepository)
    {
        $this->twig = $twig;
        $this->pageRepository = $pageRepository;
    }
    
    public function onControllerEvent(ControllerEvent $event)
    {
        $locale = $event->getRequest()->getLocale();
        $this->twig->addGlobal('pages', $this->pageRepository->getPages($locale));
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
