<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatesucursalesAPIRequest;
use App\Http\Requests\API\UpdatesucursalesAPIRequest;
use App\Models\sucursales;
use App\Repositories\sucursalesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class sucursalesAPIController
 */
class sucursalesAPIController extends AppBaseController
{
    private sucursalesRepository $sucursalesRepository;

    public function __construct(sucursalesRepository $sucursalesRepo)
    {
        $this->sucursalesRepository = $sucursalesRepo;
    }

    /**
     * Display a listing of the sucursales.
     * GET|HEAD /sucursales
     */
    public function index(Request $request): JsonResponse
    {
        $sucursales = $this->sucursalesRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($sucursales->toArray(), 'Sucursales retrieved successfully');
    }

    /**
     * Store a newly created sucursales in storage.
     * POST /sucursales
     */
    public function store(CreatesucursalesAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $sucursales = $this->sucursalesRepository->create($input);

        return $this->sendResponse($sucursales->toArray(), 'Sucursales saved successfully');
    }

    /**
     * Display the specified sucursales.
     * GET|HEAD /sucursales/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var sucursales $sucursales */
        $sucursales = $this->sucursalesRepository->find($id);

        if (empty($sucursales)) {
            return $this->sendError('Sucursales not found');
        }

        return $this->sendResponse($sucursales->toArray(), 'Sucursales retrieved successfully');
    }

    /**
     * Update the specified sucursales in storage.
     * PUT/PATCH /sucursales/{id}
     */
    public function update($id, UpdatesucursalesAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var sucursales $sucursales */
        $sucursales = $this->sucursalesRepository->find($id);

        if (empty($sucursales)) {
            return $this->sendError('Sucursales not found');
        }

        $sucursales = $this->sucursalesRepository->update($input, $id);

        return $this->sendResponse($sucursales->toArray(), 'sucursales updated successfully');
    }

    /**
     * Remove the specified sucursales from storage.
     * DELETE /sucursales/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var sucursales $sucursales */
        $sucursales = $this->sucursalesRepository->find($id);

        if (empty($sucursales)) {
            return $this->sendError('Sucursales not found');
        }

        $sucursales->delete();

        return $this->sendSuccess('Sucursales deleted successfully');
    }
}
