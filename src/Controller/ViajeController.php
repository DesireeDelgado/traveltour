<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Viaje;
use App\Form\ComentarioType;
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
    public function new(Request $request, EntityManagerInterface $entityManager, ViajeRepository $viajeRepository, \Symfony\Component\String\Slugger\SluggerInterface $slugger): Response
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
                return $this->redirectToRoute('app_viaje_index');
            }
            // --- FIN BLOQUE ---

            $viaje->setIdUsuario($this->getUser());

            // --- PROCESAMIENTO DE IMÁGENES ---
            $archivosImagenes = $form->get('imagenes')->getData();
            if ($archivosImagenes) {
                foreach ($archivosImagenes as $archivo) {
                    $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                    $nombreSeguro = $slugger->slug($nombreOriginal);
                    $nuevoNombre = $nombreSeguro.'-'.uniqid().'.'.$archivo->guessExtension();

                    try {
                        $archivo->move(
                            $this->getParameter('viajes_directory'),
                            $nuevoNombre
                        );

                        $imagen = new \App\Entity\Imagen();
                        $imagen->setUrlPath($nuevoNombre);
                        $viaje->addImagene($imagen);
                        
                        // Persistir la imagen individualmente
                        $entityManager->persist($imagen);
            
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Error subiendo imagen: ' . $e->getMessage());
                        continue;
                    }
                }
            }
            // ---------------------------------

            $entityManager->persist($viaje);
            $entityManager->flush();

            return $this->redirectToRoute('app_viaje_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('viaje/new.html.twig', [
            'viaje' => $viaje,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
    }

#[Route('/{id}', name: 'app_viaje_show', methods: ['GET'])]
public function show(Request $request, Viaje $viaje, FavoritosRepository $favRepo): Response
{
    $user = $this->getUser();
    $isFavorito = false;

    if ($user) {
        $favorito = $favRepo->findOneBy([
            'id_usuario' => $user,
            'id_viaje'   => $viaje,
        ]);
        $isFavorito = ($favorito !== null);
    }

    // Token de un solo uso para prevenir envíos duplicados
    $commentSid = bin2hex(random_bytes(8));
    $request->getSession()->set('comment_sid_' . $viaje->getId(), $commentSid);

    $comentarioForm = $this->createForm(ComentarioType::class, new Comentario());

    return $this->render('viaje/show.html.twig', [
        'viaje'          => $viaje,
        'isFavorito'     => $isFavorito,
        'comentarioForm' => $comentarioForm,
        'comment_sid'    => $commentSid,
    ]);
}

#[Route('/{id}/comentar', name: 'app_viaje_comentar', methods: ['POST'])]
public function comentar(Request $request, Viaje $viaje, EntityManagerInterface $entityManager): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $session    = $request->getSession();
    $sessionKey = 'comment_sid_' . $viaje->getId();

    // Verificar y consumir el token de un solo uso
    $expectedSid  = $session->get($sessionKey);
    $submittedSid = $request->request->get('comment_sid', '');

    if (!$expectedSid || $submittedSid !== $expectedSid) {
        // Token ya consumido o inválido: ignorar el envío duplicado
        return $this->redirectToRoute('app_viaje_show', ['id' => $viaje->getId()]);
    }

    // Consumir el token inmediatamente para que no pueda reutilizarse
    $session->remove($sessionKey);

    $comentario = new Comentario();
    $form = $this->createForm(ComentarioType::class, $comentario);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $comentario
            ->setIdUsuario($this->getUser())
            ->setIdViaje($viaje)
            ->setFechaCreacion(new \DateTimeImmutable());

        $entityManager->persist($comentario);
        $entityManager->flush();

        $this->addFlash('success', '¡Comentario publicado con éxito!');
    }

    return $this->redirectToRoute('app_viaje_show', ['id' => $viaje->getId()]);
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
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
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
