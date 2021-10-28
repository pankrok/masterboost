<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServersRepository;
use App\Repository\PagesRepository;
use App\Repository\VoteRepository;

class HomeController extends AbstractController
{
    #[Route('/{_locale}/', name: 'home')]
    public function index(AuthenticationUtils $authenticationUtils, ServersRepository $servers, VoteRepository $vote): Response
    {       
        $lastUsername = $authenticationUtils->getLastUsername();   
        $sotw = $vote->getTop();     
        $allServers = $vote->getServersByVotes(); 
        $static = $servers->getStaticList();
        $dynamic = $servers->getDynamicList();
        
        $response = 
            $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'last_username' => $lastUsername,
                'servers' => $allServers,
                'sotw' => $sotw,
                'static' => $static,
                'dynamic' => $dynamic,
            ]);
        $response->setSharedMaxAge(900);
        return $response;
    }
   
    #[Route('/{_locale}/list/static', name: 'static_list')]
    public function staticList(AuthenticationUtils $authenticationUtils, ServersRepository $servers, Request $request)
    {
        $response = ( $this->render('home/list.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'list' => 'static',
            'static' => $servers->getStaticList(),
        ]));
        
        $response->setSharedMaxAge(900); //15min
        return $response;
    }
    
     #[Route('/{_locale}/list/dynaic', name: 'dynamic_list')]
    public function dynamicList(AuthenticationUtils $authenticationUtils, ServersRepository $servers, Request $request)
    {
        $response = ( $this->render('home/list.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'list' => 'dynamic',
            'dynamic' => $servers->getDynamicList(),
        ]));
        
        $response->setSharedMaxAge(900); // 15min
        return $response;
    }
   
    #[Route('/{_locale}/list/all', name: 'all_list')]
    public function allList(AuthenticationUtils $authenticationUtils, VoteRepository $vote, Request $request)
    {
        $response = ( $this->render('home/list.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'list' => 'all',
            'servers' => $vote->getServersByVotes(10000),
        ]));
        
        $response->setSharedMaxAge(900); //15min
        return $response;
    }
   
    #[Route('/', name: 'base')]
    public function init(Request $request): Response
    {
        $locale = $request->getLocale();
        
        return $this->redirectToRoute('home', ['_locale' => $locale]);
    }
    
    #[Route('/{_locale}/p/{slug}', name: 'page')]
    public function page(string $slug, AuthenticationUtils $authenticationUtils, PagesRepository $pages, Request $request)
    {
        $response = ( $this->render('home/page.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'page' => $pages->findOneBy(['name' => $slug, 'locale' => $request->getLocale()]),
        ]));
        
        $response->setSharedMaxAge(86400); //24h
        return $response;
    }
}
