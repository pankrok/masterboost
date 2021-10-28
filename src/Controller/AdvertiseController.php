<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\ServersRepository;
use App\Form\StaticAdType;
use App\Form\DynamicAdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

#[Route('/{_locale}/user/advertise', name: 'ad_')]
class AdvertiseController extends AbstractController
{
    
    protected $entityManager;
    protected $config;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; 
        
    }
    
    #[Route('/dynamic/{slug}/{sid}', name: 'dynamic')]
    public function dynamicAd(int $slug, int $sid, Request $request, ServersRepository $serversRepository): Response
    {
        $this->config = Yaml::parseFile($this->getParameter('kernel.project_dir') . '/config/boost.yaml');
        $user = $this->getUser();
        $f = null;
        $server = $serversRepository->find($sid);
        $options =  [
                'credits' => 0,
                'days' => 0,
                'rate' => '0.00'
            ];
        if($server->getType() === 1) {
            
            $now = time(); // or your date as well
            $your_date = $server->getDateEndDynamic() ? strtotime($server->getDateEndDynamic()->format('Y-m-d H:i:s')) : $now;
            $datediff  = round(($your_date - $now) / (60 * 60 * 24));
            $credits = $datediff * $server->getRate();
            $options = [
                'credits' => $credits,
                'days' => round($datediff, 0),
                'rate' => $server->getRate(),
            ];

        }
        
        if ($slug === $user->getId()) {    
            $form = $this->createForm(DynamicAdType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($user->getWallet() < intval($form->get('credits')->getData())) {
                    $this->addFlash('error', 'you dont hav enough money');
                    return $this->redirectToRoute('ad_dynamic', ['slug' => $slug, 'sid' => $sid]);
                }
                
                $task = $form->getData();
                if ($form->get('days')->getData() < $this->config['minDays']) {
                    $f = 'minDays';
                    $this->addFlash(
                        'danger',
                        ['msg' => 'minimal days for ads is: ',  'val' => $this->config['minDays']]
                    );
                    return $this->redirectToRoute('ad_dynamic', ['slug' => $slug, 'sid' => $sid]);
                }
                
                if ($form->get('credits')->getData() < $this->config['minCredits']) {
                    $this->addFlash(
                        'danger',
                        ['msg' => 'minimal credits for ads is: ',  'val' => $this->config['minCredits']]
                    );
                    return $this->redirectToRoute('ad_dynamic', ['slug' => $slug, 'sid' => $sid]);
                }
                
                $date = new \DateTime(); 
                $currentDateTimestamp = $server->getDateEndDynamic() ? $server->getDateEndDynamic()->getTimestamp() : time();
                $endDate = $currentDateTimestamp + (intval($form->get('days')->getData()) * 60 * 60 * 24);
                $date->setTimestamp($endDate);
                $priority = round(intval($form->get('credits')->getData() + $options['credits']) / intval($form->get('days')->getData() + $options['days']), 2);
                if ($priority < $this->config['minRate']) {
                    $this->addFlash(
                        'danger',
                        ['msg' => 'minimal rate for ads is: ', 'val' => $this->config['minRate']]
                    );
                    return $this->redirectToRoute('ad_dynamic', ['slug' => $slug, 'sid' => $sid]);
                }
                
                $server->setType(1);
                $server->setRate($priority);
                $server->setDateEndDynamic($date);
                
                $this->entityManager->persist($server);
                $this->entityManager->flush();
                
                $user->subtractToWallet(floatval($form->get('credits')->getData()));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
              
                $this->addFlash(
                        'success',
                        'the ads will start now!'
                    );
            }
        } 
        
        return $this->render('advertise/dynamic.html.twig', [
            'controller_name' => 'DynamicAdvertiseController',
            'static_form' => $form->createView(),
            'options' => $options,
            'name' => $server->getHostname(),
        ]);
    }
    
    #[Route('/static/{slug}/{sid}', name: 'static')]
    public function staticAd(int $slug, int $sid, Request $request, ServersRepository $serversRepository): Response
    {
        $this->config = Yaml::parseFile($this->getParameter('kernel.project_dir') . '/config/boost.yaml');
        $user = $this->getUser();
        $f = null;
        $server = $serversRepository->find($sid);
        $options =  [
                'credits' => 0,
                'days' => 0,
                'rate' => '0.00'
            ];
        if($server->getType() === 2) {
            
            $now = time(); // or your date as well
            $your_date = $server->getDateEndStatic() ? strtotime($server->getDateEndStatic()->format('Y-m-d H:i:s')) : $now;
            $datediff  = round(($your_date - $now) / (60 * 60 * 24));
            $credits = $datediff * $server->getRate();
            $options = [
                'days' => round($datediff, 0),
            ];

        }
        
        if ($slug === $user->getId()) {    
            $form = $this->createForm(StaticAdType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($user->getWallet() < intval($form->get('days')->getData() * $this->config['staticCostPerDay'])) {
                    $this->addFlash('error', 'you dont hav enough money');
                    return $this->redirectToRoute('ad_static', ['slug' => $slug, 'sid' => $sid]);
                }
                
                $task = $form->getData();
                if ($form->get('days')->getData() < $this->config['minDays']) {
                    $f = 'minDays';
                    $this->addFlash(
                        'danger',
                        ['msg' => 'minimal days for ads is: ',  'val' => $this->config['minDays']]
                    );
                    return $this->redirectToRoute('ad_static', ['slug' => $slug, 'sid' => $sid]);
                }

                $date = new \DateTime(); 
                $currentDateTimestamp = $server->getDateEndDynamic() ? $server->getDateEndDynamic()->getTimestamp() : time();
                $endDate = $currentDateTimestamp + (intval($form->get('days')->getData()) * 60 * 60 * 24);
                $date->setTimestamp($endDate);
                    
                $server->setType(2);
                $server->setDateEndStatic($date);
                
                $this->entityManager->persist($server);
                $this->entityManager->flush();
                
                $user->subtractToWallet(floatval($form->get('days')->getData() * $this->config['staticCostPerDay']));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
              
                $this->addFlash(
                        'success',
                        'the static ads will start now!'
                    );
            }
        } 
        
        return $this->render('advertise/static.html.twig', [
            'controller_name' => 'DynamicAdvertiseController',
            'cost_per_day' => $this->config['staticCostPerDay'],
            'static_form' => $form->createView(),
            'options' => $options,
            'name' => $server->getHostname(),
        ]);
    }
}
