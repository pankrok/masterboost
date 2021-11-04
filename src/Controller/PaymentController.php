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
use App\Form\MicroSmsType;
use App\Form\UserTransferType;
use Symfony\Component\Yaml\Yaml;

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
    
    #[Route('/basket/sms', name: 'sms')]
    public function basketSms(Request $request): Response
    {
        $user = $this->getUser();
        $okmsg = $errormsg = null;
        $microSmsType = $this->createForm(MicroSmsType::class);
        $microSmsType->handleRequest($request);
        if ($microSmsType->isSubmitted() && $microSmsType->isValid()) {
            $code = $microSmsType->getData()['sms_code'];
            $settings = Yaml::parseFile($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR  . 'config' . DIRECTORY_SEPARATOR  .'microsms.yaml');
            $data = $settings['sms'];
            if (preg_match("/^[A-Za-z0-9]{8}$/", $code)) {
                
                $a = array();
                $b = array();
                
                foreach ($data as $cfg) {
                    array_push($a, $cfg['number']);
                    $b[$cfg['number']] = ['product' => $cfg['product'], 'amount' => ($cfg['amount']*1.23)];
                }
                /*
                    Łączymy się z serwerem MicroSMS
                */
                $api = @file_get_contents("http://microsms.pl/api/v2/multi.php?userid=" . $settings['userid'] . "&code=" . $code . '&serviceid=' . $settings['serviceid']);
                // debug
                // $api = '{"connect": true, "data": {"status":1,"number":7055}}';
                // print_r($api);
                /* 
                    Jeśli wystąpi problem z połączeniem, skrypt wyświetli błąd.
                */
                if (!isset($api)) {
                    $msg = 'Nie można nawiązać połączenia z serwerem płatności.';
                } else {
                    /*
                        Dekodujemy odpowiedź serwera do formatu json
                    */
                    $api = json_decode($api);
                
                    /* 
                        Sprawdzamy czy odpowiedź na pewno jest w formacie json
                    */
                    if (!is_object($api)) {
                        $errormsg = 'Nie można odczytać informacji o płatności.';
                    } else if (isset($api->error) && $api->error) {
                        $errormsg = 'Kod błędu: ' . $api->error->errorCode . ' - ' . $api->error->message;
                    } else if ($api->connect == FALSE) {
                        $errormsg = 'Kod błędu: ' . $api->data->errorCode . ' - ' . $api->data->message;
                    } else if (!isset($b[$api->data->number])) {
                        $errormsg = 'Przesłany kod jest nieprawidłowy, spróbuj ponownie.';
                    }
                }
                
                if (!isset($errormsg) && isset($api->connect) && $api->connect == TRUE) {
                    /*
                        Jeśli kod jest prawidłowy, wydajemy produkt
                    */
                    if ($api->data->status == 1) {
                        $okmsg = 'Zakupiłeś produkt ' . $b[$api->data->number]['product'] . ', konto zostało doładowane o kwotę: ' . $b[$api->data->number]['amount'];               
                        // user logic
                        $user->addToWallet($b[$api->data->number]['amount']);
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                        
                        $wallet = new Wallet();
                        $wallet->setOperation(md5($user->getUsername() . $b[$api->data->number]['product'] . time()));
                        $wallet->setUid($user);
                        $wallet->setCreatedAt(new \Datetime);
                        $wallet->setCrc(md5(time()));
                        $wallet->setStatus($status);
                        $wallet->setAmount($b[$api->data->number]['amount']);
                        $wallet->setPaid($b[$api->data->number]['amount']);
                        $wallet->setType('microsms');
                        $wallet->setOrderId('ms-'.md5(time().$b[$api->data->number]['product']));
                        $this->entityManager->persist($wallet);
                        $this->entityManager->flush();
                        
                    } else {
                        $errormsg = 'Przesłany kod jest nieprawidłowy, spróbuj ponownie.';
                    }
                }

            } else {
                $errormsg = 'Przesłany kod jest nieprawidłowy, przepisz go ponownie';
            }
        } else { 
            $errormsg = 'Ups, coś poszło nie tak. Skontaktuj się z administracją serwisu.';
        }
        
        return $this->render('payment/sms_done.html.twig', [
            'controller_name' => 'PaymentController',
            'okmsg' => $okmsg,
            'errormsg' => $errormsg,
            'amount' => $task['amount'] ?? 0,
            'last_username' => '',
        ]);
    }
    
    #[Route('/transfer', name: 'transfer')]
    public function paymantTransfer(Request $request): Response
    {
        $rname = $okmsg = $errmsg = null;
        $form = $this->createForm(UserTransferType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $reciver = $this->userRepository->findOneBy(['login' => $data['username']]);
            $sender = $this->getUser();
            if ($reciver === null) {
                $errmsg = 'cant find user';
            }
            
            if ($sender->getWallet() < $data['amount']) {
                $errmsg = 'you dont have enough cash on your account!';
            }
            
            if ($errmsg === null) {
                $reciver->addToWallet($data['amount']);
                $this->entityManager->persist($reciver);
                $this->entityManager->flush();
                
                $sender->subtractToWallet($data['amount']);
                $this->entityManager->persist($sender);
                $this->entityManager->flush();
                $okmsg = 'you transfer is send';
                $rname = $reciver->getUsername();
            }
            
            
        } else {
            $errmsg = 'UPS, something went wrong!';
        }
         return $this->render('payment/transfer.html.twig', [
            'controller_name' => 'PaymentController',
            'okmsg' => $okmsg,
            'errormsg' => $errmsg,
            'amount' => $data['amount'] ?? 0,
            'reciver' => $rname,
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
