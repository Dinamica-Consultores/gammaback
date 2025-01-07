<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createusuario_grupoeconomicoAPIRequest;
use App\Http\Requests\API\Updateusuario_grupoeconomicoAPIRequest;
use App\Models\usuario_grupoeconomico;
use App\Repositories\usuario_grupoeconomicoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class usuario_grupoeconomicoAPIController
 */
class usuario_grupoeconomicoAPIController extends AppBaseController
{
    private usuario_grupoeconomicoRepository $usuarioGrupoeconomicoRepository;

    public function __construct(usuario_grupoeconomicoRepository $usuarioGrupoeconomicoRepo)
    {
        $this->usuarioGrupoeconomicoRepository = $usuarioGrupoeconomicoRepo;
    }

    /**
     * Display a listing of the usuario_grupoeconomicos.
     * GET|HEAD /usuario_grupoeconomicos
     */
    public function index(Request $request): JsonResponse
    {
        $usuarioGrupoeconomicos = $this->usuarioGrupoeconomicoRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($usuarioGrupoeconomicos->toArray(), 'Usuario Grupoeconomicos retrieved successfully');
    }

    /**
     * Store a newly created usuario_grupoeconomico in storage.
     * POST /usuario_grupoeconomicos
     */
    public function store(Createusuario_grupoeconomicoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $usuarioGrupoeconomico = $this->usuarioGrupoeconomicoRepository->create($input);

        return $this->sendResponse($usuarioGrupoeconomico->toArray(), 'Usuario Grupoeconomico saved successfully');
    }

    /**
     * Display the specified usuario_grupoeconomico.
     * GET|HEAD /usuario_grupoeconomicos/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var usuario_grupoeconomico $usuarioGrupoeconomico */
        $usuarioGrupoeconomico = $this->usuarioGrupoeconomicoRepository->find($id);

        if (empty($usuarioGrupoeconomico)) {
            return $this->sendError('Usuario Grupoeconomico not found');
        }

        return $this->sendResponse($usuarioGrupoeconomico->toArray(), 'Usuario Grupoeconomico retrieved successfully');
    }

    /**
     * Update the specified usuario_grupoeconomico in storage.
     * PUT/PATCH /usuario_grupoeconomicos/{id}
     */
    public function update($id, Updateusuario_grupoeconomicoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var usuario_grupoeconomico $usuarioGrupoeconomico */
        $usuarioGrupoeconomico = $this->usuarioGrupoeconomicoRepository->find($id);

        if (empty($usuarioGrupoeconomico)) {
            return $this->sendError('Usuario Grupoeconomico not found');
        }

        $usuarioGrupoeconomico = $this->usuarioGrupoeconomicoRepository->update($input, $id);

        return $this->sendResponse($usuarioGrupoeconomico->toArray(), 'usuario_grupoeconomico updated successfully');
    }

    /**
     * Remove the specified usuario_grupoeconomico from storage.
     * DELETE /usuario_grupoeconomicos/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var usuario_grupoeconomico $usuarioGrupoeconomico */
        $usuarioGrupoeconomico = $this->usuarioGrupoeconomicoRepository->find($id);

        if (empty($usuarioGrupoeconomico)) {
            return $this->sendError('Usuario Grupoeconomico not found');
        }

        $usuarioGrupoeconomico->delete();

        return $this->sendSuccess('Usuario Grupoeconomico deleted successfully');
    }
}
