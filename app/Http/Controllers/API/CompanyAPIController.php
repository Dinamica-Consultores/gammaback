<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatecompanyAPIRequest;
use App\Http\Requests\API\UpdatecompanyAPIRequest;
use App\Models\company;
use App\Repositories\companyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class companyAPIController
 */
class companyAPIController extends AppBaseController
{
    private companyRepository $companyRepository;

    public function __construct(companyRepository $companyRepo)
    {
        $this->companyRepository = $companyRepo;
    }

    /**
     * Display a listing of the companies.
     * GET|HEAD /companies
     */
    public function index(Request $request): JsonResponse
    {
        $companies = $this->companyRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($companies->toArray(), 'Companies retrieved successfully');
    }

    /**
     * Store a newly created company in storage.
     * POST /companies
     */
    public function store(CreatecompanyAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $company = $this->companyRepository->create($input);

        return $this->sendResponse($company->toArray(), 'Company saved successfully');
    }

    /**
     * Display the specified company.
     * GET|HEAD /companies/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var company $company */
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        return $this->sendResponse($company->toArray(), 'Company retrieved successfully');
    }

    /**
     * Update the specified company in storage.
     * PUT/PATCH /companies/{id}
     */
    public function update($id, UpdatecompanyAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var company $company */
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $company = $this->companyRepository->update($input, $id);

        return $this->sendResponse($company->toArray(), 'company updated successfully');
    }

    /**
     * Remove the specified company from storage.
     * DELETE /companies/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var company $company */
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $company->delete();

        return $this->sendSuccess('Company deleted successfully');
    }
}
