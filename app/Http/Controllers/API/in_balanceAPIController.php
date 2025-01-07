<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createin_balanceAPIRequest;
use App\Http\Requests\API\Updatein_balanceAPIRequest;
use App\Models\in_balance;
use App\Repositories\in_balanceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class in_balanceAPIController
 */
class in_balanceAPIController extends AppBaseController
{
    private in_balanceRepository $inBalanceRepository;

    public function __construct(in_balanceRepository $inBalanceRepo)
    {
        $this->inBalanceRepository = $inBalanceRepo;
    }

    /**
     * Display a listing of the in_balances.
     * GET|HEAD /in_balances
     */
    public function index(Request $request): JsonResponse
    {
        $inBalances = $this->inBalanceRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($inBalances->toArray(), 'In Balances retrieved successfully');
    }
    public function showEstadoDash($year,$month,$sucursal):JsonResponse
    {
        $select='SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))) as amount_uyu,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        categorizacion_cts_balances.nivel_3,
              categorizacion_cts_balances.nivel_4,
         categorizacion_cts_balances.nivel_2,
        in_balances.ano,
        in_balances.mes';
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck=in_balance::SELECT(DB::raw($select))
        ->join('excelscompanies','excelscompanies.id','in_balances.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
            {
                $join->on('tipo_cambios.mes','=','in_balances.mes')
                ->on('tipo_cambios.ano','=','in_balances.ano')
                ->on('tipo_cambios.id_excel','=','in_balances.id_excel');
            })
        ->join('categorizacion_cts_balances',function($join){
            $join->on("categorizacion_cts_balances.id_excel","=","in_balances.id_excel")
                ->on("categorizacion_cts_balances.cuenta","=","in_balances.cuenta_master");
        });
        $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
        if($user->id_company_show>0){
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }

        if($sucursal>0){
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_balances.id_excel")
                    ->on("sucursales.nombre","=","in_balances.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if($year>0 && $month>0){
            $sqlCheck= $sqlCheck->where('in_balances.mes',$month);
            $sqlCheck= $sqlCheck->where('in_balances.ano',$year);
        }else{
            return $this->sendResponse([], 'In Balances retrieved successfully');  
        }
        
        $sqlCheck = $sqlCheck->groupByRaw('nivel_2,nivel_3,ano,mes,nivel_4');
        $data=$sqlCheck->get();
        
        return $this->sendResponse($data, 'In Balances retrieved successfully');  
    }
    public function showEstadoSitacionPatrim($year,$month,$yearCierre,$monthCierre,$sucursal):JsonResponse
    {
        $select='CAST(in_balances.saldo_uyu AS DECIMAL(18,3))as amount_uyu,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        categorizacion_cts_balances.nivel_3,
              categorizacion_cts_balances.nivel_4,
        categorizacion_cts_balances.nombre,
        categorizacion_cts_balances.nivel_2,
        categorizacion_cts_balances.nivel_1,
        in_balances.ano,
        in_balances.mes,
        categorizacion_cts_balances.posicion_moneda,
        categorizacion_cts_balances.posicion_fiscal';
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);

        $sqlCheck=in_balance::SELECT(DB::raw($select))
        ->join('excelscompanies','excelscompanies.id','in_balances.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
            {
                $join->on('tipo_cambios.mes','=','in_balances.mes')
                ->on('tipo_cambios.ano','=','in_balances.ano')
                ->on('tipo_cambios.id_excel','=','in_balances.id_excel');
            })
        ->join('categorizacion_cts_balances',function($join){
            $join->on("categorizacion_cts_balances.id_excel","=","in_balances.id_excel")
                ->on("categorizacion_cts_balances.cuenta","=","in_balances.cuenta_master");
        });
        
        $sqlCheck2=in_balance::SELECT(DB::raw($select))
        ->join('excelscompanies','excelscompanies.id','in_balances.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
        {
            $join->on('tipo_cambios.mes','=','in_balances.mes')
            ->on('tipo_cambios.ano','=','in_balances.ano')
            ->on('tipo_cambios.id_excel','=','in_balances.id_excel');
        })
        ->join('categorizacion_cts_balances',function($join){
            $join->on("categorizacion_cts_balances.id_excel","=","in_balances.id_excel")
                ->on("categorizacion_cts_balances.cuenta","=","in_balances.cuenta_master");
        });

        $sqlCheck3=in_balance::SELECT(DB::raw($select))
        ->join('excelscompanies','excelscompanies.id','in_balances.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
        {
            $join->on('tipo_cambios.mes','=','in_balances.mes')
            ->on('tipo_cambios.ano','=','in_balances.ano')
            ->on('tipo_cambios.id_excel','=','in_balances.id_excel');
        })
        ->join('categorizacion_cts_balances',function($join){
            $join->on("categorizacion_cts_balances.id_excel","=","in_balances.id_excel")
                ->on("categorizacion_cts_balances.cuenta","=","in_balances.cuenta_master");
        });

        $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);

        $sqlCheck2= $sqlCheck2->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck2= $sqlCheck2->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck2= $sqlCheck2->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck2= $sqlCheck2->where('usuario_grupoeconomicos.id_users',$user->id);
       
        $sqlCheck3= $sqlCheck3->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck3= $sqlCheck3->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck3= $sqlCheck3->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck3= $sqlCheck3->where('usuario_grupoeconomicos.id_users',$user->id);
       
