<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createin_resultadoAPIRequest;
use App\Http\Requests\API\Updatein_resultadoAPIRequest;
use App\Models\in_resultado;
use App\Models\in_presupuestos;
use App\Repositories\in_resultadoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppBaseController;

/**
 * Class in_resultadoAPIController
 */
class in_resultadoAPIController extends AppBaseController
{
    private in_resultadoRepository $inResultadoRepository;

    public function __construct(in_resultadoRepository $inResultadoRepo)
    {
        $this->inResultadoRepository = $inResultadoRepo;
    }

    /**
     * Display a listing of the in_resultados.
     * GET|HEAD /in_resultados
     */
    public function index(Request $request): JsonResponse
    {
        $inResultados = $this->inResultadoRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($inResultados->toArray(), 'In Resultados retrieved successfully');
    }

    /**
     * Store a newly created in_resultado in storage.
     * POST /in_resultados
     */
    public function store(Createin_resultadoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $inResultado = $this->inResultadoRepository->create($input);

        return $this->sendResponse($inResultado->toArray(), 'In Resultado saved successfully');
    }
    public function showDataGroup($year, $month ,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.ipc AS DECIMAL(18,5))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.clasificacion_er,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftjoin('tipo_cambios', function($joins)
                         {
                             $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                             ->on('tipo_cambios.ano','=','in_resultados.ano')
                             ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if ($year > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.ano', $year);
        }
        if ($month > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.mes', $month);
        }

        $sqlCheck = $sqlCheck->groupByRaw('clasificacion_er,ipc,clasificacion_punto_equilibrio,in_resultados.mes,in_resultados.ano,cuenta,grupo,origen');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showDataGroupEbitda($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.cuenta,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        in_resultados.ano,in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if ($year > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.ano', $year);
        }
        if ($month > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.mes', $month);
        }

        $sqlCheck = $sqlCheck->groupByRaw('ipc,clasificacion_ebit_ebitda, mes,ano');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }

    public function showDataGroupEstadosResult($year, $month): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);

        $resultados = DB::table('gammadata.clasificacion_cuenta_resuls as ccr')
            ->join('gammadata.in_resultados as ir', 'ir.cuenta_master', '=', 'ccr.cuenta')
            ->join('excelscompanies', 'excelscompanies.id', 'ir.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->select(
                'ccr.grupo',
                'ccr.nivel_1',
                'ccr.origen',
                'ccr.nombre',
                'ir.monto_uyu',
                'ir.monto_uyu_sinajuste_corriente',
                'ir.monto_enuyu_sinajuste_corriente',
                'ir.ano',
                'ir.mes'
            )
            ->where('ir.ano', $year);

        if ($user->is_user) {
            $resultados = $resultados->join('client_users', 'client_users.id_user_client', 'companies.id_user');
            $resultados = $resultados->where('client_users.id_user', $user->id);
        } else {
            $resultados = $resultados->where('companies.id_user', $user->id);
        }

        if ($month > 0) {
            $resultados = $resultados->where('ir.mes', $month);
        }

        $resultados = $resultados->orderBy('ccr.grupo')
            ->get();

        $objetoResultados = self::procesarDatos($resultados);

        $resultadosLast = DB::table('gammadata.clasificacion_cuenta_resuls as ccr')
            ->join('gammadata.in_resultados as ir', 'ir.cuenta_master', '=', 'ccr.cuenta')
            ->join('excelscompanies', 'excelscompanies.id', 'ir.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->select(
                'ccr.grupo',
                'ccr.nivel_1',
                'ccr.origen',
                'ccr.nombre',
                'ir.monto_uyu',
                'ir.monto_uyu_sinajuste_corriente',
                'ir.monto_enuyu_sinajuste_corriente',
                'ir.ano',
                'ir.mes'
            )
            ->where('ir.ano', $year - 1);

        if ($user->is_user) {
            $resultadosLast = $resultadosLast->join('client_users', 'client_users.id_user_client', 'companies.id_user');
            $resultadosLast = $resultadosLast->where('client_users.id_user', $user->id);
        } else {
            $resultadosLast = $resultadosLast->where('companies.id_user', $user->id);
        }

        if ($month > 0) {
            $resultadosLast = $resultadosLast->where('ir.mes', $month);
        }

        $resultadosLast = $resultadosLast->orderBy('ccr.grupo')
            ->get();

        $objetoResultadosLast = self::procesarDatos($resultadosLast);



        $resultadosPresupuestos = DB::table('gammadata.clasificacion_cuenta_resuls as ccr')
            ->join('gammadata.in_presupuestos as pr', 'pr.cuenta_master', '=', 'ccr.cuenta')
            ->join('excelscompanies', 'excelscompanies.id', 'pr.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->select(
                'ccr.grupo',
                'ccr.nivel_1',
                'ccr.origen',
                'ccr.nombre',
                'pr.monto_uyu',
                'pr.monto_uyu_sinajuste_corriente',
                'pr.monto_enuyu_sinajuste_corriente',
                'pr.ano',
                'pr.mes'
            )
            ->where('pr.ano', $year);

        if ($user->is_user) {
            $resultadosPresupuestos = $resultadosPresupuestos->join('client_users', 'client_users.id_user_client', 'companies.id_user');
            $resultadosPresupuestos = $resultadosPresupuestos->where('client_users.id_user', $user->id);
        } else {
            $resultadosPresupuestos = $resultadosPresupuestos->where('companies.id_user', $user->id);
        }

        if ($month > 0) {
            $resultadosPresupuestos = $resultadosPresupuestos->where('pr.mes', $month);
        }

        $resultadosPresupuestos = $resultadosPresupuestos->orderBy('ccr.grupo')
            ->get();

        $objetoResultadosPresupuesto = self::procesarDatos($resultadosPresupuestos);


        $resultadosAcumuladoActual = DB::table('gammadata.clasificacion_cuenta_resuls as ccr')
            ->join('gammadata.in_resultados as ir', 'ir.cuenta_master', '=', 'ccr.cuenta')
            ->join('excelscompanies', 'excelscompanies.id', 'ir.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->select(
                'ccr.grupo',
                'ccr.nivel_1',
                'ccr.origen',
                'ccr.nombre',
                'ir.monto_uyu',
                'ir.monto_uyu_sinajuste_corriente',
                'ir.monto_enuyu_sinajuste_corriente',
                'ir.ano',
                'ir.mes'
            )
            ->where('ir.ano', $year)
            ->orderBy('ccr.grupo');
        if ($user->is_user) {
            $resultadosAcumuladoActual = $resultadosAcumuladoActual->join('client_users', 'client_users.id_user_client', 'companies.id_user');
            $resultadosAcumuladoActual = $resultadosAcumuladoActual->where('client_users.id_user', $user->id);
        } else {
            $resultadosAcumuladoActual = $resultadosAcumuladoActual->where('companies.id_user', $user->id);
        }
        $resultadosAcumuladoActual=$resultadosAcumuladoActual ->get();
       
        $resultadosAcumuladoAnterior = DB::table('gammadata.clasificacion_cuenta_resuls as ccr')
            ->join('gammadata.in_resultados as ir', 'ir.cuenta_master', '=', 'ccr.cuenta')
            ->join('excelscompanies', 'excelscompanies.id', 'ir.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->select(
                'ccr.grupo',
                'ccr.nivel_1',
                'ccr.origen',
                'ccr.nombre',
                'ir.monto_uyu',
                'ir.monto_uyu_sinajuste_corriente',
                'ir.monto_enuyu_sinajuste_corriente',
                'ir.ano',
                'ir.mes'
            )
            ->where('ir.ano', $year - 1)
            ->orderBy('ccr.grupo');

        if ($user->is_user) {
            $resultadosAcumuladoAnterior = $resultadosAcumuladoAnterior->join('client_users', 'client_users.id_user_client', 'companies.id_user');
            $resultadosAcumuladoAnterior = $resultadosAcumuladoAnterior->where('client_users.id_user', $user->id);
        } else {
            $resultadosAcumuladoAnterior = $resultadosAcumuladoAnterior->where('companies.id_user', $user->id);
        }
        $resultadosAcumuladoAnterior=$resultadosAcumuladoAnterior  ->get();
      
        $resultadosAcumuladoPresupuestos = DB::table('gammadata.clasificacion_cuenta_resuls as ccr')
            ->join('gammadata.in_presupuestos as pr', 'pr.cuenta_master', '=', 'ccr.cuenta')
            ->join('excelscompanies', 'excelscompanies.id', 'pr.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->select(
                'ccr.grupo',
                'ccr.nivel_1',
                'ccr.origen',
                'ccr.nombre',
                'pr.monto_uyu',
                'pr.monto_uyu_sinajuste_corriente',
                'pr.monto_enuyu_sinajuste_corriente',
                'pr.ano',
                'pr.mes'
            )
            ->where('pr.ano', $year)
            ->orderBy('ccr.grupo');

        if ($user->is_user) {
            $resultadosAcumuladoPresupuestos = $resultadosAcumuladoPresupuestos->join('client_users', 'client_users.id_user_client', 'companies.id_user');
            $resultadosAcumuladoPresupuestos = $resultadosAcumuladoPresupuestos->where('client_users.id_user', $user->id);
        } else {
            $resultadosAcumuladoPresupuestos = $resultadosAcumuladoPresupuestos->where('companies.id_user', $user->id);
        }

        $resultadosAcumuladoPresupuestos= $resultadosAcumuladoPresupuestos->get();
        $objetoAcumuladoActual = self::procesarDatos($resultadosAcumuladoActual);
        $objetoAcumuladoAnterior = self::procesarDatos($resultadosAcumuladoAnterior);
        $objetoAcumuladoPresupuestos = self::procesarDatos($resultadosAcumuladoPresupuestos);


        $response = [
            'anioactual' => $objetoResultados,
            'anioanterior' => $objetoResultadosLast,
            'presupuestos' => $objetoResultadosPresupuesto,
            'acumuladoanioactual' => $objetoAcumuladoActual,
            'acumuladoanioanterior' => $objetoAcumuladoAnterior,
            'acumuladopresupuestos' => $objetoAcumuladoPresupuestos
        ];

        return $this->sendResponse($response, 'In Resultadoss retrieved successfully');

    }

