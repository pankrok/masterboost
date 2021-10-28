<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LocaleController extends AbstractController
{
    #[Route('/{_locale}/lang', name: 'locale')]
    public function index(string $_locale, Request $request): Response
    {
        return $this->redirectToRoute('home');
    }
}
