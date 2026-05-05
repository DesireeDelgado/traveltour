<?php

namespace App\Controller;

use App\Entity\Viaje;
use App\Form\ViajeType;
use App\Repository\ViajeRepository;
use App\Repository\FavoritosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/viajes')]
final class ViajeController extends AbstractController
{


    #[Route(name: 'app_viaje_index', methods: ['GET'])]
    public function index(Request $request, ViajeRepository $viajeRepository, FavoritosRepository $favRepo): Response
    {
        $presupuesto = $request->query->get('presupuesto');
        $dias = $request->query->get('dias');
        $lugar = $request->query->get('lugar');

        if ($presupuesto !== null && $presupuesto !== '') {
            $presupuesto = (float) $presupuesto;
        } else {
            $presupuesto = null;
        }

        if ($dias !== null && $dias !== '') {
            $dias = (int) $dias;
        } else {
            $dias = null;
        }

        // Filtramos viajes
        $viajes = $viajeRepository->findByFilters($presupuesto, $dias, $lugar);
        $destinos = $viajeRepository->findAllDestinos();

        $user = $this->getUser();
        
        // Creamos un array con los IDs de los viajes que el usuario tiene en favoritos
        $idsFavoritos = [];
        if ($user) {
            $favoritos = $favRepo->findBy(['id_usuario' => $user]);
            foreach ($favoritos as $f) {
                $idsFavoritos[] = $f->getIdViaje()->getId();
            }
        }

        return $this->render('viaje/index.html.twig', [
            'viajes' => $viajes,
            'idsFavoritos' => $idsFavoritos,
            'destinos_json' => json_encode($destinos),
            'filters' => [
                'presupuesto' => $presupuesto,
                'dias' => $dias,
                'lugar' => $lugar
            ]
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
   /*
    #[Route('/{id}', name: 'app_viaje_show', methods: ['GET'])]
    public function show(Viaje $viaje): Response
    {
        return $this->render('viaje/show.html.twig', [
            'viaje' => $viaje,
        ]);
    }
EL QUE PARECE QUE VA 
#[Route('/{id}', name: 'app_viaje_show', methods: ['GET'])]
    public function show(Viaje $viaje): Response
    {
        $user = $this->getUser();
        $isFavorito = false;

        // Comprobamos si el usuario está logueado y si el viaje está en su colección de favoritos
        if ($user) {
            // IMPORTANTE: Asegúrate de que el método en tu entidad User se llame getFavoritos()
            // o cámbialo por el nombre correcto de tu relación ManyToMany
            $isFavorito = $user->getFavoritos()->contains($viaje);
        }

        return $this->render('viaje/show.html.twig', [
            'viaje' => $viaje,
            'isFavorito' => $isFavorito, // Aquí es donde "nace" la variable que Twig necesita
        ]);
    }
*/
#[Route('/{id}', name: 'app_viaje_show', methods: ['GET'])]
public function show(Viaje $viaje, FavoritosRepository $favRepo): Response
{
    $user = $this->getUser();
    $isFavorito = false;

    if ($user) {
        // IMPORTANTE: Usa los nombres exactos de tus campos (id_usuario / id_viaje)
        $favorito = $favRepo->findOneBy([
            'id_usuario' => $user,
            'id_viaje' => $viaje
        ]);
        $isFavorito = ($favorito !== null);
    }

    return $this->render('viaje/show.html.twig', [
        'viaje' => $viaje,
        'isFavorito' => $isFavorito, // Esta es la variable que el HTML necesita
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
