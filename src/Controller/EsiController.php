<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EsiController extends AbstractController
{
    #[Route('/rightModules', name: 'esi_moduleRight')]
    public function index(): Response
    {
        return $this->render('partials/moduleRight.html.twig', [
            
        ]);
    }
}
