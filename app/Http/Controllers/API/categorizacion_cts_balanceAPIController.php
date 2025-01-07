<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createcategorizacion_cts_balanceAPIRequest;
use App\Http\Requests\API\Updatecategorizacion_cts_balanceAPIRequest;
use App\Models\categorizacion_cts_balance;
use App\Repositories\categorizacion_cts_balanceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class categorizacion_cts_balanceAPIController
 */
class categorizacion_cts_balanceAPIController extends AppBaseController
{
    private categorizacion_cts_balanceRepository $categorizacionCtsBalanceRepository;

    public function __construct(categorizacion_cts_balanceRepository $categorizacionCtsBalanceRepo)
    {
        $this->categorizacionCtsBalanceRepository = $categorizacionCtsBalanceRepo;
    }

    /**
     * Display a listing of the categorizacion_cts_balances.
     * GET|HEAD /categorizacion_cts_balances
     */
    public function index(Request $request): JsonResponse
    {
        $categorizacionCtsBalances = $this->categorizacionCtsBalanceRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($categorizacionCtsBalances->toArray(), 'Categorizacion Cts Balances retrieved successfully');
    }

    /**
     * Store a newly created categorizacion_cts_balance in storage.
     * POST /categorizacion_cts_balances
     */
    public function store(Createcategorizacion_cts_balanceAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->create($input);

        return $this->sendResponse($categorizacionCtsBalance->toArray(), 'Categorizacion Cts Balance saved successfully');
    }

    /**
     * Display the specified categorizacion_cts_balance.
     * GET|HEAD /categorizacion_cts_balances/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var categorizacion_cts_balance $categorizacionCtsBalance */
        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->find($id);

        if (empty($categorizacionCtsBalance)) {
            return $this->sendError('Categorizacion Cts Balance not found');
        }

        return $this->sendResponse($categorizacionCtsBalance->toArray(), 'Categorizacion Cts Balance retrieved successfully');
    }

    /**
     * Update the specified categorizacion_cts_balance in storage.
     * PUT/PATCH /categorizacion_cts_balances/{id}
     */
    public function update($id, Updatecategorizacion_cts_balanceAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var categorizacion_cts_balance $categorizacionCtsBalance */
        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->find($id);

        if (empty($categorizacionCtsBalance)) {
            return $this->sendError('Categorizacion Cts Balance not found');
        }

        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->update($input, $id);

        return $this->sendResponse($categorizacionCtsBalance->toArray(), 'categorizacion_cts_balance updated successfully');
    }

    /**
     * Remove the specified categorizacion_cts_balance from storage.
     * DELETE /categorizacion_cts_balances/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var categorizacion_cts_balance $categorizacionCtsBalance */
        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->find($id);

        if (empty($categorizacionCtsBalance)) {
            return $this->sendError('Categorizacion Cts Balance not found');
        }

        $categorizacionCtsBalance->delete();

        return $this->sendSuccess('Categorizacion Cts Balance deleted successfully');
    }
}
