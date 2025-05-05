<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createbitacora_hitosAPIRequest;
use App\Http\Requests\API\Updatebitacora_hitosAPIRequest;
use App\Models\bitacora_hitos;
use App\Repositories\bitacora_hitosRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class bitacora_hitosAPIController
 */
class bitacora_hitosAPIController extends AppBaseController
{
    private bitacora_hitosRepository $bitacoraHitosRepository;

    public function __construct(bitacora_hitosRepository $bitacoraHitosRepo)
    {
        $this->bitacoraHitosRepository = $bitacoraHitosRepo;
    }

    /**
     * Display a listing of the bitacora_hitos.
     * GET|HEAD /bitacora_hitos
     */
    public function index(Request $request): JsonResponse
    {
        $bitacoraHitos = $this->bitacoraHitosRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($bitacoraHitos->toArray(), 'Bitacora Hitos retrieved successfully');
    }

    /**
     * Store a newly created bitacora_hitos in storage.
     * POST /bitacora_hitos
     */
    public function store(Createbitacora_hitosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $bitacoraHitos = $this->bitacoraHitosRepository->create($input);

        return $this->sendResponse($bitacoraHitos->toArray(), 'Bitacora Hitos saved successfully');
    }

    /**
     * Display the specified bitacora_hitos.
     * GET|HEAD /bitacora_hitos/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var bitacora_hitos $bitacoraHitos */
        $bitacoraHitos = $this->bitacoraHitosRepository->find($id);

        if (empty($bitacoraHitos)) {
            return $this->sendError('Bitacora Hitos not found');
        }

        return $this->sendResponse($bitacoraHitos->toArray(), 'Bitacora Hitos retrieved successfully');
    }

    /**
     * Update the specified bitacora_hitos in storage.
     * PUT/PATCH /bitacora_hitos/{id}
     */
    public function update($id, Updatebitacora_hitosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var bitacora_hitos $bitacoraHitos */
        $bitacoraHitos = $this->bitacoraHitosRepository->find($id);

        if (empty($bitacoraHitos)) {
            return $this->sendError('Bitacora Hitos not found');
        }

        $bitacoraHitos = $this->bitacoraHitosRepository->update($input, $id);

        return $this->sendResponse($bitacoraHitos->toArray(), 'bitacora_hitos updated successfully');
    }

    /**
     * Remove the specified bitacora_hitos from storage.
     * DELETE /bitacora_hitos/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var bitacora_hitos $bitacoraHitos */
        $bitacoraHitos = $this->bitacoraHitosRepository->find($id);

        if (empty($bitacoraHitos)) {
            return $this->sendError('Bitacora Hitos not found');
        }

        $bitacoraHitos->delete();

        return $this->sendSuccess('Bitacora Hitos deleted successfully');
    }
}
