<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateexcelscompanyAPIRequest;
use App\Http\Requests\API\UpdateexcelscompanyAPIRequest;
use App\Models\excelscompany;
use App\Repositories\excelscompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class excelscompanyAPIController
 */
class excelscompanyAPIController extends AppBaseController
{
    private excelscompanyRepository $excelscompanyRepository;

    public function __construct(excelscompanyRepository $excelscompanyRepo)
    {
        $this->excelscompanyRepository = $excelscompanyRepo;
    }

    /**
     * Display a listing of the excelscompanies.
     * GET|HEAD /excelscompanies
     */
    public function index(Request $request): JsonResponse
    {
        $excelscompanies = $this->excelscompanyRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($excelscompanies->toArray(), 'Excelscompanies retrieved successfully');
    }

    /**
     * Store a newly created excelscompany in storage.
     * POST /excelscompanies
     */
    public function store(CreateexcelscompanyAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $excelscompany = $this->excelscompanyRepository->create($input);

        return $this->sendResponse($excelscompany->toArray(), 'Excelscompany saved successfully');
    }

    /**
     * Display the specified excelscompany.
     * GET|HEAD /excelscompanies/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var excelscompany $excelscompany */
        $excelscompany = $this->excelscompanyRepository->find($id);

        if (empty($excelscompany)) {
            return $this->sendError('Excelscompany not found');
        }

        return $this->sendResponse($excelscompany->toArray(), 'Excelscompany retrieved successfully');
    }

    /**
     * Update the specified excelscompany in storage.
     * PUT/PATCH /excelscompanies/{id}
     */
    public function update($id, UpdateexcelscompanyAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var excelscompany $excelscompany */
        $excelscompany = $this->excelscompanyRepository->find($id);

        if (empty($excelscompany)) {
            return $this->sendError('Excelscompany not found');
        }

        $excelscompany = $this->excelscompanyRepository->update($input, $id);

        return $this->sendResponse($excelscompany->toArray(), 'excelscompany updated successfully');
    }

    /**
     * Remove the specified excelscompany from storage.
     * DELETE /excelscompanies/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var excelscompany $excelscompany */
        $excelscompany = $this->excelscompanyRepository->find($id);

        if (empty($excelscompany)) {
            return $this->sendError('Excelscompany not found');
        }

        $excelscompany->delete();

        return $this->sendSuccess('Excelscompany deleted successfully');
    }
}
