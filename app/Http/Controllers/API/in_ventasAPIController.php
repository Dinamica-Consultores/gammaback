<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createin_ventasAPIRequest;
use App\Http\Requests\API\Updatein_ventasAPIRequest;
use App\Models\in_ventas;
use App\Repositories\in_ventasRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppBaseController;

/**
 * Class in_ventasAPIController
 */
class in_ventasAPIController extends AppBaseController
{
    private in_ventasRepository $inVentasRepository;

    public function __construct(in_ventasRepository $inVentasRepo)
    {
        $this->inVentasRepository = $inVentasRepo;
    }

    /**
     * Display a listing of the in_ventas.
     * GET|HEAD /in_ventas
     */
    public function index(Request $request): JsonResponse
    {
        $inVentas = $this->inVentasRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($inVentas->toArray(), 'In Ventas retrieved successfully');
    }
    
    public function showDataGroup($year,$month,$sucursal):JsonResponse{

        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck=in_ventas::SELECT(DB::raw('SUM(in_ventas.ventas_uyu) as amount_ventas_total,
        SUM(in_ventas.costo_uyu) as costo_uyu_total,
        SUM(in_ventas.cantidad_costo) as cantidad_costo_total,
        SUM(in_ventas.cantidad_margen) as cantidad_margen_total,
        setup_analises.nombre,
        in_ventas.mes,
        in_ventas.ano,
        in_ventas.codigo_analisis'))
        ->join('excelscompanies','excelscompanies.id','in_ventas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($joins)
        {
            $joins->on('tipo_cambios.mes','=','in_ventas.mes')
            ->on('tipo_cambios.ano','=','in_ventas.ano')
            ->on('tipo_cambios.id_excel','=','in_ventas.id_excel');
        }) ->leftjoin('tipo_cambios_globals',function($join){
            $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
            ->on('tipo_cambios_globals.mes','=','in_ventas.mes')
            ->on('tipo_cambios_globals.ano','=','in_ventas.ano');
        })
        ->join('setup_analises','setup_analises.codigo','in_ventas.codigo_analisis');
        $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
        if($user->id_company_show>0){
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }
        if($sucursal>0){
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_ventas.id_excel")
                    ->on("sucursales.nombre","=","in_ventas.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if($year>0){
            $sqlCheck= $sqlCheck->where('in_ventas.ano',$year);
        }
        if($month>0){
            $sqlCheck= $sqlCheck->where('in_ventas.mes',$month);
        }
        
        $sqlCheck= $sqlCheck->groupByRaw('setup_analises.nombre, in_ventas.mes,in_ventas.ano,in_ventas.codigo_analisis');
        $data=$sqlCheck->get();
        
        return $this->sendResponse($data, 'In Ventas retrieved successfully');
    }
    public function showDataFull($year,$month,$sucursal):JsonResponse{

        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck=in_ventas::SELECT(DB::raw('in_ventas.ventas_uyu as amount_ventas,
        in_ventas.costo_uyu as costo_uyu,
        in_ventas.cantidad_costo as cantidad_costo,
        in_ventas.cantidad_margen as cantidad_margen,
        setup_analises.nombre,
        in_ventas.mes,
        in_ventas.ano,
        in_ventas.codigo_analisis'))
        ->join('excelscompanies','excelscompanies.id','in_ventas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($joins)
        {
            $joins->on('tipo_cambios.mes','=','in_ventas.mes')
            ->on('tipo_cambios.ano','=','in_ventas.ano')
            ->on('tipo_cambios.id_excel','=','in_ventas.id_excel');
        })->leftjoin('tipo_cambios_globals',function($join){
            $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
            ->on('tipo_cambios_globals.mes','=','in_ventas.mes')
            ->on('tipo_cambios_globals.ano','=','in_ventas.ano');
        })
        ->join('setup_analises','setup_analises.codigo','in_ventas.codigo_analisis');
        $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
        $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
        if($user->id_company_show>0){
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }
        if($sucursal>0){
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_ventas.id_excel")
                    ->on("sucursales.nombre","=","in_ventas.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
        }
        if($year>0){
            $sqlCheck= $sqlCheck->where('in_ventas.ano',$year);
        }
        $data=$sqlCheck->get();
        
        return $this->sendResponse($data, 'In Ventas retrieved successfully');
    }

    public function showDataAnalisisdeVentas($year,$month,$yearan,$monthan,$sucursal):JsonResponse{
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $textoBusqueda='in_ventas.ventas_uyu as amount_ventas,
        in_ventas.costo_uyu,
        in_ventas.ganancia_bruta_uyu,
        (CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_ventas_dolar_compra,
        (CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as costo_uyu_dolar_compra,
        (CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_dolar_compra,
       
        (CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) ) as amount_ventas_dolar_venta,
        (CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) ) as costo_uyu_dolar_venta,
        (CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_dolar_venta,

        (CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) ) as amount_ventas_dolar_promedio,
        (CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) ) as costo_uyu_dolar_promedio,
        (CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_dolar_promedio,

        (CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) ) as amount_ventas_euro_promedio,
        (CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) ) as costo_uyu_euro_promedio,
        (CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_euro_promedio,

        (CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) ) as amount_ventas_francosuizo_promedio,
        (CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) ) as costo_uyu_francosuizo_promedio,
        (CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_francosuizo_promedio,
        in_ventas.cantidad_venta_unidades,
        in_ventas.cantidad_costo,
        in_ventas.cantidad_margen,
        setup_analises.nombre,
        in_ventas.mes,
        in_ventas.ano,
        in_ventas.codigo_analisis';
        $sqlCheck=in_ventas::SELECT(DB::raw($textoBusqueda))
        ->join('excelscompanies','excelscompanies.id','in_ventas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($joins)
        {
            $joins->on('tipo_cambios.mes','=','in_ventas.mes')
            ->on('tipo_cambios.ano','=','in_ventas.ano')
            ->on('tipo_cambios.id_excel','=','in_ventas.id_excel');
        })->leftjoin('tipo_cambios_globals',function($join){
            $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
            ->on('tipo_cambios_globals.mes','=','in_ventas.mes')
            ->on('tipo_cambios_globals.ano','=','in_ventas.ano');
        })
        ->join('setup_analises',function($join){
            $join->on("setup_analises.id_excel","=","in_ventas.id_excel")
                ->on("setup_analises.codigo","=","in_ventas.codigo_analisis");
        });
        $sqlCheck2=in_ventas::SELECT(DB::raw($textoBusqueda))
        ->join('excelscompanies','excelscompanies.id','in_ventas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($joins)
        {
            $joins->on('tipo_cambios.mes','=','in_ventas.mes')
            ->on('tipo_cambios.ano','=','in_ventas.ano')
            ->on('tipo_cambios.id_excel','=','in_ventas.id_excel');
        })->leftjoin('tipo_cambios_globals',function($join){
            $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
            ->on('tipo_cambios_globals.mes','=','in_ventas.mes')
            ->on('tipo_cambios_globals.ano','=','in_ventas.ano');
        })
        ->join('setup_analises',function($join){
            $join->on("setup_analises.id_excel","=","in_ventas.id_excel")
                ->on("setup_analises.codigo","=","in_ventas.codigo_analisis");
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
        $sqlCheck = $sqlCheck->orderByRaw('setup_analises.nombre ASC');
        $sqlCheck2 = $sqlCheck2->orderByRaw('setup_analises.nombre ASC');
        if($sucursal>0){
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_ventas.id_excel")
                    ->on("sucursales.nombre","=","in_ventas.sucursal");
            });
            $sqlCheck2=$sqlCheck2->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_ventas.id_excel")
                    ->on("sucursales.nombre","=","in_ventas.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
            $sqlCheck2=$sqlCheck2->where('sucursales.id',$sucursal);
        }
        if($year>0 && $month>0 && $yearan>0 && $monthan){
            $sqlCheck = $sqlCheck->where('in_ventas.ano', $year);
            $sqlCheck2 = $sqlCheck2->where('in_ventas.ano', $yearan);
            $sqlCheck = $sqlCheck->where('in_ventas.mes', $month);
            $sqlCheck2 = $sqlCheck2->where('in_ventas.mes', $monthan);

            $data=$sqlCheck->get();
            $data2=$sqlCheck2->get();
            return $this->sendResponse(['mesanalisis'=>$data,'mescomparado'=>$data2], 'In Ventas retrieved successfully');
        }else{

            return $this->sendResponse([], 'In Ventas retrieved successfully');
        }
        
    }
    public function showDataAnalisisdeVentasDesdeHasta($desdeanalisis,$hastaanalisis,$desdecomparativo,$hastacomparativo,$sucursal):JsonResponse{
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $textoBusqueda='SUM(in_ventas.ventas_uyu) as amount_ventas,
        SUM(in_ventas.costo_uyu) as costo_uyu,
        SUM(in_ventas.ganancia_bruta_uyu) as ganancia_bruta_uyu,
        SUM(CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_ventas_dolar_compra,
        SUM(CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as costo_uyu_dolar_compra,
        SUM(CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_dolar_compra,
       
        SUM(CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) ) as amount_ventas_dolar_venta,
        SUM(CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) ) as costo_uyu_dolar_venta,
        SUM(CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_dolar_venta,

        SUM(CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) ) as amount_ventas_dolar_promedio,
        SUM(CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) ) as costo_uyu_dolar_promedio,
        SUM(CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_dolar_promedio,

        SUM(CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) ) as amount_ventas_euro_promedio,
        SUM(CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) ) as costo_uyu_euro_promedio,
        SUM(CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_euro_promedio,

        SUM(CAST(in_ventas.ventas_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) ) as amount_ventas_francosuizo_promedio,
        SUM(CAST(in_ventas.costo_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) ) as costo_uyu_francosuizo_promedio,
        SUM(CAST(in_ventas.ganancia_bruta_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) ) as ganancia_bruta_uyu_francosuizo_promedio,
        SUM(in_ventas.cantidad_venta_unidades) as cantidad_venta_unidades,
        SUM(in_ventas.cantidad_costo) as cantidad_costo,
        SUM(in_ventas.cantidad_margen) as cantidad_margen,
        setup_analises.nombre,
        in_ventas.codigo_analisis';
        $sqlCheck=in_ventas::SELECT(DB::raw($textoBusqueda))
        ->join('excelscompanies','excelscompanies.id','in_ventas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($joins)
        {
            $joins->on('tipo_cambios.mes','=','in_ventas.mes')
            ->on('tipo_cambios.ano','=','in_ventas.ano')
            ->on('tipo_cambios.id_excel','=','in_ventas.id_excel');
        })->leftjoin('tipo_cambios_globals',function($join){
            $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
            ->on('tipo_cambios_globals.mes','=','in_ventas.mes')
            ->on('tipo_cambios_globals.ano','=','in_ventas.ano');
        })
        ->join('setup_analises',function($join){
            $join->on("setup_analises.id_excel","=","in_ventas.id_excel")
                ->on("setup_analises.codigo","=","in_ventas.codigo_analisis");
        });
        $sqlCheck2=in_ventas::SELECT(DB::raw($textoBusqueda))
        ->join('excelscompanies','excelscompanies.id','in_ventas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->leftJoin('tipo_cambios', function($joins)
        {
            $joins->on('tipo_cambios.mes','=','in_ventas.mes')
            ->on('tipo_cambios.ano','=','in_ventas.ano')
            ->on('tipo_cambios.id_excel','=','in_ventas.id_excel');
        })->leftjoin('tipo_cambios_globals',function($join){
            $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
            ->on('tipo_cambios_globals.mes','=','in_ventas.mes')
            ->on('tipo_cambios_globals.ano','=','in_ventas.ano');
        })
        ->join('setup_analises',function($join){
            $join->on("setup_analises.id_excel","=","in_ventas.id_excel")
                ->on("setup_analises.codigo","=","in_ventas.codigo_analisis");
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
                $join->on("sucursales.id_excel","=","in_ventas.id_excel")
                    ->on("sucursales.nombre","=","in_ventas.sucursal");
            });
            $sqlCheck2=$sqlCheck2->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_ventas.id_excel")
                    ->on("sucursales.nombre","=","in_ventas.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.id',$sucursal);
            $sqlCheck2=$sqlCheck2->where('sucursales.id',$sucursal);
        }
        $sqlCheck= $sqlCheck->groupByRaw('setup_analises.nombre,  in_ventas.codigo_analisis');
        $sqlCheck2= $sqlCheck2->groupByRaw('setup_analises.nombre, in_ventas.codigo_analisis');
        $sqlCheck = $sqlCheck->orderByRaw('setup_analises.nombre ASC');
        $sqlCheck2 = $sqlCheck2->orderByRaw('setup_analises.nombre ASC');
            $dateDesdeAnalisis = $desdeanalisis . '-01';
            $dateHastaAnalisis = $hastaanalisis . '-01';
            $dateDesdeComparado = $desdecomparativo . '-01';
            $dateHastaComparado = $hastacomparativo . '-01';
            $edateDesdeAnalisis = strtotime($dateDesdeAnalisis); // returns timestamp
            $edateHastaAnalisis = strtotime($dateHastaAnalisis); // returns timestamp
            $edateDesdeComparado = strtotime($dateDesdeComparado); // returns timestamp
            $edateHastaComparado = strtotime($dateHastaComparado); // returns timestamp
            $date = date('Y-m-d', $edateDesdeAnalisis);
            $date2 = date('Y-m-d', $edateHastaAnalisis);
            $date3 = date('Y-m-d', $edateDesdeComparado);
            $date4 = date('Y-m-d', $edateHastaComparado);
            $dataSql = [$date, $date2];
            $sqlCheck = $sqlCheck->whereRaw('DATE(CONCAT (in_ventas.ano,"-",in_ventas.mes,"-","1")) >= ? AND DATE(CONCAT (in_ventas.ano,"-",in_ventas.mes,"-","1"))<=?', $dataSql);
            $dataSql2 = [$date3, $date4];
            $sqlCheck2 = $sqlCheck2->whereRaw('DATE(CONCAT (in_ventas.ano,"-",in_ventas.mes,"-","1")) >= ? AND DATE(CONCAT (in_ventas.ano,"-",in_ventas.mes,"-","1"))<=?', $dataSql2);

            $data=$sqlCheck->get();
            $data2=$sqlCheck2->get();
            return $this->sendResponse(['mesanalisis'=>$data,'mescomparado'=>$data2], 'In Ventas retrieved successfully');
     
        
    }
    /**
     * Store a newly created in_ventas in storage.
     * POST /in_ventas
     */
    public function store(Createin_ventasAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $inVentas = $this->inVentasRepository->create($input);

        return $this->sendResponse($inVentas->toArray(), 'In Ventas saved successfully');
    }

    /**
     * Display the specified in_ventas.
     * GET|HEAD /in_ventas/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var in_ventas $inVentas */
        $inVentas = $this->inVentasRepository->find($id);

        if (empty($inVentas)) {
            return $this->sendError('In Ventas not found');
        }

        return $this->sendResponse($inVentas->toArray(), 'In Ventas retrieved successfully');
    }

    /**
     * Update the specified in_ventas in storage.
     * PUT/PATCH /in_ventas/{id}
     */
    public function update($id, Updatein_ventasAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var in_ventas $inVentas */
        $inVentas = $this->inVentasRepository->find($id);

        if (empty($inVentas)) {
            return $this->sendError('In Ventas not found');
        }

        $inVentas = $this->inVentasRepository->update($input, $id);

        return $this->sendResponse($inVentas->toArray(), 'in_ventas updated successfully');
    }

    /**
     * Remove the specified in_ventas from storage.
     * DELETE /in_ventas/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var in_ventas $inVentas */
        $inVentas = $this->inVentasRepository->find($id);

        if (empty($inVentas)) {
            return $this->sendError('In Ventas not found');
        }

        $inVentas->delete();

        return $this->sendSuccess('In Ventas deleted successfully');
    }
}
