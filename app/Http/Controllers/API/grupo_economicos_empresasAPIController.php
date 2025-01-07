<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Creategrupo_economicos_empresasAPIRequest;
use App\Http\Requests\API\Updategrupo_economicos_empresasAPIRequest;
use App\Models\grupo_economicos_empresas;
use App\Models\company;
use App\Repositories\grupo_economicos_empresasRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class grupo_economicos_empresasAPIController
 */
class grupo_economicos_empresasAPIController extends AppBaseController
{
    private grupo_economicos_empresasRepository $grupoEconomicosEmpresasRepository;

    public function __construct(grupo_economicos_empresasRepository $grupoEconomicosEmpresasRepo)
    {
        $this->grupoEconomicosEmpresasRepository = $grupoEconomicosEmpresasRepo;
    }

    /**
     * Display a listing of the grupo_economicos_empresas.
     * GET|HEAD /grupo_economicos_empresas
     */
    public function index(Request $request): JsonResponse
    {
        $grupoEconomicosEmpresas = $this->grupoEconomicosEmpresasRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($grupoEconomicosEmpresas->toArray(), 'Grupo Economicos Empresas retrieved successfully');
    }

    /**
     * Store a newly created grupo_economicos_empresas in storage.
     * POST /grupo_economicos_empresas
     */
    public function store(Creategrupo_economicos_empresasAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $grupoEconomicosEmpresas = $this->grupoEconomicosEmpresasRepository->create($input);

        return $this->sendResponse($grupoEconomicosEmpresas->toArray(), 'Grupo Economicos Empresas saved successfully');
    }
    public function getAllCompanies(){
        
        $user =auth()->guard('api')->user();
        $grupoEconomicosEmpresas=company::SELECT('companies.*')->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id')->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show)->get();
        return $this->sendResponse($grupoEconomicosEmpresas->toArray(), 'Grupo Economicos Empresas saved successfully');
    }
    /**
     * Display the specified grupo_economicos_empresas.
     * GET|HEAD /grupo_economicos_empresas/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var grupo_economicos_empresas $grupoEconomicosEmpresas */
        $grupoEconomicosEmpresas = $this->grupoEconomicosEmpresasRepository->find($id);

        if (empty($grupoEconomicosEmpresas)) {
            return $this->sendError('Grupo Economicos Empresas not found');
        }

        return $this->sendResponse($grupoEconomicosEmpresas->toArray(), 'Grupo Economicos Empresas retrieved successfully');
    }

    /**
     * Update the specified grupo_economicos_empresas in storage.
     * PUT/PATCH /grupo_economicos_empresas/{id}
     */
    public function update($id, Updategrupo_economicos_empresasAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var grupo_economicos_empresas $grupoEconomicosEmpresas */
        $grupoEconomicosEmpresas = $this->grupoEconomicosEmpresasRepository->find($id);

        if (empty($grupoEconomicosEmpresas)) {
            return $this->sendError('Grupo Economicos Empresas not found');
        }

        $grupoEconomicosEmpresas = $this->grupoEconomicosEmpresasRepository->update($input, $id);

        return $this->sendResponse($grupoEconomicosEmpresas->toArray(), 'grupo_economicos_empresas updated successfully');
    }

    /**
     * Remove the specified grupo_economicos_empresas from storage.
     * DELETE /grupo_economicos_empresas/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var grupo_economicos_empresas $grupoEconomicosEmpresas */
        $grupoEconomicosEmpresas = $this->grupoEconomicosEmpresasRepository->find($id);

        if (empty($grupoEconomicosEmpresas)) {
            return $this->sendError('Grupo Economicos Empresas not found');
        }

        $grupoEconomicosEmpresas->delete();

        return $this->sendSuccess('Grupo Economicos Empresas deleted successfully');
    }
}
