<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\TpayController;
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Form\PayemntType;

#[Route('/{_locale}/payment', name: 'payment_')]

class PaymentController extends AbstractController
{
    
    protected $entityManager;
    protected $userRepository;
    protected $walletRepository;
    protected $tPay;
    protected $router;
    protected $params;
    
    public function __construct(
        RequestStack $requestStack, 
        EntityManagerInterface $entityManager,
        WalletRepository $walletRepository, 
        UserRepository $userRepository, 
        ContainerBagInterface $params, 
        UrlGeneratorInterface $router)
    {
   
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->router = $router;
        $this->params = $params;
        $this->tPay = new TpayController(
            $params->get('kernel.project_dir'),
            $router->generate('base'),
            $requestStack->getCurrentRequest()->getLocale(),
        );
        
    }
    
    
    #[Route('/basket', name: 'basket')]
    public function basket(Request $request): Response
    {
        $user = $this->getUser();
        $tpayForm = $this->createForm(PayemntType::class);
        $tpayForm->handleRequest($request);
        if ($tpayForm->isSubmitted() && $tpayForm->isValid()) {
            $task = $tpayForm->getData();
            $crc = implode('.', [md5(time()), $user->getId(), md5($user->getEmail())]);
            
            $config = array(
                'amount' => $task['amount'],
                'description' => 'Order: ' . $user->getId() . '-' . $this->walletRepository->nextItem() . '-' . md5(time()),
                'crc' => $crc,
                'return_url' => 'https://'.$request->getHost() . $this->router->generate('payment_done'),
                'result_url' => 'https://'.$request->getHost() . $this->router->generate('payment_response', ['uid' => $user->getId()]),
                'result_email' => 'admin@s89.eu',
                'email' => $user->getEmail(),
                'name' => $user->getLogin(),
            );
            
            $form = $this->tPay->generateForm($config);
  
        }
        
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
            'tpay' => $form ?? '',
            'amount' => $task['amount'] ?? 0,
            'last_username' => '',
        ]);
    }
    
    #[Route('/done', name: 'done')]
    public function done(): Response
    {
        return $this->render('payment/done.html.twig', [
            'controller_name' => 'PaymentController',
            'last_username' => '',
        ]);
    }
    
    #[Route('/{uid}/tpay', name: 'response')]
    public function tpayGet(Request $request, int $uid): Response
    {
        $data = $request->request->all();
        $md5sum = md5($data['id'] . $data['tr_id'] . $data['tr_amount'] . $data['tr_crc'] . $this->tPay->getCode());
        if ($md5sum === $data['md5sum']) {
            $user = $this->userRepository->find($uid);
            $status = $data['tr_status'];
            $wallet = new Wallet();
            $wallet->setOperation($data['tr_id']);
            $wallet->setUid($user);
            $wallet->setCreatedAt(new \Datetime);
            $wallet->setCrc($data['tr_crc']);
            $wallet->setStatus($status);
            $wallet->setAmount($data['tr_amount']);
            $wallet->setPaid($data['tr_paid']);
            $wallet->setType('tpay');
            $wallet->setOrderId($data['tr_desc']);
            
            $user->addToWallet($data['tr_paid']);
            
            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            echo 'TRUE';
        } else {
            echo 'FALSE - Invalid request';
        }
        return new Response();
    }
}
