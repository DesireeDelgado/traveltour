<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    //Home publica
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    //Home protegida para usuarios logueados
    #[Route('/home', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')] 
    public function dashboard(): Response
    {
        return $this->render('home_logueado.html.twig');
    }
}