    static function procesarDatos($collection)
    {
        return $collection->groupBy('grupo')->map(function ($grupo) {
            $nombreGrupo = $grupo->first()->grupo;

            $datosNivel1 = $grupo->groupBy('nivel_1')->map(function ($nivel) {
                $nombreNivel = $nivel->first()->nivel_1;

                $datosNombre = $nivel->groupBy('nombre')->map(function ($nombre) {
                    $sumaDatos = $nombre->reduce(function ($carry, $item) {
                        // Sumar los valores específicos
                        $carry['monto_uyu'] += intval($item->monto_uyu) ?? 0;
                        $carry['monto_uyu_sinajuste_corriente'] += intval($item->monto_uyu_sinajuste_corriente) ?? 0;
                        $carry['monto_enuyu_sinajuste_corriente'] += intval($item->monto_enuyu_sinajuste_corriente) ?? 0;
                        return $carry;
                    }, ['monto_uyu' => 0, 'monto_uyu_sinajuste_corriente' => 0, 'monto_enuyu_sinajuste_corriente' => 0]);

                    return [
                        'nombre' => $nombre->first()->nombre,
                        'datos' => $sumaDatos
                    ];
                })->values();

                return [
                    'nombre' => $nombreNivel,
                    'origen' => $nivel->first()->origen,
                    'datos' => $datosNombre
                ];
            })->values();
            return [
                'nombre' => $nombreGrupo,
                'datos' => $datosNivel1
            ];
        })->values();
    }
    public function showEstadisticas($sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        clasificacion_cuenta_resuls.clasificacion_er,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck = $sqlCheck->groupByRaw('clasificacion_er,in_resultados.ano,in_resultados.mes');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showDataGroupEbitda7month($sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) as date'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) ASC');
        $sqlCheck = $sqlCheck->groupByRaw('ipc,clasificacion_ebit_ebitda,DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1"))');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showDataFullYear($year,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                             ->on('tipo_cambios.ano','=','in_resultados.ano')
                             ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        
        $sqlCheck = $sqlCheck->groupByRaw('
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        in_resultados.ano,
        tipo_cambios.ipc,
        in_resultados.mes');
        if ($year > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.ano','<=', $year+1);
            $sqlCheck = $sqlCheck->where('in_resultados.ano','>=', $year-12);
        }

        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showDataFull($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.cuenta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        clasificacion_cuenta_resuls.grupo,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                             ->on('tipo_cambios.ano','=','in_resultados.ano')
                             ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if ($year > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.ano', $year);
        }
        if ($month > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.mes', $month);
        }

        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showInformERFiscal($year,$month,$yearfiscal,$monthfiscal,$sucursal):JsonResponse{
        $dataSqls='in_resultados.monto_uyu as amount_uyu,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        in_resultados.ano,
        in_resultados.mes';
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw($dataSqls))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
            });
            $sqlCheck2 = in_resultado::SELECT(DB::raw($dataSqls))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                    $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                        ->on("sucursales.nombre","=","in_resultados.sucursal");
                });
                $sqlCheck2=$sqlCheck2->join('sucursales',function($join){
                    $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                        ->on("sucursales.nombre","=","in_resultados.sucursal");
                });
                $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
                $sqlCheck2=$sqlCheck2->where('sucursales.id',$sucursal);
            }
            $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) ASC');
            $sqlCheck2 = $sqlCheck2->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) ASC');
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
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) <= ? AND DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1"))>=?', $dataSql);
            $sqlCheck2 = $sqlCheck2->whereRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) <= ? AND DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1"))>=?', $dataSql2);
            $data = $sqlCheck->get();
            $data2 = $sqlCheck2->get();
            return $this->sendResponse(['actual'=>$data,'anterior'=>$data2], 'In Resultadoss retrieved successfully');
        }
    public function showInformER($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('in_resultados.monto_uyu as amount_uyu,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if ($year > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.ano', $year);
        }
        if ($month > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.mes', $month);
        }

        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showDataLegal($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,5)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,5))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,5))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,5))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,5))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.ui AS DECIMAL(18,5))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.ipc AS DECIMAL(18,5))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,5))) as amount_uyu_ipc_empresa,
        clasificacion_cuenta_resuls.clasificacion_cuenta_juridica_legal,
        clasificacion_cuenta_resuls.origen,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) ASC');
        if ($year > 0 && $month > 0) {
            $dateStart = $year . '-' . $month . '-01';
            $effectiveDate = strtotime("+11 months", strtotime($dateStart)); // returns timestamp
            $date = date('Y-m-d', $effectiveDate);
            $dataSql = [$date, $dateStart];
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) <= ? AND DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1"))>=?', $dataSql);
        } else {
            return $this->sendResponse([], 'In Resultadoss retrieved successfully');
        } 
       
        $sqlCheck = $sqlCheck->groupByRaw('clasificacion_cuenta_juridica_legal,in_resultados.mes,in_resultados.ano,origen');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showDataErEvolutivo($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('CAST(in_resultados.monto_uyu AS DECIMAL(18,3)) as amount_uyu,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.clasificacion_ratios_financ,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        clasificacion_cuenta_resuls.nivel_1,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) ASC');
        if ($year > 0 && $month > 0) {
            $dateStart = $year . '-' . $month . '-01';
            $effectiveDate = strtotime("-11 months", strtotime($dateStart)); // returns timestamp
            $date = date('Y-m-d', $effectiveDate);
            $dataSql = [$date, $dateStart];
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) >= ? AND DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1"))<=?', $dataSql);
        } else {
            return $this->sendResponse([], 'In Resultadoss retrieved successfully');
        }
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function showDataPuntoEquilibrioEvolutivo($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('CAST(in_resultados.monto_uyu AS DECIMAL(18,3)) as amount_uyu,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
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
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) ASC');
        if ($year > 0 && $month > 0) {
            $dateStart = $year . '-' . $month . '-01';
            $effectiveDate = strtotime("-11 months", strtotime($dateStart)); // returns timestamp
            $date = date('Y-m-d', $effectiveDate);
            $dataSql = [$date, $dateStart];
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) >= ? AND DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1"))<=?', $dataSql);
        } else {
            return $this->sendResponse([], 'In Resultadoss retrieved successfully');
        }
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }

    /**
     * Display the specified in_resultado.
     * GET|HEAD /in_resultados/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var in_resultado $inResultado */
        $inResultado = $this->inResultadoRepository->find($id);

        if (empty($inResultado)) {
            return $this->sendError('In Resultado not found');
        }

        return $this->sendResponse($inResultado->toArray(), 'In Resultado retrieved successfully');
    }

    /**
     * Update the specified in_resultado in storage.
     * PUT/PATCH /in_resultados/{id}
     */
    public function update($id, Updatein_resultadoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var in_resultado $inResultado */
        $inResultado = $this->inResultadoRepository->find($id);

        if (empty($inResultado)) {
            return $this->sendError('In Resultado not found');
        }

        $inResultado = $this->inResultadoRepository->update($input, $id);

        return $this->sendResponse($inResultado->toArray(), 'in_resultado updated successfully');
    }

    /**
     * Remove the specified in_resultado from storage.
     * DELETE /in_resultados/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var in_resultado $inResultado */
        $inResultado = $this->inResultadoRepository->find($id);

        if (empty($inResultado)) {
            return $this->sendError('In Resultado not found');
        }

        $inResultado->delete();

        return $this->sendSuccess('In Resultado deleted successfully');
    }
}
