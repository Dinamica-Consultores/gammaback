<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Creategrupo_economicosAPIRequest;
use App\Http\Requests\API\Updategrupo_economicosAPIRequest;
use App\Models\grupo_economicos;
use App\Repositories\grupo_economicosRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class grupo_economicosAPIController
 */
class grupo_economicosAPIController extends AppBaseController
{
    private grupo_economicosRepository $grupoEconomicosRepository;

    public function __construct(grupo_economicosRepository $grupoEconomicosRepo)
    {
        $this->grupoEconomicosRepository = $grupoEconomicosRepo;
    }

    /**
     * Display a listing of the grupo_economicos.
     * GET|HEAD /grupo_economicos
     */
    public function index(Request $request): JsonResponse
    {
        $grupoEconomicos = $this->grupoEconomicosRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($grupoEconomicos->toArray(), 'Grupo Economicos retrieved successfully');
    }

    /**
     * Store a newly created grupo_economicos in storage.
     * POST /grupo_economicos
     */
    public function store(Creategrupo_economicosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $grupoEconomicos = $this->grupoEconomicosRepository->create($input);

        return $this->sendResponse($grupoEconomicos->toArray(), 'Grupo Economicos saved successfully');
    }

    /**
     * Display the specified grupo_economicos.
     * GET|HEAD /grupo_economicos/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var grupo_economicos $grupoEconomicos */
        $grupoEconomicos = $this->grupoEconomicosRepository->find($id);

        if (empty($grupoEconomicos)) {
            return $this->sendError('Grupo Economicos not found');
        }

        return $this->sendResponse($grupoEconomicos->toArray(), 'Grupo Economicos retrieved successfully');
    }

    /**
     * Update the specified grupo_economicos in storage.
     * PUT/PATCH /grupo_economicos/{id}
     */
    public function update($id, Updategrupo_economicosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var grupo_economicos $grupoEconomicos */
        $grupoEconomicos = $this->grupoEconomicosRepository->find($id);

        if (empty($grupoEconomicos)) {
            return $this->sendError('Grupo Economicos not found');
        }

        $grupoEconomicos = $this->grupoEconomicosRepository->update($input, $id);

        return $this->sendResponse($grupoEconomicos->toArray(), 'grupo_economicos updated successfully');
    }

    /**
     * Remove the specified grupo_economicos from storage.
     * DELETE /grupo_economicos/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var grupo_economicos $grupoEconomicos */
        $grupoEconomicos = $this->grupoEconomicosRepository->find($id);

        if (empty($grupoEconomicos)) {
            return $this->sendError('Grupo Economicos not found');
        }

        $grupoEconomicos->delete();

        return $this->sendSuccess('Grupo Economicos deleted successfully');
    }
}
