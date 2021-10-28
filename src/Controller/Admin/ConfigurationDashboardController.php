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
use App\Form\AdminConfigType;
use Symfony\Component\Yaml\Yaml;

class ConfigurationDashboardController extends AbstractDashboardController
{
    
    protected $req;
    
    public function __construct(RequestStack $request)
    {
        $this->req = $request;
    }
    
    /**
     * @Route("/admin/cfg", name="admin_cfg")
     */
    public function index(): Response
    {
        $configFile = $this->getParameter('kernel.project_dir') . '/config/boost.yaml';
        $config = Yaml::parseFile($configFile);
        $form = $this->createForm(AdminConfigType::class, null, $config);
        $form->handleRequest($this->req->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            foreach ($task as $k => $v) {
                if($k === 'minRate') {
                    $task[$k] = floatval($v);
                } elseif ($k === 'termUrl') {
                    $task[$k] = string($v); 
                } else {
                    $task[$k] = intval($v);
                }
            }
            $yaml = Yaml::dump($task);

                file_put_contents($configFile, $yaml);
        }
        
        return $this->render('admin/config.html.twig', [
            'controller' => 'Boost settings',
            'd_form' => $form->createView()
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Boost');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Dashboard', 'fa fa-home', 'admin');
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::linkToCrud('Codes', 'fas fa-key', PromoCodes::class);
        }
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::linkToCrud('Servers', 'fas fa-server', Servers::class);
        }
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        }
        if ($this->isGranted('ROLE_MODERATOR') || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::linkToCrud('Pages', 'fas fa-file-alt', Pages::class);
        }
        if ($this->isGranted('ROLE_SUPERADMIN')) {
        yield MenuItem::linkToRoute('Configuration', 'fa fa-cogs', 'admin_cfg');
        }
    }
}
