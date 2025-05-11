<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createin_presupuestosAPIRequest;
use App\Http\Requests\API\Updatein_presupuestosAPIRequest;
use App\Models\in_presupuestos;
use App\Repositories\in_presupuestosRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppBaseController;

/**
 * Class in_presupuestosAPIController
 */
class in_presupuestosAPIController extends AppBaseController
{
    private in_presupuestosRepository $inPresupuestosRepository;

    public function __construct(in_presupuestosRepository $inPresupuestosRepo)
    {
        $this->inPresupuestosRepository = $inPresupuestosRepo;
    }

    /**
     * Display a listing of the in_presupuestos.
     * GET|HEAD /in_presupuestos
     */
    public function index(Request $request): JsonResponse
    {
        $inPresupuestos = $this->inPresupuestosRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($inPresupuestos->toArray(), 'In Presupuestos retrieved successfully');
    }
    public function showEstadisticas($sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_presupuestos::SELECT(DB::raw('SUM(in_presupuestos.monto_uyu) as amount_uyu,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        clasificacion_cuenta_resuls.clasificacion_er,
        in_presupuestos.ano,
        in_presupuestos.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_presupuestos.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_presupuestos.mes')
                ->on('tipo_cambios.ano','=','in_presupuestos.ano')
                ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                    ->on("sucursales.nombre","=","in_presupuestos.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck = $sqlCheck->groupByRaw('clasificacion_er,in_presupuestos.ano,in_presupuestos.mes');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Presupuestos retrieved successfully');
    }
    public function showDataLegal($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_presupuestos::SELECT(DB::raw('SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))) as amount_uyu,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,5)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,5))) as amount_uyu_dolar_venta,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,5))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,5))) as amount_uyu_euro_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,5))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.ui AS DECIMAL(18,5))) as amount_uyu_ui,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.ipc AS DECIMAL(18,5))) as amount_uyu_ipc,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,5))) as amount_uyu_ipc_empresa,
        clasificacion_cuenta_resuls.clasificacion_cuenta_juridica_legal,
        clasificacion_cuenta_resuls.origen,
        in_presupuestos.ano,
        in_presupuestos.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_presupuestos.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_presupuestos.mes')
                ->on('tipo_cambios.ano','=','in_presupuestos.ano')
                ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                    ->on("sucursales.nombre","=","in_presupuestos.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")) ASC');
        if ($year > 0 && $month > 0) {
            $dateStart = $year . '-' . $month . '-01';
            $effectiveDate = strtotime("+11 months", strtotime($dateStart)); // returns timestamp
            $date = date('Y-m-d', $effectiveDate);
            $dataSql = [$date, $dateStart];
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")) <= ? AND DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1"))>=?', $dataSql);
        } else {
            return $this->sendResponse([], 'In Presupuestos retrieved successfully');
        } 
       
        $sqlCheck = $sqlCheck->groupByRaw('clasificacion_cuenta_juridica_legal,in_presupuestos.mes,in_presupuestos.ano,origen');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Presupuestos retrieved successfully');
    }
    public function showDataGroup($year,$month,$sucursal):JsonResponse
    {
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck=in_presupuestos::SELECT(DB::raw('SUM(in_presupuestos.monto_uyu) as amount_uyu,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.grupo,
        in_presupuestos.ano,
        in_presupuestos.mes'))
        ->join('excelscompanies','excelscompanies.id','in_presupuestos.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
        {
            $join->on('tipo_cambios.mes','=','in_presupuestos.mes')
            ->on('tipo_cambios.ano','=','in_presupuestos.ano')
            ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
        })
        ->join('clasificacion_cuenta_resuls',function($join){
            $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                    ->on("sucursales.nombre","=","in_presupuestos.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if($year>0){
            $sqlCheck= $sqlCheck->where('in_presupuestos.ano',$year);
        }
        if($month>0){
            $sqlCheck= $sqlCheck->where('in_presupuestos.mes',$month);
        }
        
        $sqlCheck= $sqlCheck->groupByRaw('clasificacion_er,ipc,clasificacion_punto_equilibrio, mes,ano,grupo');
        $data=$sqlCheck->get();
        
        return $this->sendResponse($data, 'In Presupuesto retrieved successfully');  
    }
    public function showDataFull($year,$month,$sucursal):JsonResponse
    {
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck=in_presupuestos::SELECT(DB::raw('in_presupuestos.monto_uyu as amount_uyu,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        in_presupuestos.ano,
        in_presupuestos.mes'))
        ->join('excelscompanies','excelscompanies.id','in_presupuestos.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
        {
            $join->on('tipo_cambios.mes','=','in_presupuestos.mes')
            ->on('tipo_cambios.ano','=','in_presupuestos.ano')
            ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
        })
        ->join('clasificacion_cuenta_resuls',function($join){
            $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                    ->on("sucursales.nombre","=","in_presupuestos.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if($year>0){
            $sqlCheck= $sqlCheck->where('in_presupuestos.ano',$year);
        }
        if($month>0){
            $sqlCheck= $sqlCheck->where('in_presupuestos.mes',$month);
        }
        
        $data=$sqlCheck->get();
        
        return $this->sendResponse($data, 'In Presupuesto retrieved successfully');  
    }
    public function showInformERFiscal($year,$month,$yearfiscal,$monthfiscal,$sucursal):JsonResponse{
        $dataSqls='in_presupuestos.monto_uyu as amount_uyu,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        clasificacion_cuenta_resuls.clasificacion_er,
        in_presupuestos.ano,
        in_presupuestos.mes';
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_presupuestos::SELECT(DB::raw($dataSqls))
            ->join('excelscompanies', 'excelscompanies.id', 'in_presupuestos.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_presupuestos.mes')
                ->on('tipo_cambios.ano','=','in_presupuestos.ano')
                ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
            });
            $sqlCheck2 = in_presupuestos::SELECT(DB::raw($dataSqls))
            ->join('excelscompanies', 'excelscompanies.id', 'in_presupuestos.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_presupuestos.mes')
                ->on('tipo_cambios.ano','=','in_presupuestos.ano')
                ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
            });
            $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
            $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
            $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
            if($user->id_company_show>0){
                $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
            }

            $sqlCheck2= $sqlCheck2->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
            $sqlCheck2= $sqlCheck2->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
            $sqlCheck2= $sqlCheck2->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
            $sqlCheck2= $sqlCheck2->where('usuario_grupoeconomicos.id_users',$user->id);
            if($user->id_company_show>0){
                $sqlCheck2= $sqlCheck2->where('grupo_economicos_empresas.id_company',$user->id_company_show);
            }


            if($sucursal>0){
                $sqlCheck=$sqlCheck->join('sucursales',function($join){
                    $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                        ->on("sucursales.nombre","=","in_presupuestos.sucursal");
                });
                $sqlCheck2=$sqlCheck2->join('sucursales',function($join){
                    $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                        ->on("sucursales.nombre","=","in_presupuestos.sucursal");
                });
                $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
                $sqlCheck2=$sqlCheck2->where('sucursales.id',$sucursal);
            }
            $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")),clasificacion_cuenta_resuls.id ASC');
            $sqlCheck2 = $sqlCheck2->orderByRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")),clasificacion_cuenta_resuls.id ASC');
            $yearbusqueda="";
            $yearbusqueda2="";
            $monthsbusqueda=$monthfiscal;
            if($month>=$monthfiscal){
                $yearbusqueda=$year;
                $yearbusqueda2=$year-1;
            }else{
                $yearbusqueda=$year-1;
                $yearbusqueda2=$year-2;
            }
            $dateStart = $yearbusqueda . '-' . $monthsbusqueda . '-01';
            $dateStart2 = $yearbusqueda2 . '-' . $monthsbusqueda . '-01';
            $effectiveDate = strtotime("+11 months", strtotime($dateStart)); 
            $effectiveDate2 = strtotime("+11 months", strtotime($dateStart2)); 
            $date = date('Y-m-d', $effectiveDate);
            $date2 = date('Y-m-d', $effectiveDate2);
            $dataSql = [$date, $dateStart];
            $dataSql2 = [$date2, $dateStart2];
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")) <= ? AND DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1"))>=?', $dataSql);
            $sqlCheck2 = $sqlCheck2->whereRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")) <= ? AND DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1"))>=?', $dataSql2);
            $data = $sqlCheck->get();
            $data2 = $sqlCheck2->get();
            return $this->sendResponse(['actual'=>$data,'anterior'=>$data2], 'In Resultadoss retrieved successfully');
        }
    public function showInformER($year,$month,$sucursal):JsonResponse
    {
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck=in_presupuestos::SELECT(DB::raw('in_presupuestos.monto_uyu as amount_uyu,
        clasificacion_cuenta_resuls.nombre,
        (in_presupuestos.monto_uyu/tipo_cambios.dolar_compra) as amount_uyu_dolar_compra,
        (in_presupuestos.monto_uyu/tipo_cambios.dolar_venta) as amount_uyu_dolar_venta,
        (in_presupuestos.monto_uyu/tipo_cambios.dolar_promedio) as amount_uyu_dolar_promedio,
        (in_presupuestos.monto_uyu/tipo_cambios.euro_promedio) as amount_uyu_euro_promedio,
        (in_presupuestos.monto_uyu/tipo_cambios.francosuizo_promedio) as amount_uyu_francosuizo_promedio,
        (in_presupuestos.monto_uyu/tipo_cambios.ui) as amount_uyu_ui,
        (in_presupuestos.monto_uyu/tipo_cambios.ipc) as amount_uyu_ipc,
        (in_presupuestos.monto_uyu/tipo_cambios.ipc_empresa) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        in_presupuestos.ano,
        in_presupuestos.mes'))
        ->join('excelscompanies','excelscompanies.id','in_presupuestos.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
            {
                $join->on('tipo_cambios.mes','=','in_presupuestos.mes')
                ->on('tipo_cambios.ano','=','in_presupuestos.ano')
                ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
            })
        ->join('clasificacion_cuenta_resuls',function($join){
            $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                    ->on("sucursales.nombre","=","in_presupuestos.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if($year>0){
            $sqlCheck= $sqlCheck->where('in_presupuestos.ano',$year);
        }
        if($month>0){
            $sqlCheck= $sqlCheck->where('in_presupuestos.mes',$month);
        }
        
        $data=$sqlCheck->get();
        
        return $this->sendResponse($data, 'In Presupuesto retrieved successfully');  
    }
    public function showDataErEvolutivo($year,$month,$sucursal):JsonResponse
    {
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck=in_presupuestos::SELECT(DB::raw('in_presupuestos.monto_uyu as amount_uyu,
        (in_presupuestos.monto_uyu/tipo_cambios.dolar_compra) as amount_uyu_dolar_compra,
        (in_presupuestos.monto_uyu/tipo_cambios.dolar_venta) as amount_uyu_dolar_venta,
        (in_presupuestos.monto_uyu/tipo_cambios.dolar_promedio) as amount_uyu_dolar_promedio,
        (in_presupuestos.monto_uyu/tipo_cambios.euro_promedio) as amount_uyu_euro_promedio,
        (in_presupuestos.monto_uyu/tipo_cambios.francosuizo_promedio) as amount_uyu_francosuizo_promedio,
        (in_presupuestos.monto_uyu/tipo_cambios.ui) as amount_uyu_ui,
        (in_presupuestos.monto_uyu/tipo_cambios.ipc) as amount_uyu_ipc,
        (in_presupuestos.monto_uyu/tipo_cambios.ipc_empresa) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc, 
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.clasificacion_ratios_financ,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        in_presupuestos.ano,
        in_presupuestos.mes'))
        ->join('excelscompanies','excelscompanies.id','in_presupuestos.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($join)
        {
            $join->on('tipo_cambios.mes','=','in_presupuestos.mes')
            ->on('tipo_cambios.ano','=','in_presupuestos.ano')
            ->on('tipo_cambios.id_excel','=','in_presupuestos.id_excel');
        })
        ->join('clasificacion_cuenta_resuls',function($join){
            $join->on("clasificacion_cuenta_resuls.id_excel","=","in_presupuestos.id_excel")
                ->on("clasificacion_cuenta_resuls.cuenta","=","in_presupuestos.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_presupuestos.id_excel")
                    ->on("sucursales.nombre","=","in_presupuestos.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck= $sqlCheck->orderByRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")),clasificacion_cuenta_resuls.id ASC');
        if($year>0 && $month>0){
            $dateStart=$year.'-'.$month.'-01';
            $effectiveDate = strtotime("-11 months", strtotime($dateStart)); // returns timestamp
            $date = date('Y-m-d',$effectiveDate);
            $dataSql=[ $date,$dateStart];
            $sqlCheck=$sqlCheck->whereRaw('DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1")) >= ? AND DATE(CONCAT (in_presupuestos.ano,"-",in_presupuestos.mes,"-","1"))<=?',$dataSql);
        }else{
            return $this->sendResponse([], 'In Presupuesto retrieved successfully');  
        }
        $data=$sqlCheck->get();
        
        return $this->sendResponse($data, 'In Presupuesto retrieved successfully');  
    }
    /**
     * Store a newly created in_presupuestos in storage.
     * POST /in_presupuestos
     */
    public function store(Createin_presupuestosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $inPresupuestos = $this->inPresupuestosRepository->create($input);

        return $this->sendResponse($inPresupuestos->toArray(), 'In Presupuestos saved successfully');
    }

    /**
     * Display the specified in_presupuestos.
     * GET|HEAD /in_presupuestos/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var in_presupuestos $inPresupuestos */
        $inPresupuestos = $this->inPresupuestosRepository->find($id);

        if (empty($inPresupuestos)) {
            return $this->sendError('In Presupuestos not found');
        }

        return $this->sendResponse($inPresupuestos->toArray(), 'In Presupuestos retrieved successfully');
    }

    /**
     * Update the specified in_presupuestos in storage.
     * PUT/PATCH /in_presupuestos/{id}
     */
    public function update($id, Updatein_presupuestosAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var in_presupuestos $inPresupuestos */
        $inPresupuestos = $this->inPresupuestosRepository->find($id);

        if (empty($inPresupuestos)) {
            return $this->sendError('In Presupuestos not found');
        }

        $inPresupuestos = $this->inPresupuestosRepository->update($input, $id);

        return $this->sendResponse($inPresupuestos->toArray(), 'in_presupuestos updated successfully');
    }

    /**
     * Remove the specified in_presupuestos from storage.
     * DELETE /in_presupuestos/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var in_presupuestos $inPresupuestos */
        $inPresupuestos = $this->inPresupuestosRepository->find($id);

        if (empty($inPresupuestos)) {
            return $this->sendError('In Presupuestos not found');
        }

        $inPresupuestos->delete();

        return $this->sendSuccess('In Presupuestos deleted successfully');
    }
}
