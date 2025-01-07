<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createclasificacion_cuenta_resulAPIRequest;
use App\Http\Requests\API\Updateclasificacion_cuenta_resulAPIRequest;
use App\Models\clasificacion_cuenta_resul;
use App\Repositories\clasificacion_cuenta_resulRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class clasificacion_cuenta_resulAPIController
 */
class clasificacion_cuenta_resulAPIController extends AppBaseController
{
    private clasificacion_cuenta_resulRepository $clasificacionCuentaResulRepository;

    public function __construct(clasificacion_cuenta_resulRepository $clasificacionCuentaResulRepo)
    {
        $this->clasificacionCuentaResulRepository = $clasificacionCuentaResulRepo;
    }

    /**
     * Display a listing of the clasificacion_cuenta_resuls.
     * GET|HEAD /clasificacion_cuenta_resuls
     */
    public function index(Request $request): JsonResponse
    {
        $clasificacionCuentaResuls = $this->clasificacionCuentaResulRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($clasificacionCuentaResuls->toArray(), 'Clasificacion Cuenta Resuls retrieved successfully');
    }

    /**
     * Store a newly created clasificacion_cuenta_resul in storage.
     * POST /clasificacion_cuenta_resuls
     */
    public function store(Createclasificacion_cuenta_resulAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->create($input);

        return $this->sendResponse($clasificacionCuentaResul->toArray(), 'Clasificacion Cuenta Resul saved successfully');
    }

    /**
     * Display the specified clasificacion_cuenta_resul.
     * GET|HEAD /clasificacion_cuenta_resuls/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var clasificacion_cuenta_resul $clasificacionCuentaResul */
        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->find($id);

        if (empty($clasificacionCuentaResul)) {
            return $this->sendError('Clasificacion Cuenta Resul not found');
        }

        return $this->sendResponse($clasificacionCuentaResul->toArray(), 'Clasificacion Cuenta Resul retrieved successfully');
    }

    /**
     * Update the specified clasificacion_cuenta_resul in storage.
     * PUT/PATCH /clasificacion_cuenta_resuls/{id}
     */
    public function update($id, Updateclasificacion_cuenta_resulAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var clasificacion_cuenta_resul $clasificacionCuentaResul */
        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->find($id);

        if (empty($clasificacionCuentaResul)) {
            return $this->sendError('Clasificacion Cuenta Resul not found');
        }

        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->update($input, $id);

        return $this->sendResponse($clasificacionCuentaResul->toArray(), 'clasificacion_cuenta_resul updated successfully');
    }

    /**
     * Remove the specified clasificacion_cuenta_resul from storage.
     * DELETE /clasificacion_cuenta_resuls/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var clasificacion_cuenta_resul $clasificacionCuentaResul */
        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->find($id);

        if (empty($clasificacionCuentaResul)) {
            return $this->sendError('Clasificacion Cuenta Resul not found');
        }

        $clasificacionCuentaResul->delete();

        return $this->sendSuccess('Clasificacion Cuenta Resul deleted successfully');
    }
}
