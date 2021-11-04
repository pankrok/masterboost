<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use App\Form\MicroSmsConfigType;

class MicroSmsController extends AbstractDashboardController
{
    
    protected $req;
    
    public function __construct(RequestStack $request)
    {
        $this->req = $request;
    }
    
    /**
     * @Route("/microsms", name="micro_sms")
     */
    public function index(): Response
    {
        $configFile = $this->getParameter('kernel.project_dir') . '/config/microsms.yaml';
        $config = Yaml::parseFile($configFile);
        $form = $this->createForm(MicroSmsConfigType::class, null, $config);
        $form->handleRequest($this->req->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $data = [
                'active' => $task['active'],
                'userid' => $task['userid'],
                'serviceid' => $task['serviceid'],
                'text' => $task['text'],
                'sms' => []
            ];
            
            foreach ($task['sms'] as $key => $val) {
                if ($val['netprice'] !== null)
                    $data['sms'][$key] = $val;
            }
            
            $yaml = Yaml::dump($data);
            file_put_contents($configFile, $yaml);
        }
        
        return $this->render('admin/microsms.html.twig', [
            'controller' => 'Boost settings',
            'form' => $form->createView()
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
