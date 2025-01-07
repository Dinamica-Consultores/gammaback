<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateestudiosAPIRequest;
use App\Http\Requests\API\UpdateestudiosAPIRequest;
use App\Models\estudios;
use App\Repositories\estudiosRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class estudiosAPIController
 */
class estudiosAPIController extends AppBaseController
{
    private estudiosRepository $estudiosRepository;

    public function __construct(estudiosRepository $estudiosRepo)
    {
        $this->estudiosRepository = $estudiosRepo;
    }

    /**
     * Display a listing of the estudios.
     * GET|HEAD /estudios
     */
    public function index(Request $request): JsonResponse
    {
        $estudios = $this->estudiosRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($estudios->toArray(), 'Estudios retrieved successfully');
    }

    /**
     * Store a newly created estudios in storage.
     * POST /estudios
     */
    public function store(CreateestudiosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $estudios = $this->estudiosRepository->create($input);

        return $this->sendResponse($estudios->toArray(), 'Estudios saved successfully');
    }

    /**
     * Display the specified estudios.
     * GET|HEAD /estudios/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var estudios $estudios */
        $estudios = $this->estudiosRepository->find($id);

        if (empty($estudios)) {
            return $this->sendError('Estudios not found');
        }

        return $this->sendResponse($estudios->toArray(), 'Estudios retrieved successfully');
    }

    /**
     * Update the specified estudios in storage.
     * PUT/PATCH /estudios/{id}
     */
    public function update($id, UpdateestudiosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var estudios $estudios */
        $estudios = $this->estudiosRepository->find($id);

        if (empty($estudios)) {
            return $this->sendError('Estudios not found');
        }

        $estudios = $this->estudiosRepository->update($input, $id);

        return $this->sendResponse($estudios->toArray(), 'estudios updated successfully');
    }

    /**
     * Remove the specified estudios from storage.
     * DELETE /estudios/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var estudios $estudios */
        $estudios = $this->estudiosRepository->find($id);

        if (empty($estudios)) {
            return $this->sendError('Estudios not found');
        }

        $estudios->delete();

        return $this->sendSuccess('Estudios deleted successfully');
    }
}
