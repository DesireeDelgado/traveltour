<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;

#[Route('/usuario')]
final class UsuarioController extends AbstractController
{
    /* INDEX COMENTADO POR SEGURIDAD ANTI-DUPLICADOS, SE DEJA SOLO PARA ADMINISTRADORES
    #[Route(name: 'app_usuario_index', methods: ['GET'])]
    public function index(UsuarioRepository $usuarioRepository): Response
    {
        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarioRepository->findAll(),
        ]);
    }
*/
    #[Route('/new', name: 'app_usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($usuario);
            $entityManager->flush();

            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_usuario_show', methods: ['GET'])]
    public function show(Usuario $usuario): Response
    {
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_usuario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Usuario $usuario, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // SEGURIDAD: Si el ID de la URL no coincide con el usuario logueado, lanzamos 403
        if ($usuario !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para editar este perfil.');
        }

        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('current_password')->getData();
            $newPassword = $form->get('plainPassword')->getData();// Solo si se ha intentado cambiar la contraseña, validamos los campos relacionados

            if ($newPassword || $currentPassword) {
                if (!$currentPassword) {
                    $form->get('current_password')->addError(new FormError('Debes introducir tu contraseña actual para cambiarla.'));
                } elseif (!$newPassword) {
                    $form->get('plainPassword')->first()->addError(new FormError('Debes introducir la nueva contraseña.'));
                } elseif (!$userPasswordHasher->isPasswordValid($usuario, $currentPassword)) {
                    // Control rápido por si hubo "Doble Submit": evitamos el error falso si la contraseña ya fue cambiada
                    if (!$userPasswordHasher->isPasswordValid($usuario, $newPassword)) {
                        $form->get('current_password')->addError(new FormError('La contraseña actual no es correcta.'));
                    } else {
                        $this->addFlash('success', 'Contraseña actualizada correctamente.');
                    }
                } else {
                    $usuario->setPassword(
                        $userPasswordHasher->hashPassword(
                            $usuario,
                            $newPassword
                        )
                    );
                    $this->addFlash('success', 'Contraseña actualizada correctamente.');
                }
            }

            if ($form->getErrors(true)->count() === 0) {
                $entityManager->flush();

                $this->addFlash('success', 'Perfil actualizado correctamente.');
                // Redirigimos a su propio perfil tras editar
                return $this->redirectToRoute('app_usuario_show', ['id' => $usuario->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_usuario_delete', methods: ['POST'])]
    public function delete(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        // SEGURIDAD: Solo el dueño puede borrar su propia cuenta
        if ($usuario !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para borrar esta cuenta.');
        }

        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->getPayload()->getString('_token'))) {
            // Antes de borrar, hay que cerrar la sesión del usuario para evitar errores
            $this->container->get('security.token_storage')->setToken(null);
            
            $entityManager->remove($usuario);
            $entityManager->flush();
        }

        // Al borrar la cuenta, lo mandamos al inicio (página pública)
        return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
