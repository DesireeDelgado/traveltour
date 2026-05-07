<?php

namespace App\Controller;

use App\Entity\Imagen;
use App\Form\ImagenType;
use App\Repository\ImagenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/imagen')]
final class ImagenController extends AbstractController
{
    #[Route('/perfil/{filename}', name: 'app_imagen_perfil', methods: ['GET'])]
    public function perfil(string $filename, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');// Solo usuarios autenticados pueden acceder a las imágenes de perfil

        // Protegemos contra ataques de path traversal, asegurándonos que el filename no contenga rutas relativas o caracteres sospechosos
        if (str_contains($filename, '/') || str_contains($filename, '\\') || str_contains($filename, '..')) {
            throw $this->createNotFoundException('Imagen no válida.');
        }
        // Construimos la ruta completa al archivo de imagen
        $filepath = $this->getParameter('kernel.project_dir') . '/storage/profiles/' . $filename;
        if (!file_exists($filepath)) {
            throw $this->createNotFoundException('Imagen no encontrada.');
        }
        
        // Liberar la sesión antes de servir la imagen para evitar bloqueos de sesión (Deadlock)
        if ($request->hasSession()) {
            $request->getSession()->save();
        }

        return new BinaryFileResponse($filepath);
    }

    #[Route('/viaje/{filename}', name: 'app_imagen_viaje', methods: ['GET'])]
    public function viaje(string $filename, Request $request): Response
    {
        // Protegemos contra ataques de path traversal
        if (str_contains($filename, '/') || str_contains($filename, '\\') || str_contains($filename, '..')) {
            throw $this->createNotFoundException('Imagen no válida.');
        }
        
        $filepath = $this->getParameter('kernel.project_dir') . '/storage/viajes/' . $filename;
        
        if (!file_exists($filepath)) {
            throw $this->createNotFoundException('Imagen no encontrada.');
        }
        
        // Liberar la sesión antes de servir la imagen para evitar bloqueos de sesión (Deadlock)
        if ($request->hasSession()) {
            $request->getSession()->save();
        }

        return new BinaryFileResponse($filepath);
    }

    #[Route(name: 'app_imagen_index', methods: ['GET'])]
    public function index(ImagenRepository $imagenRepository): Response
    {
        return $this->render('imagen/index.html.twig', [
            'imagens' => $imagenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_imagen_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $imagen = new Imagen();
        $form = $this->createForm(ImagenType::class, $imagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($imagen);
            $entityManager->flush();

            return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('imagen/new.html.twig', [
            'imagen' => $imagen,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_imagen_show', methods: ['GET'])]
    public function show(Imagen $imagen): Response
    {
        return $this->render('imagen/show.html.twig', [
            'imagen' => $imagen,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_imagen_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Imagen $imagen, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImagenType::class, $imagen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('imagen/edit.html.twig', [
            'imagen' => $imagen,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_imagen_delete', methods: ['POST'])]
    public function delete(Request $request, Imagen $imagen, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imagen->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($imagen);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_imagen_index', [], Response::HTTP_SEE_OTHER);
    }
}
