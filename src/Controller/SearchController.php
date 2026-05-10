<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use App\Repository\ViajeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
final class SearchController extends AbstractController
{
    /**
     * Endpoint AJAX que devuelve sugerencias de autocompletado:
     * - Usuarios cuyo nickname coincide con la búsqueda
     * - Destinos de viajes que coinciden con la búsqueda
     */
    #[Route('/autocomplete', name: 'app_search_autocomplete', methods: ['GET'])]
    public function autocomplete(
        Request $request,
        UsuarioRepository $usuarioRepository,
        ViajeRepository $viajeRepository
    ): JsonResponse {
        $q = trim($request->query->get('q', ''));

        if (strlen($q) < 1) {
            return $this->json([]);
        }

        $suggestions = [];

        // Buscar destinos de viajes
        $destinos = $viajeRepository->findDestinosByQuery($q);
        foreach ($destinos as $destino) {
            $suggestions[] = [
                'type'  => 'destino',
                'label' => $destino,
                'url'   => $this->generateUrl('app_viaje_index', ['lugar' => $destino]),
            ];
        }

        // Buscar usuarios por nickname
        $usuarios = $usuarioRepository->findByNicknameQuery($q);
        foreach ($usuarios as $usuario) {
            $avatar = $usuario->getUrlFotoPerfil()
                ?? 'https://ui-avatars.com/api/?name=' . urlencode($usuario->getNickname()) . '&background=dbeafe&color=2563eb';

            $suggestions[] = [
                'type'   => 'usuario',
                'label'  => $usuario->getNickname(),
                'url'    => $this->generateUrl('app_usuario_show', ['id' => $usuario->getId()]),
                'avatar' => $avatar,
            ];
        }

        return $this->json($suggestions);
    }

    /**
     * Ruta de búsqueda principal: redirige al listado de viajes filtrado por destino.
     */
    #[Route('', name: 'app_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $q = trim($request->query->get('q', ''));

        if (empty($q)) {
            return $this->redirectToRoute('app_viaje_index');
        }

        return $this->redirectToRoute('app_viaje_index', ['lugar' => $q]);
    }
}
