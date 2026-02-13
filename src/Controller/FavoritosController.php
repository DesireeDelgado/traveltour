<?php

namespace App\Controller;

use App\Entity\Favoritos;
use App\Form\FavoritosType;
use App\Repository\FavoritosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favoritos')]
final class FavoritosController extends AbstractController
{
    #[Route(name: 'app_favoritos_index', methods: ['GET'])]
    public function index(FavoritosRepository $favoritosRepository): Response
    {
        return $this->render('favoritos/index.html.twig', [
            'favoritos' => $favoritosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_favoritos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $favorito = new Favoritos();
        $form = $this->createForm(FavoritosType::class, $favorito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($favorito);
            $entityManager->flush();

            return $this->redirectToRoute('app_favoritos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('favoritos/new.html.twig', [
            'favorito' => $favorito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_favoritos_show', methods: ['GET'])]
    public function show(Favoritos $favorito): Response
    {
        return $this->render('favoritos/show.html.twig', [
            'favorito' => $favorito,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_favoritos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Favoritos $favorito, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FavoritosType::class, $favorito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_favoritos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('favoritos/edit.html.twig', [
            'favorito' => $favorito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_favoritos_delete', methods: ['POST'])]
    public function delete(Request $request, Favoritos $favorito, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$favorito->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($favorito);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_favoritos_index', [], Response::HTTP_SEE_OTHER);
    }
}
