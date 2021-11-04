<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ServersRepository;
use App\Repository\WalletRepository;
use App\Repository\PromoCodesRepository;
use App\Form\WalletFormType;
use App\Form\PayemntType;
use App\Form\MicroSmsType;
use App\Form\UserTransferType;
use App\Entity\Servers;
use App\Entity\Wallet;
use App\Form\AddServerType;
use App\Controller\CronController;
use Symfony\Component\Yaml\Yaml;

/**
 * @Route("/{_locale}")
 */
class UserPanelController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/user/panel/{slug}/{offset<\d+>?0}', name: 'user_panel')]
    public function index(string $slug, int $offset, UserRepository $user, Request $request, ServersRepository $serversRepository): Response
    {          
        if ($user->find($slug)->getId() === $this->getUser()->getId()) {
            $can_edit = true;
            $offset = max(0, $offset);
            $paginator = $serversRepository->getServersPaginator($this->getUser(), $offset);
        } else {
            $can_edit = false;
        }
        
        return $this->render('user_panel/index.html.twig', [
            'controller_name' => 'User Panel',
            'can_edit' => $can_edit,
            'servers' => $paginator,
            'previous' => $offset - ServersRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ServersRepository::PAGINATOR_PER_PAGE),
        ]);
    }
    
    #[Route('/user/add-server/{slug}/', name: 'user_panel_server')]
    public function addServer(string $slug, UserRepository $user, Request $request, CronController $c): Response 
    {
        
        
        if ($user->find($slug)->getId() === $this->getUser()->getId()) {
            $can_edit = true;
            $server = new Servers();
            $form = $this->createForm(AddServerType::class, $server);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $server->setUid($this->getUser());
                $server->setDateCreate = time();
                $server->setStatus(0);
                $this->entityManager->persist($server);
                $this->entityManager->flush();
                
                $c->singleGqQuery($server->getId());
            }
            
        } else {
            $can_edit = false;
        }
        
        return $this->render('user_panel/server.html.twig', [
            'controller_name' => 'User Server Panel',
            'can_edit' => $can_edit,
            'server_form' => $form->createView(),
        ]);
    }
    
    #[Route('/user/buy-ad/{slug}/{offset<\d+>?0}', name: 'user_panel_buy')]
    public function buyAds(string $slug, int $offset, UserRepository $user, ServersRepository $serversRepository): Response 
    {
        if ($user->find($slug)->getId() === $this->getUser()->getId()) {
            $can_edit = true;
            $offset = max(0, $offset);
            $paginator = $serversRepository->getServersPaginator($this->getUser(), $offset);
            
        } else {
            $can_edit = false;
        }
        
        return $this->render('user_panel/buy.html.twig', [
            'controller_name' => 'User buy ad',
            'can_edit' => $can_edit,
            'servers' => $paginator,
            'previous' => $offset - ServersRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ServersRepository::PAGINATOR_PER_PAGE),

        ]);
    }
    
    #[Route('/user/wallet/{slug}', name: 'user_panel_wallet')]
    public function wallet(string $slug, UserRepository $user, Request $request, PromoCodesRepository $promoCodesRepository, WalletRepository $walletRepository): Response 
    {
        if ($user->find($slug)->getId() === $this->getUser()->getId()) {
            $can_edit = true;     
            $form = $this->createForm(WalletFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();
                $promoCode = $promoCodesRepository->findBy(['code' => $task["promocode"]]);
                if ($promoCode) {
                    $promoCode = $promoCode[0];
                    if ($promoCode->getUsed() === null) {
                        $promoCode->setUsed($this->getUser());
                        $this->getUser()->addToWallet($promoCode->getAmount());
                        $this->entityManager->persist($promoCode);
                        $this->entityManager->flush();
                        
                        $this->entityManager->persist($this->getUser());
                        $this->entityManager->flush();
                        
                        $wallet = new Wallet();
                        $wallet->setOperation($promoCode->getCode());
                        $wallet->setUid($this->getUser());
                        $wallet->setCreatedAt(new \Datetime);
                        $wallet->setCrc('none');
                        $wallet->setStatus(1);
                        $wallet->setAmount($promoCode->getAmount());
                        $wallet->setPaid($promoCode->getAmount());
                        $wallet->setType('promocode');
                        $wallet->setOrderId($promoCode->getId());
                        
                        $this->entityManager->persist($wallet);
                        $this->entityManager->flush();
                        
                    } else {
                        $this->addFlash(
                            'error',
                            'This code was used already'
                        );
                    }
                    
                } else {
                    $this->addFlash(
                            'error',
                            'code dose not exist'
                        );
                }
                
            }
            
            $tpay_form = $this->createForm(PayemntType::class,[],[
                'action' => $this->generateUrl('payment_basket'),
                'method' => 'POST',
            ]   );
            
            $ms_form = $this->createForm(MicroSmsType::class,[],[
                'action' => $this->generateUrl('payment_sms'),
                'method' => 'POST',
            ]  );
            
            $userTransfer_form = $this->createForm(UserTransferType::class,[],[
                'action' => $this->generateUrl('payment_transfer'),
                'method' => 'POST',
            ]);
            
            $tplcfg = Yaml::parseFile($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR  . 'config' . DIRECTORY_SEPARATOR  .'tpay.yaml');
            $mslcfg = Yaml::parseFile($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR  . 'config' . DIRECTORY_SEPARATOR  .'microsms.yaml');

        } else {
            $can_edit = false;
        }
        
        return $this->render('user_panel/wallet.html.twig', [
            'controller_name' => 'User wallet',
            'can_edit' => $can_edit,
            'tp_active' => $tplcfg['active'],
            'ms' => $mslcfg,
            'ut_form' => $userTransfer_form->createView(),
            'wallet_form' => $form->createView(),
            'tpay_form' => $tpay_form->createView(),
            'ms_form' => $ms_form->createView(),
            'transactions' => $walletRepository->findByUser($this->getUser()->getId()),
        ]);
    }
}
