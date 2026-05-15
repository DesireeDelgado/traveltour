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
        // 1. Obtenemos el usuario logueado
        $user = $this->getUser();
        // 2. Si no hay usuario, mandamos al login
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // 3. Filtramos para que solo salgan los registros del usuario actual
        // y que el dueño del viaje no esté en soft-delete
        $favoritos = $favoritosRepository->createQueryBuilder('f')
            ->join('f.id_viaje', 'v')
            ->join('v.id_usuario', 'u')
            ->where('f.id_usuario = :user')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->render('favoritos/index.html.twig', [
            'favoritos' => $favoritos,
        ]);
    }

     //RUTA PARA GUARDAR EN FAVORITOS
    #[Route('/toggle/{id}', name: 'app_favoritos_toggle', methods: ['GET', 'POST'])]
    public function toggle(
        \App\Entity\Viaje $viaje, 
        EntityManagerInterface $entityManager, 
        FavoritosRepository $favoritosRepository,
        Request $request
    ): Response {
    $user = $this->getUser();

    if (!$user) {
        // Si es AJAX y no hay usuario, mandamos error en vez de redirigir
        if ($request->isXmlHttpRequest()) {
            return $this->json(['error' => 'Login required'], 403);
        }
        return $this->redirectToRoute('app_login');
    }
    // Buscamos si ya existe un favorito para este usuario y viaje
    $favorito = $favoritosRepository->findOneBy([
        'id_usuario' => $user,
        'id_viaje' => $viaje
    ]);
    // Si existe, lo borramos (toggle off). Si no, lo creamos (toggle on)
    if ($favorito) {
        $entityManager->remove($favorito);
        $estado = false;
    } else {
        $nuevoFavorito = new \App\Entity\Favoritos();
        $nuevoFavorito->setIdUsuario($user);
        $nuevoFavorito->setIdViaje($viaje);
        $entityManager->persist($nuevoFavorito);
        $estado = true;
    }
    // Guardamos los cambios en la base de datos
    $entityManager->flush();

    // Si la petición viene de JavaScript (AJAX), respondemos con datos, no con redirección
    if ($request->isXmlHttpRequest() || $request->query->get('ajax') == 1) {
    return $this->json(['isFavorito' => $estado]);
    }

    // Si es un click normal (como en tu Index), sigue funcionando igual que antes
    $referer = $request->headers->get('referer');
    return $this->redirect($referer ?: $this->generateUrl('app_viaje_index'));
    }
/*
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
*/
}

