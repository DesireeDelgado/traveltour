<?php

namespace App\Controller;

use App\Entity\Viaje;
use App\Form\ViajeType;
use App\Repository\ViajeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/viajes')]
final class ViajeController extends AbstractController
{
    #[Route(name: 'app_viaje_index', methods: ['GET'])]
    public function index(ViajeRepository $viajeRepository): Response
    {
        return $this->render('viaje/index.html.twig', [
            'viajes' => $viajeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_viaje_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, ViajeRepository $viajeRepository): Response
{
    $viaje = new Viaje();
    $form = $this->createForm(ViajeType::class, $viaje);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // --- BLOQUE DE SEGURIDAD ANTI-DUPLICADOS ---
        $existe = $viajeRepository->findOneBy([
            'titulo' => $viaje->getTitulo(),
            'destino' => $viaje->getDestino()
        ]);

        if ($existe) {
            // Si ya existe uno igual, simplemente redirigimos sin guardar
            return $this->redirectToRoute('app_viaje_index');
        }
        // --- FIN BLOQUE ---

        $viaje->setIdUsuario($this->getUser());
        $entityManager->persist($viaje);
        $entityManager->flush();

        return $this->redirectToRoute('app_viaje_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('viaje/new.html.twig', [
        'viaje' => $viaje,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_viaje_show', methods: ['GET'])]
    public function show(Viaje $viaje): Response
    {
        return $this->render('viaje/show.html.twig', [
            'viaje' => $viaje,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_viaje_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Viaje $viaje, EntityManagerInterface $entityManager): Response
    {
        if ($viaje->getIdUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para editar este viaje.');
        }

        $form = $this->createForm(ViajeType::class, $viaje);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_viaje_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('viaje/edit.html.twig', [
            'viaje' => $viaje,
            'form' => $form,
        ]);
    }

   #[Route('/{id}', name: 'app_viaje_delete', methods: ['POST'])]
public function delete(Request $request, ?Viaje $viaje, EntityManagerInterface $entityManager): Response
{
    if (!$viaje) {
        return $this->redirectToRoute('app_viaje_index');
    }

    // --- AÑADE ESTA COMPROBACIÓN ---
    if ($viaje->getIdUsuario() !== $this->getUser()) {
        throw $this->createAccessDeniedException('No puedes borrar viajes de otros usuarios.');
    }
    // -------------------------------

    if ($this->isCsrfTokenValid('delete'.$viaje->getId(), $request->getPayload()->getString('_token'))) {
        $entityManager->remove($viaje);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_viaje_index', [], Response::HTTP_SEE_OTHER);
}
}
