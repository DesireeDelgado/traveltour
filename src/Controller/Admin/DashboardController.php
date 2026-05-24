<?php

namespace App\Controller\Admin;

use App\Entity\Comentario;
use App\Entity\Usuario;
use App\Entity\Viaje;
use App\Controller\Admin\UsuarioCrudController;
use App\Controller\Admin\ViajeCrudController;
use App\Controller\Admin\ComentarioCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UsuarioCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Traveltour');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(UsuarioCrudController::class, 'Usuarios', 'fas fa-users');
        yield MenuItem::linkTo(ViajeCrudController::class, 'Viajes', 'fas fa-plane');
        yield MenuItem::linkTo(ComentarioCrudController::class, 'Comentarios', 'fas fa-comments');
        yield MenuItem::linkToLogout('Cerrar sesión', 'fas fa-sign-out-alt');
    }
}
