<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PromoCodes;
use App\Entity\Servers;
use App\Entity\User;
use App\Entity\Pages;
use App\Form\TpayAdminType;
use Symfony\Component\Yaml\Yaml;

class TpayController extends AbstractDashboardController
{
    
    protected $req;
    
    public function __construct(RequestStack $request)
    {
        $this->req = $request;
    }
    
    /**
     * @Route("{_locale}/admin/tpay", name="admin_tpay")
     */
    public function index(): Response
    {
        $configFile = $this->getParameter('kernel.project_dir') . '/config/tpay.yaml';
        $config = Yaml::parseFile($configFile);
        $form = $this->createForm(TpayAdminType::class, null, $config);
        $form->handleRequest($this->req->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task['merchantId'] = intval($task['merchantId']);
            $yaml = Yaml::dump($task);
            file_put_contents($configFile, $yaml);
        }
        
        return $this->render('admin/config.html.twig', [
            'controller' => 'Tpay settings',
            'd_form' => $form->createView(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Boost');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
