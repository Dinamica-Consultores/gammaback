<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createtipo_cambios_globalAPIRequest;
use App\Http\Requests\API\Updatetipo_cambios_globalAPIRequest;
use App\Models\tipo_cambios_global;
use App\Repositories\tipo_cambios_globalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class tipo_cambios_globalAPIController
 */
class tipo_cambios_globalAPIController extends AppBaseController
{
    private tipo_cambios_globalRepository $tipoCambiosGlobalRepository;

    public function __construct(tipo_cambios_globalRepository $tipoCambiosGlobalRepo)
    {
        $this->tipoCambiosGlobalRepository = $tipoCambiosGlobalRepo;
    }

    /**
     * Display a listing of the tipo_cambios_globals.
     * GET|HEAD /tipo_cambios_globals
     */
    public function index(Request $request): JsonResponse
    {
        $tipoCambiosGlobals = $this->tipoCambiosGlobalRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($tipoCambiosGlobals->toArray(), 'Tipo Cambios Globals retrieved successfully');
    }

    /**
     * Store a newly created tipo_cambios_global in storage.
     * POST /tipo_cambios_globals
     */
    public function store(Createtipo_cambios_globalAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->create($input);

        return $this->sendResponse($tipoCambiosGlobal->toArray(), 'Tipo Cambios Global saved successfully');
    }

    /**
     * Display the specified tipo_cambios_global.
     * GET|HEAD /tipo_cambios_globals/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var tipo_cambios_global $tipoCambiosGlobal */
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->find($id);

        if (empty($tipoCambiosGlobal)) {
            return $this->sendError('Tipo Cambios Global not found');
        }

        return $this->sendResponse($tipoCambiosGlobal->toArray(), 'Tipo Cambios Global retrieved successfully');
    }

    /**
     * Update the specified tipo_cambios_global in storage.
     * PUT/PATCH /tipo_cambios_globals/{id}
     */
    public function update($id, Updatetipo_cambios_globalAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var tipo_cambios_global $tipoCambiosGlobal */
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->find($id);

        if (empty($tipoCambiosGlobal)) {
            return $this->sendError('Tipo Cambios Global not found');
        }

        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->update($input, $id);

        return $this->sendResponse($tipoCambiosGlobal->toArray(), 'tipo_cambios_global updated successfully');
    }

    /**
     * Remove the specified tipo_cambios_global from storage.
     * DELETE /tipo_cambios_globals/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var tipo_cambios_global $tipoCambiosGlobal */
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->find($id);

        if (empty($tipoCambiosGlobal)) {
            return $this->sendError('Tipo Cambios Global not found');
        }

        $tipoCambiosGlobal->delete();

        return $this->sendSuccess('Tipo Cambios Global deleted successfully');
    }
}
