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
    // RUTA PARA LIMPIAR NOTIFICACIONES
    #[Route('/limpiar', name: 'app_notificacion_limpiar', methods: ['POST'])]
    public function limpiar(EntityManagerInterface $entityManager, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Debes estar logueado.');
        }

        // Validación del token CSRF 
        if ($this->isCsrfTokenValid('limpiar_notificaciones', $request->request->get('_token'))) {
            $notificaciones = $user->getNotificaciones();
            foreach ($notificaciones as $notificacion) {
                $entityManager->remove($notificacion);
            }
            $entityManager->flush();
            
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false], 400);
    }
}
