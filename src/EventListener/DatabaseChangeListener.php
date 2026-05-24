<?php

namespace App\EventListener;

use App\Entity\Comentario;
use App\Entity\Notificacion;
use App\Entity\Viaje;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsDoctrineListener(event: Events::preRemove)]
class DatabaseChangeListener
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        // Solo enviamos notificaciones de borrado administrativo si el usuario logueado es administrador
        // o si estamos realizando la acción a través del panel de administración (EasyAdmin).
        $currentUser = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $isAdminPath = $request && str_starts_with($request->getPathInfo(), '/admin');
        $isAdmin = ($currentUser && $this->security->isGranted('ROLE_ADMIN')) || $isAdminPath;

        if (!$isAdmin) {
            return;
        }

        if ($entity instanceof Viaje) {
            $usuario = $entity->getIdUsuario();
            if ($usuario !== null) {
                $titulo = $entity->getTitulo();
                $mensaje = sprintf(
                    "Un administrador ha eliminado tu post de viaje '<strong>%s</strong>' porque infringía las normas de la comunidad.",
                    htmlspecialchars($titulo)
                );

                $notificacion = new Notificacion();
                $notificacion->setUsuario($usuario);
                $notificacion->setMensaje($mensaje);
                $notificacion->setLeido(false);
                $notificacion->setCreatedAt(new \DateTimeImmutable());

                $em = $args->getObjectManager();
                $em->persist($notificacion);

                $uow = $em->getUnitOfWork();
                $metadata = $em->getClassMetadata(Notificacion::class);
                $uow->computeChangeSet($metadata, $notificacion);
            }
        }

        if ($entity instanceof Comentario) {
            $usuario = $entity->getIdUsuario();
            if ($usuario !== null) {
                $viaje = $entity->getIdViaje();
                $tituloViaje = $viaje !== null ? $viaje->getTitulo() : 'Viaje Desconocido';

                $mensaje = sprintf(
                    "Un administrador ha eliminado tu comentario en el viaje '<strong>%s</strong>' porque infringía las normas de la comunidad.",
                    htmlspecialchars($tituloViaje)
                );

                $notificacion = new Notificacion();
                $notificacion->setUsuario($usuario);
                $notificacion->setMensaje($mensaje);
                $notificacion->setLeido(false);
                $notificacion->setCreatedAt(new \DateTimeImmutable());

                // Solo asociamos el viaje si existe y no está siendo borrado
                if ($viaje !== null) {
                    $notificacion->setViaje($viaje);
                }

                $em = $args->getObjectManager();
                $em->persist($notificacion);

                $uow = $em->getUnitOfWork();
                $metadata = $em->getClassMetadata(Notificacion::class);
                $uow->computeChangeSet($metadata, $notificacion);
            }
        }
    }
}
