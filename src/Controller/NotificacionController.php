<?php

namespace App\Controller;

use App\Entity\Notificacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notificacion')]
class NotificacionController extends AbstractController
{
    #[Route('/leer/{id}', name: 'app_notificacion_leer', methods: ['GET'])]
    public function leer(Notificacion $notificacion, EntityManagerInterface $entityManager): Response
    {
        // Verificar que el usuario solo pueda leer sus propias notificaciones
        if ($this->getUser() !== $notificacion->getUsuario()) {
            throw $this->createAccessDeniedException('No tienes permiso para ver esta notificación.');
        }

        $notificacion->setLeido(true);
        $entityManager->flush();

        $viaje = $notificacion->getViaje();
        if ($viaje !== null) {
            return $this->redirectToRoute('app_viaje_show', ['id' => $viaje->getId()]);
        }

        // Si el viaje fue borrado, redirigimos a la página de inicio
        return $this->redirectToRoute('app_dashboard'); 
    }
}
