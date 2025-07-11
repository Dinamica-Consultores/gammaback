<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createtipo_cambioAPIRequest;
use App\Http\Requests\API\Updatetipo_cambioAPIRequest;
use App\Models\tipo_cambio;
use App\Models\tipo_cambios_global;
use App\Repositories\tipo_cambioRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class tipo_cambioAPIController
 */
class tipo_cambioAPIController extends AppBaseController
{
    private tipo_cambioRepository $tipoCambioRepository;

    public function __construct(tipo_cambioRepository $tipoCambioRepo)
    {
        $this->tipoCambioRepository = $tipoCambioRepo;
    }

    /**
     * Display a listing of the tipo_cambios.
     * GET|HEAD /tipo_cambios
     */
    public function index(Request $request): JsonResponse
    {
        $tipoCambios = $this->tipoCambioRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($tipoCambios->toArray(), 'Tipo Cambios retrieved successfully');
    }
    public function getTipoCambio($year,$month){

        $user = auth()->guard('api')->user();
        $sqlCheck = tipo_cambios_global::SELECT(DB::raw('tipo_cambios.id_excel,tipo_cambios.ipc_empresa,tipo_cambios_globals.*'))
        ->join('tipo_cambios',function($join){
            $join->on('tipo_cambios.mes','=','tipo_cambios_globals.mes')
            ->on('tipo_cambios.ano','=','tipo_cambios_globals.ano');
        })->join('excelscompanies', 'excelscompanies.id', 'tipo_cambios.id_excel')
        ->join('companies', 'companies.id', 'excelscompanies.id_company'); 
        $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
        if($user->id_company_show>0){
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }
        
        $sqlCheck = $sqlCheck->where('tipo_cambios_globals.ano', $year)->where('tipo_cambios_globals.mes', $month);
        $data = $sqlCheck->get();
        return $this->sendResponse($data, 'Get Data TipoCambios');
    }
    /**
     * Store a newly created tipo_cambio in storage.
     * POST /tipo_cambios
     */
    public function store(Createtipo_cambioAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $tipoCambio = $this->tipoCambioRepository->create($input);

        return $this->sendResponse($tipoCambio->toArray(), 'Tipo Cambio saved successfully');
    }

    /**
     * Display the specified tipo_cambio.
     * GET|HEAD /tipo_cambios/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var tipo_cambio $tipoCambio */
        $tipoCambio = $this->tipoCambioRepository->find($id);

        if (empty($tipoCambio)) {
            return $this->sendError('Tipo Cambio not found');
        }

        return $this->sendResponse($tipoCambio->toArray(), 'Tipo Cambio retrieved successfully');
    }

    /**
     * Update the specified tipo_cambio in storage.
     * PUT/PATCH /tipo_cambios/{id}
     */
    public function update($id, Updatetipo_cambioAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var tipo_cambio $tipoCambio */
        $tipoCambio = $this->tipoCambioRepository->find($id);

        if (empty($tipoCambio)) {
            return $this->sendError('Tipo Cambio not found');
        }

        $tipoCambio = $this->tipoCambioRepository->update($input, $id);

        return $this->sendResponse($tipoCambio->toArray(), 'tipo_cambio updated successfully');
    }

    /**
     * Remove the specified tipo_cambio from storage.
     * DELETE /tipo_cambios/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var tipo_cambio $tipoCambio */
        $tipoCambio = $this->tipoCambioRepository->find($id);

        if (empty($tipoCambio)) {
            return $this->sendError('Tipo Cambio not found');
        }

        $tipoCambio->delete();

        return $this->sendSuccess('Tipo Cambio deleted successfully');
    }
}
