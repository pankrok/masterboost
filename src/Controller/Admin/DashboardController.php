<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PromoCodes;
use App\Entity\Partners;
use App\Entity\Servers;
use App\Entity\User;
use App\Entity\Pages;
use App\Entity\Comments;
use App\Entity\Vote;
use App\Entity\Wallet;

class DashboardController extends AbstractDashboardController
{
    
    /**
     * @Route("/bcp")
     */
    public function admin()
    {
        return $this->redirectToRoute('admin', [], 301);
    }
    /**
     * @Route("/{_locale}/bcp", name="admin")
     */
    public function index(): Response
    {
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Boost')
            ;
    }

    public function configureMenuItems(): iterable
    {
        
        
        yield MenuItem::linkToRoute('Dashboard', 'fa fa-home', 'admin');
        yield MenuItem::section('Servers');
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::linkToCrud('Servers', 'fas fa-server', Servers::class);
        }
        
        if ($this->isGranted('ROLE_MODERATOR') || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::linkToCrud('Comments', 'fas fa-comments', Comments::class);
        }
        
        if ($this->isGranted('ROLE_MODERATOR') || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::linkToCrud('Votes', 'fas fa-vote-yea', Vote::class);
        }
         
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::section('Accounts');
            yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        }
        
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::section('Promotions');
            yield MenuItem::linkToCrud('Codes', 'fas fa-key', PromoCodes::class);
            yield MenuItem::linkToCrud('Partners', 'far fa-handshake', Partners::class);
        }
        
        if ($this->isGranted('ROLE_MODERATOR') || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPERADMIN')) {
             yield MenuItem::section('content');
            yield MenuItem::linkToCrud('Pages', 'fas fa-file-alt', Pages::class);
        }
        
        if ($this->isGranted('ROLE_SUPERADMIN')) {
            yield MenuItem::section('Configuration');
            yield MenuItem::linkToRoute('Settings', 'fa fa-cogs', 'admin_cfg');
            yield MenuItem::subMenu('Payments', 'fas fa-money-bill-wave-alt')->setSubItems([
                MenuItem::linkToRoute('Tpay', 'fas fa-credit-card', 'admin_tpay'),
                MenuItem::linkToRoute('MicroSMS', 'fas fa-mobile-alt', 'micro_sms'),
            ]);
            yield MenuItem::subMenu('Logs', 'fas fa-history')->setSubItems([
                MenuItem::linkToCrud('Payments', 'fas fa-dollar-sign', Wallet::class),
            ]);
        }
    }
}
