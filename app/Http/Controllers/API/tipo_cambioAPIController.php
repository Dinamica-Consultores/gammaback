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
        $sqlCheck = tipo_cambio::SELECT(DB::raw('tipo_cambios.id_excel,tipo_cambios.ipc_empresa'))
        ->join('excelscompanies', 'excelscompanies.id', 'tipo_cambios.id_excel')
        ->join('companies', 'companies.id', 'excelscompanies.id_company'); 
        $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
        if($user->id_company_show>0){
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }
        $sqlCheck = $sqlCheck->where('tipo_cambios.ano', $year)->where('tipo_cambios.mes', $month);
        $data = $sqlCheck->get();

        $sqlCheck2 = tipo_cambios_global::SELECT('*')->where('tipo_cambios_globals.ano', $year)->where('tipo_cambios_globals.mes', $month);
        $data2 = $sqlCheck2->get();
        $info=array(
            'idGlobal'=>0,
            'id'=>0,
            'dolar_compra'=>0,
            'dolar_venta'=>0,
            'dolar_promedio'=>0,
            'euro_promedio'=>0,
            'francosuizo_promedio'=>0,
            'ui'=>0,
            'ipc'=>0,
            'mes'=>0,
            'ano'=>0,
            'ipc_empresa'=>0,
            'id_excel'=>0,
        );
        if(count($data2)>0){
            $info['id']=$data2[0]['id'];
            $info['dolar_compra']=$data2[0]['dolar_compra'];
            $info['dolar_venta']=$data2[0]['dolar_venta'];
            $info['dolar_promedio']=$data2[0]['dolar_promedio'];
            $info['euro_promedio']=$data2[0]['euro_promedio'];
            $info['francosuizo_promedio']=$data2[0]['francosuizo_promedio'];
            $info['ui']=$data2[0]['ui'];
            $info['ipc']=$data2[0]['ipc'];
            $info['mes']=$data2[0]['mes'];
            $info['ano']=$data2[0]['ano'];
        }
        if(count($data)>0){

            $info['id_excel']=$data[0]['id_excel'];
            $info['ipc_empresa']=$data[0]['ipc_empresa'];
        }
        return $this->sendResponse(array($info), 'Get Data TipoCambios');
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
