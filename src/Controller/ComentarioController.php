<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Form\ComentarioType;
use App\Repository\ComentarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comentario')]
final class ComentarioController extends AbstractController
{
    #[Route(name: 'app_comentario_index', methods: ['GET'])]
    public function index(ComentarioRepository $comentarioRepository): Response
    {
        return $this->render('comentario/index.html.twig', [
            'comentarios' => $comentarioRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comentario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comentario = new Comentario();
        $form = $this->createForm(ComentarioType::class, $comentario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comentario);

            // LOGICA NOTIFICACION: Solo notificar si el que comenta no es el dueño del viaje
            $viaje = $comentario->getIdViaje();
            $autorViaje = $viaje?->getIdUsuario();
            $comentarista = $this->getUser();

            if ($autorViaje && $comentarista && $autorViaje !== $comentarista) {
                $mensaje = sprintf('<strong>@%s</strong> ha comentado en tu viaje: <strong>%s</strong>', htmlspecialchars($comentarista->getNickname()), htmlspecialchars($viaje->getTitulo()));
                
                // Evitamos duplicados exactos
                $notificacionExistente = $entityManager->getRepository(\App\Entity\Notificacion::class)->findOneBy([
                    'usuario' => $autorViaje,
                    'viaje' => $viaje,
                    'mensaje' => $mensaje
                ]);

                if (!$notificacionExistente) {
                    $notificacion = new \App\Entity\Notificacion();
                    $notificacion->setUsuario($autorViaje);
                    $notificacion->setViaje($viaje);
                    $notificacion->setLeido(false);
                    $notificacion->setCreatedAt(new \DateTimeImmutable());
                    $notificacion->setMensaje($mensaje);
                    $entityManager->persist($notificacion);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_viaje_show', ['id' => $viaje?->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comentario/new.html.twig', [
            'comentario' => $comentario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comentario_show', methods: ['GET'])]
    public function show(Comentario $comentario): Response
    {
        return $this->render('comentario/show.html.twig', [
            'comentario' => $comentario,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comentario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comentario $comentario, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ComentarioType::class, $comentario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comentario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comentario/edit.html.twig', [
            'comentario' => $comentario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comentario_delete', methods: ['POST'])]
    public function delete(Request $request, ?Comentario $comentario, EntityManagerInterface $entityManager): Response
    {
        // Si el comentario no existe (ya fue borrado), redirigimos al viaje o a la página de inicio
        if (!$comentario) {
            $referer = $request->headers->get('referer');
            return $this->redirect($referer ?: '/');
        }

        $user  = $this->getUser();
        $viaje = $comentario->getIdViaje();

        // Seguridad: solo el autor del comentario O el dueño del viaje pueden eliminarlo
        $esAutor      = ($comentario->getIdUsuario() === $user);
        $esDuenoViaje = ($viaje !== null && $viaje->getIdUsuario() === $user);

        if (!$esAutor && !$esDuenoViaje) {
            throw $this->createAccessDeniedException('No tienes permiso para eliminar este comentario.');
        }

        if ($this->isCsrfTokenValid('delete'.$comentario->getId(), $request->getPayload()->getString('_token'))) {
            
            //Si el usuario que ha comentado borra su comentario, se borra la notificación 
            $autorViaje = $viaje?->getIdUsuario();
            if ($autorViaje && $autorViaje !== $comentario->getIdUsuario()) {
                $mensaje = sprintf('<strong>@%s</strong> ha comentado en tu viaje: <strong>%s</strong>', htmlspecialchars($comentario->getIdUsuario()->getNickname()), htmlspecialchars($viaje->getTitulo()));
                
                $notificacionExistente = $entityManager->getRepository(\App\Entity\Notificacion::class)->findOneBy([
                    'usuario' => $autorViaje,
                    'viaje' => $viaje,
                    'mensaje' => $mensaje
                ]);
                
                if ($notificacionExistente) {
                    $entityManager->remove($notificacionExistente);
                }
            }

            $entityManager->remove($comentario);
            $entityManager->flush();
            $this->addFlash('success', 'Comentario eliminado correctamente.');
        }

        return $this->redirectToRoute('app_viaje_show', ['id' => $viaje?->getId()], Response::HTTP_SEE_OTHER);
    }
}
