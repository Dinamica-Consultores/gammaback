<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createin_resultadoAPIRequest;
use App\Http\Requests\API\Updatein_resultadoAPIRequest;
use App\Models\in_resultado;
use App\Models\in_presupuestos;
use App\Models\sucursales;
use App\Repositories\in_resultadoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    public function espacioFiscal($year, $month, $sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if ($user->getContainestudios2($user->id)) {
            return $this->sendResponse([], 'No tienes Estudios');
        }

        // 1. Obtener la compañía para determinar su inicio de ejercicio fiscal
        $company = \App\Models\company::find($user->id_company_show);
        if (!$company) {
            $company = \App\Models\company::query()
                ->select('companies.*')
                ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'companies.id')
                ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
                ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
                ->first();
        }

        $monthfiscal = ($company && !empty($company->mes)) ? (int)$company->mes : 1;

        if ((int)$month >= $monthfiscal) {
            $yearStart = (int)$year;
        } else {
            $yearStart = (int)$year - 1;
        }

        $fechaInicio = $yearStart . '-' . str_pad($monthfiscal, 2, "0", STR_PAD_LEFT) . '-01';
        $fechaFin = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT) . '-01';

        $fechaFinFiscalDT = (new \DateTime($fechaInicio))->modify('+11 months');
        $fechaFinFiscal = $fechaFinFiscalDT->format('Y-m-01');

        $fechaInicioPresupuestoDT = (new \DateTime($fechaFin))->modify('+1 month');
        $fechaInicioPresupuesto = $fechaInicioPresupuestoDT->format('Y-m-01');

        if ($year <= 0 || $month <= 0) {
            return $this->sendResponse([], 'In Resultados retrieved successfully');
        }

        // -------------------------------------------------------------
        // Query 1 & 2: Espacio Fiscal (Desglosado por Cuentas)
        // -------------------------------------------------------------
        $selectResultados = "
            'in_resultados' as tipo,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))) as amount_uyu,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3))) as amount_uyu_dolar_compra,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
            (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
            clasificacion_cuenta_resuls.nivel_3,
            clasificacion_cuenta_resuls.grupo,
            clasificacion_cuenta_resuls.nombre,
            clasificacion_cuenta_resuls.cuenta,
            in_resultados.operador,
            in_resultados.signo,
            in_resultados.monto_valor,
            in_resultados.mes,
            in_resultados.ano
        ";

        $queryResultados = DB::table('in_resultados')
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function ($join) {
                $join->on('tipo_cambios.mes', '=', 'in_resultados.mes')
                    ->on('tipo_cambios.ano', '=', 'in_resultados.ano')
                    ->on('tipo_cambios.id_excel', '=', 'in_resultados.id_excel');
            })
            ->leftJoin('tipo_cambios_globals', function ($join) {
                $join->on("tipo_cambios_globals.id_estudio", "=", "companies.id_estudio")
                    ->on('tipo_cambios_globals.mes', '=', 'in_resultados.mes')
                    ->on('tipo_cambios_globals.ano', '=', 'in_resultados.ano');
            })
            ->join('clasificacion_cuenta_resuls', function ($join) {
                $join->on("clasificacion_cuenta_resuls.id_excel", "=", "in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta", "=", "in_resultados.cuenta_master");
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', 'companies.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id)
            ->whereRaw("DATE(CONCAT(in_resultados.ano, '-', in_resultados.mes, '-01')) BETWEEN ? AND ?", [$fechaInicio, $fechaFin])
            ->where('clasificacion_cuenta_resuls.espacio_fiscal_ajuste', 'Si');

        if ($user->id_company_show > 0) {
            $queryResultados->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }

        if ($sucursal > 0) {
            $sucursalas = sucursales::find($sucursal);
            if ($sucursalas) {
                $queryResultados->join('sucursales', function ($join) {
                    $join->on("sucursales.id_excel", "=", "in_resultados.id_excel")
                        ->on("sucursales.nombre", "=", "in_resultados.sucursal");
                })->where('sucursales.nombre', $sucursalas->nombre);
            }
        }

        $queryResultados->select(DB::raw($selectResultados));

        $selectPresupuesto = "
            'in_presupuesto' as tipo,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))) as amount_uyu,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3))) as amount_uyu_dolar_compra,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
            (CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
            clasificacion_cuenta_resuls.nivel_3,
            clasificacion_cuenta_resuls.grupo,
            clasificacion_cuenta_resuls.nombre,
            clasificacion_cuenta_resuls.cuenta,
            in_presupuestos.operador,
            in_presupuestos.signo,
            in_presupuestos.monto_valor,
            in_presupuestos.mes,
            in_presupuestos.ano
        ";

        $queryPresupuesto = DB::table('in_presupuestos')
            ->join('excelscompanies', 'excelscompanies.id', 'in_presupuestos.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function ($join) {
                $join->on('tipo_cambios.mes', '=', 'in_presupuestos.mes')
                    ->on('tipo_cambios.ano', '=', 'in_presupuestos.ano')
                    ->on('tipo_cambios.id_excel', '=', 'in_presupuestos.id_excel');
            })
            ->leftJoin('tipo_cambios_globals', function ($join) {
                $join->on("tipo_cambios_globals.id_estudio", "=", "companies.id_estudio")
                    ->on('tipo_cambios_globals.mes', '=', 'in_presupuestos.mes')
                    ->on('tipo_cambios_globals.ano', '=', 'in_presupuestos.ano');
            })
            ->join('clasificacion_cuenta_resuls', function ($join) {
                $join->on("clasificacion_cuenta_resuls.id_excel", "=", "in_presupuestos.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta", "=", "in_presupuestos.cuenta_master");
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', 'companies.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id)
            ->whereRaw("DATE(CONCAT(in_presupuestos.ano, '-', in_presupuestos.mes, '-01')) BETWEEN ? AND ?", [$fechaInicioPresupuesto, $fechaFinFiscal])
            ->where('clasificacion_cuenta_resuls.espacio_fiscal_ajuste', 'Si');

        if ($user->id_company_show > 0) {
            $queryPresupuesto->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }

        if ($sucursal > 0) {
            $sucursalas = sucursales::find($sucursal);
            if ($sucursalas) {
                $queryPresupuesto->join('sucursales', function ($join) {
                    $join->on("sucursales.id_excel", "=", "in_presupuestos.id_excel")
                        ->on("sucursales.nombre", "=", "in_presupuestos.sucursal");
                })->where('sucursales.nombre', $sucursalas->nombre);
            }
        }

        $queryPresupuesto->select(DB::raw($selectPresupuesto));

        $DataEspacioFiscal = $queryResultados->unionAll($queryPresupuesto)->get();

        // -------------------------------------------------------------
        // Query 3: exonPI (Consolidado idéntico a Anticipos IRAE)
        // -------------------------------------------------------------
        $qExonResultados = DB::table('in_resultados')
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function ($join) {
                $join->on('tipo_cambios.mes', '=', 'in_resultados.mes')
                    ->on('tipo_cambios.ano', '=', 'in_resultados.ano')
                    ->on('tipo_cambios.id_excel', '=', 'in_resultados.id_excel');
            })
            ->leftJoin('tipo_cambios_globals', function ($join) {
                $join->on("tipo_cambios_globals.id_estudio", "=", "companies.id_estudio")
                    ->on('tipo_cambios_globals.mes', '=', 'in_resultados.mes')
                    ->on('tipo_cambios_globals.ano', '=', 'in_resultados.ano');
            })
            ->join('clasificacion_cuenta_resuls', function ($join) {
                $join->on("clasificacion_cuenta_resuls.id_excel", "=", "in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta", "=", "in_resultados.cuenta_master");
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', 'companies.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id)
            ->whereRaw("DATE(CONCAT(in_resultados.ano, '-', in_resultados.mes, '-01')) BETWEEN ? AND ?", [$fechaInicio, $fechaFin])
            ->where('clasificacion_cuenta_resuls.irae', 'Si')
            ->select(DB::raw("
                'in_resultados' as tipo,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3)) as amount_uyu,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) as amount_uyu_dolar_compra,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) as amount_uyu_dolar_venta,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) as amount_uyu_dolar_promedio,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) as amount_uyu_euro_promedio,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) as amount_uyu_francosuizo_promedio,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3)) as amount_uyu_ui,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as amount_uyu_ipc,
                CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3)) as amount_uyu_ipc_empresa
            "));

        $qExonPresupuestos = DB::table('in_presupuestos')
            ->join('excelscompanies', 'excelscompanies.id', 'in_presupuestos.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function ($join) {
                $join->on('tipo_cambios.mes', '=', 'in_presupuestos.mes')
                    ->on('tipo_cambios.ano', '=', 'in_presupuestos.ano')
                    ->on('tipo_cambios.id_excel', '=', 'in_presupuestos.id_excel');
            })
            ->leftJoin('tipo_cambios_globals', function ($join) {
                $join->on("tipo_cambios_globals.id_estudio", "=", "companies.id_estudio")
                    ->on('tipo_cambios_globals.mes', '=', 'in_presupuestos.mes')
                    ->on('tipo_cambios_globals.ano', '=', 'in_presupuestos.ano');
            })
            ->join('clasificacion_cuenta_resuls', function ($join) {
                $join->on("clasificacion_cuenta_resuls.id_excel", "=", "in_presupuestos.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta", "=", "in_presupuestos.cuenta_master");
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', 'companies.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id)
            ->whereRaw("DATE(CONCAT(in_presupuestos.ano, '-', in_presupuestos.mes, '-01')) BETWEEN ? AND ?", [$fechaInicioPresupuesto, $fechaFinFiscal])
            ->where('clasificacion_cuenta_resuls.irae', 'Si')
            ->select(DB::raw("
                'in_presupuesto' as tipo,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3)) as amount_uyu,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) as amount_uyu_dolar_compra,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) as amount_uyu_dolar_venta,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) as amount_uyu_dolar_promedio,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) as amount_uyu_euro_promedio,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) as amount_uyu_francosuizo_promedio,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3)) as amount_uyu_ui,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as amount_uyu_ipc,
                CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3)) as amount_uyu_ipc_empresa
            "));

        if ($user->id_company_show > 0) {
            $qExonResultados->where('grupo_economicos_empresas.id_company', $user->id_company_show);
            $qExonPresupuestos->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }

        if ($sucursal > 0) {
            $sucursalas = sucursales::find($sucursal);
            if ($sucursalas) {
                $qExonResultados->join('sucursales', function ($join) {
                    $join->on("sucursales.id_excel", "=", "in_resultados.id_excel")
                        ->on("sucursales.nombre", "=", "in_resultados.sucursal");
                })->where('sucursales.nombre', $sucursalas->nombre);

                $qExonPresupuestos->join('sucursales', function ($join) {
                    $join->on("sucursales.id_excel", "=", "in_presupuestos.id_excel")
                        ->on("sucursales.nombre", "=", "in_presupuestos.sucursal");
                })->where('sucursales.nombre', $sucursalas->nombre);
            }
        }

        $unionExon = $qExonResultados->unionAll($qExonPresupuestos);

        $exonPI = DB::table(DB::raw("({$unionExon->toSql()}) as sub_exon"))
            ->mergeBindings($unionExon)
            ->select(DB::raw("
                SUM(amount_uyu) as amount_uyu,
                SUM(amount_uyu_dolar_compra) as amount_uyu_dolar_compra,
                SUM(amount_uyu_dolar_venta) as amount_uyu_dolar_venta,
                SUM(amount_uyu_dolar_promedio) as amount_uyu_dolar_promedio,
                SUM(amount_uyu_euro_promedio) as amount_uyu_euro_promedio,
                SUM(amount_uyu_francosuizo_promedio) as amount_uyu_francosuizo_promedio,
                SUM(amount_uyu_ui) as amount_uyu_ui,
                SUM(amount_uyu_ipc) as amount_uyu_ipc,
                SUM(amount_uyu_ipc_empresa) as amount_uyu_ipc_empresa
            "))
            ->first();

        // -------------------------------------------------------------
        // Query 4: Anticipos acumulados e IRAE
        // -------------------------------------------------------------
        $porcentajeIrae = $company ? (float) $company->porcentaje_irae : 0;
        $cuentaAnticiposIrae = $company ? $company->cuenta_anticipos_irae : null;
        $sumaAnticiposIrae = null;

        if (!empty($cuentaAnticiposIrae)) {
            $qAnticiposResultados = DB::table('in_resultados')
                ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
                ->join('companies', 'companies.id', 'excelscompanies.id_company')
                ->leftJoin('tipo_cambios', function ($join) {
                    $join->on('tipo_cambios.mes', '=', 'in_resultados.mes')
                        ->on('tipo_cambios.ano', '=', 'in_resultados.ano')
                        ->on('tipo_cambios.id_excel', '=', 'in_resultados.id_excel');
                })
                ->leftJoin('tipo_cambios_globals', function ($join) {
                    $join->on("tipo_cambios_globals.id_estudio", "=", "companies.id_estudio")
                        ->on('tipo_cambios_globals.mes', '=', 'in_resultados.mes')
                        ->on('tipo_cambios_globals.ano', '=', 'in_resultados.ano');
                })
                ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', 'companies.id')
                ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', 'grupo_economicos_empresas.id_grupoeconomico')
                ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
                ->where('usuario_grupoeconomicos.id_users', $user->id)
                ->whereRaw("DATE(CONCAT(in_resultados.ano, '-', in_resultados.mes, '-01')) BETWEEN ? AND ?", [$fechaInicio, $fechaFin])
                ->where('in_resultados.cuenta_master', $cuentaAnticiposIrae)
                ->select(DB::raw("
                    'in_resultados' as tipo,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3)) as amount_uyu,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) as amount_uyu_dolar_compra,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) as amount_uyu_dolar_venta,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) as amount_uyu_dolar_promedio,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) as amount_uyu_euro_promedio,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) as amount_uyu_francosuizo_promedio,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3)) as amount_uyu_ui,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as amount_uyu_ipc,
                    CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3)) as amount_uyu_ipc_empresa
                "));

            $qAnticiposPresupuestos = DB::table('in_presupuestos')
                ->join('excelscompanies', 'excelscompanies.id', 'in_presupuestos.id_excel')
                ->join('companies', 'companies.id', 'excelscompanies.id_company')
                ->leftJoin('tipo_cambios', function ($join) {
                    $join->on('tipo_cambios.mes', '=', 'in_presupuestos.mes')
                        ->on('tipo_cambios.ano', '=', 'in_presupuestos.ano')
                        ->on('tipo_cambios.id_excel', '=', 'in_presupuestos.id_excel');
                })
                ->leftJoin('tipo_cambios_globals', function ($join) {
                    $join->on("tipo_cambios_globals.id_estudio", "=", "companies.id_estudio")
                        ->on('tipo_cambios_globals.mes', '=', 'in_presupuestos.mes')
                        ->on('tipo_cambios_globals.ano', '=', 'in_presupuestos.ano');
                })
                ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', 'companies.id')
                ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', 'grupo_economicos_empresas.id_grupoeconomico')
                ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
                ->where('usuario_grupoeconomicos.id_users', $user->id)
                ->whereRaw("DATE(CONCAT(in_presupuestos.ano, '-', in_presupuestos.mes, '-01')) BETWEEN ? AND ?", [$fechaInicioPresupuesto, $fechaFinFiscal])
                ->where('in_presupuestos.cuenta_master', $cuentaAnticiposIrae)
                ->select(DB::raw("
                    'in_presupuesto' as tipo,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3)) as amount_uyu,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) as amount_uyu_dolar_compra,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3)) as amount_uyu_dolar_venta,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3)) as amount_uyu_dolar_promedio,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3)) as amount_uyu_euro_promedio,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3)) as amount_uyu_francosuizo_promedio,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3)) as amount_uyu_ui,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as amount_uyu_ipc,
                    CAST(in_presupuestos.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3)) as amount_uyu_ipc_empresa
                "));

            if ($user->id_company_show > 0) {
                $qAnticiposResultados->where('grupo_economicos_empresas.id_company', $user->id_company_show);
                $qAnticiposPresupuestos->where('grupo_economicos_empresas.id_company', $user->id_company_show);
            }

            $unionAnticipos = $qAnticiposResultados->unionAll($qAnticiposPresupuestos);

            $sumaAnticiposIrae = DB::table(DB::raw("({$unionAnticipos->toSql()}) as sub_anticipos"))
                ->mergeBindings($unionAnticipos)
                ->select(DB::raw("
                    SUM(amount_uyu) as amount_uyu,
                    SUM(amount_uyu_dolar_compra) as amount_uyu_dolar_compra,
                    SUM(amount_uyu_dolar_venta) as amount_uyu_dolar_venta,
                    SUM(amount_uyu_dolar_promedio) as amount_uyu_dolar_promedio,
                    SUM(amount_uyu_euro_promedio) as amount_uyu_euro_promedio,
                    SUM(amount_uyu_francosuizo_promedio) as amount_uyu_francosuizo_promedio,
                    SUM(amount_uyu_ui) as amount_uyu_ui,
                    SUM(amount_uyu_ipc) as amount_uyu_ipc,
                    SUM(amount_uyu_ipc_empresa) as amount_uyu_ipc_empresa
                "))
                ->first();
        }

        $data = [
            'espacio_fiscal'               => $DataEspacioFiscal,
            'exon_pi'                      => $exonPI,
            'porcentaje_irae'              => $porcentajeIrae,
            'suma_cuenta_anticipos_irae'   => $sumaAnticiposIrae
        ];

        return $this->sendResponse($data, 'Espacio Fiscal Acumulado recuperado correctamente');
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
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,5))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
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
            ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
        }
        if ($year > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.ano', $year);
        }
        if ($month > 0) {
            $sqlCheck = $sqlCheck->where('in_resultados.mes', $month);
        }

        $sqlCheck = $sqlCheck->groupByRaw('clasificacion_er,clasificacion_punto_equilibrio,in_resultados.mes,in_resultados.ano,cuenta,grupo,origen');
        $data = $sqlCheck->get();

        return $this->sendResponse($data, 'In Resultadoss retrieved successfully');
    }
    public function post_modal(Request $request){
        $input=$request->all();
        $user = auth()->guard('api')->user();
        if(!isset($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        clasificacion_cuenta_resuls.nombre,
        clasificacion_cuenta_resuls.cuenta,
        clasificacion_cuenta_resuls.origen'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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

        if($input['sucursal']>0){
            $sucursalas = sucursales::find($input['sucursal']);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
        }

        
        $sqlCheck = $sqlCheck->where('in_resultados.ano', $input['ano']);
        $sqlCheck = $sqlCheck->where('in_resultados.mes', $input['mes']);
        $textoBusqueda=$input['tipo'];
        $nombre=$input['nombre'];
        
        $sqlCheck = $sqlCheck->where('clasificacion_cuenta_resuls.'.$textoBusqueda, $nombre);
        $sqlCheck = $sqlCheck->groupByRaw('nombre,cuenta,origen');
        
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
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as ipc,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);

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

  
    public function showEstadisticas($sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
            })
            ->join('clasificacion_cuenta_resuls',function($join){
                $join->on("clasificacion_cuenta_resuls.id_excel","=","in_resultados.id_excel")
                    ->on("clasificacion_cuenta_resuls.cuenta","=","in_resultados.cuenta_master");
            });


            $sqlCheck= $sqlCheck->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
            $sqlCheck= $sqlCheck->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
            $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
            $sqlCheck= $sqlCheck->where('clasificacion_cuenta_resuls.origen','<>','Jurídico');
            
            $sqlCheck= $sqlCheck->where('usuario_grupoeconomicos.id_users',$user->id);
            if($user->id_company_show>0){
                $sqlCheck= $sqlCheck->where('grupo_economicos_empresas.id_company',$user->id_company_show);
            }


        if($sucursal>0){
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
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
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) as date'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);

        }
        $sqlCheck = $sqlCheck->groupByRaw('ipc,clasificacion_ebit_ebitda,DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1"))');
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")) ASC');
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
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as ipc,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);

        }
        $sqlCheck = $sqlCheck->groupByRaw('
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        in_resultados.ano,
        tipo_cambios_globals.ipc,
        clasificacion_cuenta_resuls.id,
        in_resultados.mes');
        
       $sqlCheck= $sqlCheck->orderByRaw('clasificacion_cuenta_resuls.id ASC');
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
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as ipc,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);

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
        $dataSqls='SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
                $sucursalas = sucursales::find($sucursal);    
                $sqlCheck=$sqlCheck->join('sucursales',function($join){
                    $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                        ->on("sucursales.nombre","=","in_resultados.sucursal");
                });
                $sqlCheck2=$sqlCheck2->join('sucursales',function($join){
                    $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                        ->on("sucursales.nombre","=","in_resultados.sucursal");
                });
                $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
                $sqlCheck2=$sqlCheck2->where('sucursales.nombre',$sucursalas->nombre);
            }
            $sqlCheck2 = $sqlCheck2->groupByRaw('
            clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        in_resultados.ano,
        in_resultados.mes');
            $sqlCheck = $sqlCheck->groupByRaw('
            clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        in_resultados.ano,
        in_resultados.mes');
            $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")),clasificacion_cuenta_resuls.id ASC');
            $sqlCheck2 = $sqlCheck2->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")),clasificacion_cuenta_resuls.id ASC');
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
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        (CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3)) as ipc,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
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

        public function showinformedesdeyhasta($yeardesde, $monthdesde, $yearhasta, $monthhasta, $sucursal): JsonResponse {
            $dataSqls = 'SUM(in_resultados.monto_uyu) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
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
            if ($user->getContainestudios2($user->id)) {
                return $this->sendResponse([], 'No tienes Estudios');
            }

            $fechaInicio = $yeardesde . '-' . str_pad($monthdesde, 2, "0", STR_PAD_LEFT) . '-01';
            $fechaFin = $yearhasta . '-' . str_pad($monthhasta, 2, "0", STR_PAD_LEFT) . '-01';

            $sqlCheck = in_resultado::select(DB::raw($dataSqls))
                ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
                ->join('companies', 'companies.id', 'excelscompanies.id_company')
                 ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })
                ->leftjoin('tipo_cambios_globals', function($join) {
                    $join->on("tipo_cambios_globals.id_estudio", "=", "companies.id_estudio")
                        ->on('tipo_cambios_globals.mes', '=', 'in_resultados.mes')
                        ->on('tipo_cambios_globals.ano', '=', 'in_resultados.ano');
                })
                ->join('clasificacion_cuenta_resuls', function($join) {
                    $join->on("clasificacion_cuenta_resuls.id_excel", "=", "in_resultados.id_excel")
                        ->on("clasificacion_cuenta_resuls.cuenta", "=", "in_resultados.cuenta_master");
                })
                ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', 'companies.id')
                ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show);
            $sqlCheck = $sqlCheck->whereRaw("DATE(CONCAT(in_resultados.ano, '-', in_resultados.mes, '-01')) BETWEEN ? AND ?", [$fechaInicio, $fechaFin]);

            if ($user->id_company_show > 0) {
                $sqlCheck = $sqlCheck->where('grupo_economicos_empresas.id_company', $user->id_company_show);
            }

           if($sucursal>0){
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
        }

            $data = $sqlCheck->groupByRaw('
                    clasificacion_cuenta_resuls.origen, 
                    clasificacion_cuenta_resuls.grupo, 
                    clasificacion_cuenta_resuls.nivel_1, 
                    clasificacion_cuenta_resuls.nivel_2, 
                    clasificacion_cuenta_resuls.nivel_3, 
                    clasificacion_cuenta_resuls.clasificacion_er, 
                    clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
                    in_resultados.ano, 
                    in_resultados.mes')
                ->orderByRaw('in_resultados.ano ASC, in_resultados.mes ASC, clasificacion_cuenta_resuls.id ASC')
                ->get();

            return $this->sendResponse(['actual' => $data], 'Datos recuperados desde ' . $fechaInicio . ' hasta ' . $fechaFin);
    }
    public function showDataLegal($year, $month,$sucursal): JsonResponse
    {
        $user = auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $id_estudios=$user->getIdEstudios2($user->id);
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,5)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,5))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,5))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,5))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,5))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,5))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,5))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,5))) as amount_uyu_ipc,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
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
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3)) )as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.clasificacion_ratios_financ,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        in_resultados.ano,
        in_resultados.mes'))
            ->join('excelscompanies', 'excelscompanies.id', 'in_resultados.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company')
            ->leftJoin('tipo_cambios', function($joins)
            {
                $joins->on('tipo_cambios.mes','=','in_resultados.mes')
                ->on('tipo_cambios.ano','=','in_resultados.ano')
                ->on('tipo_cambios.id_excel','=','in_resultados.id_excel');
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
        }
        $sqlCheck = $sqlCheck->groupByRaw('
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.clasificacion_ratios_financ,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        clasificacion_cuenta_resuls.clasificacion_er,
        clasificacion_cuenta_resuls.clasificacion_ebit_ebitda,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        in_resultados.ano,
        in_resultados.mes');
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")),clasificacion_cuenta_resuls.id ASC');
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
        $sqlCheck = in_resultado::SELECT(DB::raw('SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))) as amount_uyu,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_compra AS DECIMAL(18,3)) ) as amount_uyu_dolar_compra,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ui AS DECIMAL(18,3))) as amount_uyu_ui,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios_globals.ipc AS DECIMAL(18,3))) as amount_uyu_ipc,
        SUM(CAST(in_resultados.monto_uyu AS DECIMAL(18,3))/CAST(tipo_cambios.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa,
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
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
            })  ->leftjoin('tipo_cambios_globals',function($join){
                $join->on("tipo_cambios_globals.id_estudio","=","companies.id_estudio")
                ->on('tipo_cambios_globals.mes','=','in_resultados.mes')
                ->on('tipo_cambios_globals.ano','=','in_resultados.ano');
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
            $sucursalas = sucursales::find($sucursal);
            $sqlCheck=$sqlCheck->join('sucursales',function($join){
                $join->on("sucursales.id_excel","=","in_resultados.id_excel")
                    ->on("sucursales.nombre","=","in_resultados.sucursal");
            });
            $sqlCheck=$sqlCheck->where('sucursales.nombre',$sucursalas->nombre);
        }
        $sqlCheck = $sqlCheck->groupByRaw('
        clasificacion_cuenta_resuls.origen,
        clasificacion_cuenta_resuls.grupo,
        clasificacion_cuenta_resuls.nivel_1,
        clasificacion_cuenta_resuls.nivel_2,
        clasificacion_cuenta_resuls.nivel_3,
        clasificacion_cuenta_resuls.clasificacion_punto_equilibrio,
        in_resultados.ano,
        in_resultados.mes');
        $sqlCheck = $sqlCheck->orderByRaw('DATE(CONCAT (in_resultados.ano,"-",in_resultados.mes,"-","1")),clasificacion_cuenta_resuls.id ASC');
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
