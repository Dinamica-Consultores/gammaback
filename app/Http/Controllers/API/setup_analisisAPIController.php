<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createsetup_analisisAPIRequest;
use App\Http\Requests\API\Updatesetup_analisisAPIRequest;
use App\Models\setup_analisis;
use App\Repositories\setup_analisisRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class setup_analisisAPIController
 */
class setup_analisisAPIController extends AppBaseController
{
    private setup_analisisRepository $setupAnalisisRepository;

    public function __construct(setup_analisisRepository $setupAnalisisRepo)
    {
        $this->setupAnalisisRepository = $setupAnalisisRepo;
    }

    /**
     * Display a listing of the setup_analises.
     * GET|HEAD /setup_analises
     */
    public function index(Request $request): JsonResponse
    {
        $setupAnalises = $this->setupAnalisisRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($setupAnalises->toArray(), 'Setup Analises retrieved successfully');
    }

    /**
     * Store a newly created setup_analisis in storage.
     * POST /setup_analises
     */
    public function store(Createsetup_analisisAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $setupAnalisis = $this->setupAnalisisRepository->create($input);

        return $this->sendResponse($setupAnalisis->toArray(), 'Setup Analisis saved successfully');
    }

    /**
     * Display the specified setup_analisis.
     * GET|HEAD /setup_analises/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var setup_analisis $setupAnalisis */
        $setupAnalisis = $this->setupAnalisisRepository->find($id);

        if (empty($setupAnalisis)) {
            return $this->sendError('Setup Analisis not found');
        }

        return $this->sendResponse($setupAnalisis->toArray(), 'Setup Analisis retrieved successfully');
    }

    /**
     * Update the specified setup_analisis in storage.
     * PUT/PATCH /setup_analises/{id}
     */
    public function update($id, Updatesetup_analisisAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var setup_analisis $setupAnalisis */
        $setupAnalisis = $this->setupAnalisisRepository->find($id);

        if (empty($setupAnalisis)) {
            return $this->sendError('Setup Analisis not found');
        }

        $setupAnalisis = $this->setupAnalisisRepository->update($input, $id);

        return $this->sendResponse($setupAnalisis->toArray(), 'setup_analisis updated successfully');
    }

    /**
     * Remove the specified setup_analisis from storage.
     * DELETE /setup_analises/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var setup_analisis $setupAnalisis */
        $setupAnalisis = $this->setupAnalisisRepository->find($id);

        if (empty($setupAnalisis)) {
            return $this->sendError('Setup Analisis not found');
        }

        $setupAnalisis->delete();

        return $this->sendSuccess('Setup Analisis deleted successfully');
    }
}
