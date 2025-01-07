<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createestudios_usuariosAPIRequest;
use App\Http\Requests\API\Updateestudios_usuariosAPIRequest;
use App\Models\estudios_usuarios;
use App\Repositories\estudios_usuariosRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class estudios_usuariosAPIController
 */
class estudios_usuariosAPIController extends AppBaseController
{
    private estudios_usuariosRepository $estudiosUsuariosRepository;

    public function __construct(estudios_usuariosRepository $estudiosUsuariosRepo)
    {
        $this->estudiosUsuariosRepository = $estudiosUsuariosRepo;
    }

    /**
     * Display a listing of the estudios_usuarios.
     * GET|HEAD /estudios_usuarios
     */
    public function index(Request $request): JsonResponse
    {
        $estudiosUsuarios = $this->estudiosUsuariosRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($estudiosUsuarios->toArray(), 'Estudios Usuarios retrieved successfully');
    }

    /**
     * Store a newly created estudios_usuarios in storage.
     * POST /estudios_usuarios
     */
    public function store(Createestudios_usuariosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $estudiosUsuarios = $this->estudiosUsuariosRepository->create($input);

        return $this->sendResponse($estudiosUsuarios->toArray(), 'Estudios Usuarios saved successfully');
    }

    /**
     * Display the specified estudios_usuarios.
     * GET|HEAD /estudios_usuarios/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var estudios_usuarios $estudiosUsuarios */
        $estudiosUsuarios = $this->estudiosUsuariosRepository->find($id);

        if (empty($estudiosUsuarios)) {
            return $this->sendError('Estudios Usuarios not found');
        }

        return $this->sendResponse($estudiosUsuarios->toArray(), 'Estudios Usuarios retrieved successfully');
    }

    /**
     * Update the specified estudios_usuarios in storage.
     * PUT/PATCH /estudios_usuarios/{id}
     */
    public function update($id, Updateestudios_usuariosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var estudios_usuarios $estudiosUsuarios */
        $estudiosUsuarios = $this->estudiosUsuariosRepository->find($id);

        if (empty($estudiosUsuarios)) {
            return $this->sendError('Estudios Usuarios not found');
        }

        $estudiosUsuarios = $this->estudiosUsuariosRepository->update($input, $id);

        return $this->sendResponse($estudiosUsuarios->toArray(), 'estudios_usuarios updated successfully');
    }

    /**
     * Remove the specified estudios_usuarios from storage.
     * DELETE /estudios_usuarios/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var estudios_usuarios $estudiosUsuarios */
        $estudiosUsuarios = $this->estudiosUsuariosRepository->find($id);

        if (empty($estudiosUsuarios)) {
            return $this->sendError('Estudios Usuarios not found');
        }

        $estudiosUsuarios->delete();

        return $this->sendSuccess('Estudios Usuarios deleted successfully');
    }
}
