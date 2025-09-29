<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createcompromiso_entregaAPIRequest;
use App\Http\Requests\API\Updatecompromiso_entregaAPIRequest;
use App\Models\compromiso_entrega;
use App\Repositories\compromiso_entregaRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class compromiso_entregaAPIController
 */
class compromiso_entregaAPIController extends AppBaseController
{
    private compromiso_entregaRepository $compromisoEntregaRepository;

    public function __construct(compromiso_entregaRepository $compromisoEntregaRepo)
    {
        $this->compromisoEntregaRepository = $compromisoEntregaRepo;
    }

    /**
     * Display a listing of the compromiso_entregas.
     * GET|HEAD /compromiso_entregas
     */
    public function index(Request $request): JsonResponse
    {
        $compromisoEntregas = $this->compromisoEntregaRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($compromisoEntregas->toArray(), 'Compromiso Entregas retrieved successfully');
    }

    /**
     * Store a newly created compromiso_entrega in storage.
     * POST /compromiso_entregas
     */
    public function store(Createcompromiso_entregaAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $compromisoEntrega = $this->compromisoEntregaRepository->create($input);

        return $this->sendResponse($compromisoEntrega->toArray(), 'Compromiso Entrega saved successfully');
    }

    /**
     * Display the specified compromiso_entrega.
     * GET|HEAD /compromiso_entregas/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var compromiso_entrega $compromisoEntrega */
        $compromisoEntrega = $this->compromisoEntregaRepository->find($id);

        if (empty($compromisoEntrega)) {
            return $this->sendError('Compromiso Entrega not found');
        }

        return $this->sendResponse($compromisoEntrega->toArray(), 'Compromiso Entrega retrieved successfully');
    }

    /**
     * Update the specified compromiso_entrega in storage.
     * PUT/PATCH /compromiso_entregas/{id}
     */
    public function update($id, Updatecompromiso_entregaAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var compromiso_entrega $compromisoEntrega */
        $compromisoEntrega = $this->compromisoEntregaRepository->find($id);

        if (empty($compromisoEntrega)) {
            return $this->sendError('Compromiso Entrega not found');
        }

        $compromisoEntrega = $this->compromisoEntregaRepository->update($input, $id);

        return $this->sendResponse($compromisoEntrega->toArray(), 'compromiso_entrega updated successfully');
    }

    /**
     * Remove the specified compromiso_entrega from storage.
     * DELETE /compromiso_entregas/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var compromiso_entrega $compromisoEntrega */
        $compromisoEntrega = $this->compromisoEntregaRepository->find($id);

        if (empty($compromisoEntrega)) {
            return $this->sendError('Compromiso Entrega not found');
        }

        $compromisoEntrega->delete();

        return $this->sendSuccess('Compromiso Entrega deleted successfully');
    }
}