        if($user->id_company_show>0){
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
            $sqlCheck2= $sqlCheck2->where('grupo_economicos_empresas.id_company',$user->id_company_show);
            $sqlCheck3= $sqlCheck3->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }
        if($sucursal>0){
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_balances.id_excel")
                    ->on("sucursales.nombre","=","in_balances.sucursal");
            });
            $sqlCheck2=$sqlCheck2->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_balances.id_excel")
                    ->on("sucursales.nombre","=","in_balances.sucursal");
            });
            $sqlCheck3=$sqlCheck3->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_balances.id_excel")
                    ->on("sucursales.nombre","=","in_balances.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
            $sqlCheck2=$sqlCheck2->where('sucursales.id',$sucursal);
            $sqlCheck3=$sqlCheck3->where('sucursales.id',$sucursal);
        }
        if($year>0 && $month>0){
            $sqlCheck= $sqlCheck->where('in_balances.mes',$month);
            $sqlCheck2= $sqlCheck2->where('in_balances.mes',$month);
            $sqlCheck3= $sqlCheck3->where('in_balances.mes',$monthCierre);
            $sqlCheck= $sqlCheck->where('in_balances.ano',$year);
            $sqlCheck2=$sqlCheck2->where('in_balances.ano',($year-1));
            $sqlCheck3=$sqlCheck3->where('in_balances.ano',($yearCierre));
        }else{
            return $this->sendResponse([], 'In Balances retrieved successfully');  
        }
        
        $data=['actual'=>$sqlCheck->get(),'anterior'=>$sqlCheck2->get(),'cierre'=>$sqlCheck3->get()];
        
        return $this->sendResponse($data, 'In Balances retrieved successfully');  
    }
    public function showDataEvolutivo($year, $month,$sucursal): JsonResponse
    {
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_balance::SELECT(DB::raw('CAST(in_balances.saldo_uyu AS DECIMAL(18,3)) as amount_uyu,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_balances.saldo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        categorizacion_cts_balances.nivel_3,
        categorizacion_cts_balances.nombre,
              categorizacion_cts_balances.nivel_4,
        categorizacion_cts_balances.nivel_2,
        categorizacion_cts_balances.nivel_1,
        in_balances.ano,
        in_balances.mes,
        categorizacion_cts_balances.posicion_moneda,
        categorizacion_cts_balances.posicion_fiscal,
        categorizacion_cts_balances.posicion_socios'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_balances.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($join)
            {
                $join->on('tipo_cambios.mes','=','in_balances.mes')
                ->on('tipo_cambios.ano','=','in_balances.ano')
                ->on('tipo_cambios.id_excel','=','in_balances.id_excel');
            })
            ->join('categorizacion_cts_balances',function($join){
                $join->on("categorizacion_cts_balances.id_excel","=","in_balances.id_excel")
                    ->on("categorizacion_cts_balances.cuenta","=","in_balances.cuenta_master");
            });
      
            $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
        if($user->id_company_show>0){
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }

        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_balances.ano,"-",in_balances.mes,"-","1")) ASC');
        if($sucursal>0){
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_balances.id_excel")
                    ->on("sucursales.nombre","=","in_balances.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if ($year > 0 && $month > 0) {
            $dateStart = $year . '-' . $month . '-01';
            $effectiveDate = strtotime("-11 months", strtotime($dateStart)); // returns timestamp
            $date = date('Y-m-d', $effectiveDate);
            $dataSql = [$date, $dateStart];
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_balances.ano,"-",in_balances.mes,"-","1")) >= ? AND DATE(CONCAT (in_balances.ano,"-",in_balances.mes,"-","1"))<=?', $dataSql);
        } else {
            return $this->sendResponse([], 'In Balances retrieved successfully');
        }
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Balances retrieved successfully');
    }
    /**
     * Store a newly created in_balance in storage.
     * POST /in_balances
     */
    public function store(Createin_balanceAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $inBalance = $this->inBalanceRepository->create($input);

        return $this->sendResponse($inBalance->toArray(), 'In Balance saved successfully');
    }

    /**
     * Display the specified in_balance.
     * GET|HEAD /in_balances/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var in_balance $inBalance */
        $inBalance = $this->inBalanceRepository->find($id);

        if (empty($inBalance)) {
            return $this->sendError('In Balance not found');
        }

        return $this->sendResponse($inBalance->toArray(), 'In Balance retrieved successfully');
    }

    /**
     * Update the specified in_balance in storage.
     * PUT/PATCH /in_balances/{id}
     */
    public function update($id, Updatein_balanceAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var in_balance $inBalance */
        $inBalance = $this->inBalanceRepository->find($id);

        if (empty($inBalance)) {
            return $this->sendError('In Balance not found');
        }

        $inBalance = $this->inBalanceRepository->update($input, $id);

        return $this->sendResponse($inBalance->toArray(), 'in_balance updated successfully');
    }

    /**
     * Remove the specified in_balance from storage.
     * DELETE /in_balances/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var in_balance $inBalance */
        $inBalance = $this->inBalanceRepository->find($id);

        if (empty($inBalance)) {
            return $this->sendError('In Balance not found');
        }

        $inBalance->delete();

        return $this->sendSuccess('In Balance deleted successfully');
    }
}
