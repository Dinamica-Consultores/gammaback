<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEspacioFiscalAPIRequest;
use App\Http\Requests\API\UpdateEspacioFiscalAPIRequest;
use App\Models\EspacioFiscal;
use App\Repositories\EspacioFiscalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use App\Models\sucursales;

class EspacioFiscalAPIController extends AppBaseController
{
    private EspacioFiscalRepository $espacioFiscalRepository;

    public function __construct(EspacioFiscalRepository $espacioFiscalRepo)
    {
        $this->espacioFiscalRepository = $espacioFiscalRepo;
    }

    public function index(Request $request): JsonResponse
    {
        $espacioFiscals = $this->espacioFiscalRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($espacioFiscals->toArray(), 'Espacio Fiscals retrieved successfully');
    }

    public function store(CreateEspacioFiscalAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        $espacioFiscal = $this->espacioFiscalRepository->create($input);

        return $this->sendResponse($espacioFiscal->toArray(), 'Espacio Fiscal saved successfully');
    }

    public function show($id): JsonResponse
    {
        /** @var EspacioFiscal $espacioFiscal */
        $espacioFiscal = $this->espacioFiscalRepository->find($id);

        if (empty($espacioFiscal)) {
            return $this->sendError('Espacio Fiscal not found');
        }

        return $this->sendResponse($espacioFiscal->toArray(), 'Espacio Fiscal retrieved successfully');
    }

    public function update($id, UpdateEspacioFiscalAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var EspacioFiscal $espacioFiscal */
        $espacioFiscal = $this->espacioFiscalRepository->find($id);

        if (empty($espacioFiscal)) {
            return $this->sendError('Espacio Fiscal not found');
        }

        $espacioFiscal = $this->espacioFiscalRepository->update($input, $id);

        return $this->sendResponse($espacioFiscal->toArray(), 'EspacioFiscal updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        /** @var EspacioFiscal $espacioFiscal */
        $espacioFiscal = $this->espacioFiscalRepository->find($id);

        if (empty($espacioFiscal)) {
            return $this->sendError('Espacio Fiscal not found');
        }

        $espacioFiscal->delete();

        return $this->sendSuccess('Espacio Fiscal deleted successfully');
    }

    /**
     * Nuevo endpoint para obtener los datos directos de espacio_fiscals por período
     * GET /espacio-fiscals/by-fecha
     */
    public function getByFecha(Request $request): JsonResponse
    {
        $year     = $request->input('ano') ?? $request->input('year');
        $month    = $request->input('mes') ?? $request->input('month');
        $sucursal = $request->input('sucursal', 0);

        $user = auth()->guard('api')->user();
        if (!$user) {
            return $this->sendError('Usuario no autenticado', 401);
        }

        // 1. Obtención y validación de la Compañía según permisos
        $queryCompany = DB::table('companies')
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'companies.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id);

        if ($user->id_company_show > 0) {
            $queryCompany->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }

        $company = $queryCompany->select('companies.*')->first();

        // 2. Consulta de registros de espacio_fiscals (Excluyendo EXONPI)
        $sqlEspacio = DB::table('espacio_fiscals as ef')
        ->join('excelscompanies as ec', 'ec.id', '=', 'ef.id_excel')
        ->join('companies as comp', 'comp.id', '=', 'ec.id_company')
        ->leftJoin('tipo_cambios as tc', function ($join) {
            $join->on('tc.mes', '=', 'ef.mes')
                 ->on('tc.ano', '=', 'ef.ano')
                 ->on('tc.id_excel', '=', 'ef.id_excel');
        })
        ->leftJoin('tipo_cambios_globals as tg', function ($join) {
            $join->on('tg.id_estudio', '=', 'comp.id_estudio')
                 ->on('tg.mes', '=', 'ef.mes')
                 ->on('tg.ano', '=', 'ef.ano');
        })
        ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'comp.id')
        ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
        ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
        ->where('usuario_grupoeconomicos.id_users', $user->id)
        ->where('ef.ano', $year)
        ->where('ef.mes', $month)
        ->where('ef.tipo', '!=', 'EXONPI');

    if ($user->id_company_show > 0) {
        $sqlEspacio->where('grupo_economicos_empresas.id_company', $user->id_company_show);
    }

    if ((int)$sucursal > 0) {
        $sucursalas = sucursales::find($sucursal);
        if ($sucursalas) {
            $sqlEspacio->where('ef.sucursal', $sucursalas->nombre);
        }
    }

    $datosEspacioFiscal = $sqlEspacio->select(
        'ef.*',
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) ELSE ef.monto_valor END as amount_uyu_monto_uyu"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tg.dolar_compra AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_dolar_compra"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tg.dolar_venta AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_dolar_venta"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tg.dolar_promedio AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_dolar_promedio"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tg.euro_promedio AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_euro_promedio"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tg.francosuizo_promedio AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_francosuizo_promedio"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tg.ui AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_ui"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tg.ipc AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_ipc"),
        DB::raw("CASE WHEN LOWER(ef.operador) = 'monto' THEN CAST(ef.monto_valor AS DECIMAL(18,3)) / CAST(tc.ipc_empresa AS DECIMAL(18,3)) ELSE 0 END as amount_uyu_ipc_empresa")
    )->get();

        // 3. Obtención y cálculo multi-moneda independiente para EXONPI
        $exonPiMonedas = [
            'amount_uyu_monto_uyu'            => 0,
            'amount_uyu_dolar_compra'         => 0,
            'amount_uyu_dolar_venta'          => 0,
            'amount_uyu_dolar_promedio'       => 0,
            'amount_uyu_euro_promedio'        => 0,
            'amount_uyu_francosuizo_promedio' => 0,
            'amount_uyu_ui'                   => 0,
            'amount_uyu_ipc'                  => 0,
            'amount_uyu_ipc_empresa'          => 0,
        ];

        $sqlExonPi = DB::table('espacio_fiscals as ef')
            ->join('excelscompanies as ec', 'ec.id', '=', 'ef.id_excel')
            ->join('companies as comp', 'comp.id', '=', 'ec.id_company')
            ->leftJoin('tipo_cambios as tc', function ($join) {
                $join->on('tc.mes', '=', 'ef.mes')
                     ->on('tc.ano', '=', 'ef.ano')
                     ->on('tc.id_excel', '=', 'ef.id_excel');
            })
            ->leftJoin('tipo_cambios_globals as tg', function ($join) {
                $join->on('tg.id_estudio', '=', 'comp.id_estudio')
                     ->on('tg.mes', '=', 'ef.mes')
                     ->on('tg.ano', '=', 'ef.ano');
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'comp.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id)
            ->where('ef.tipo', 'EXONPI')
            ->where('ef.ano', $year)
            ->where('ef.mes', $month);

        if ($user->id_company_show > 0) {
            $sqlExonPi->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }

        if ((int)$sucursal > 0) {
            $sucursalas = sucursales::find($sucursal);
            if ($sucursalas) {
                $sqlExonPi->where('ef.sucursal', $sucursalas->nombre);
            }
        }

        $resExonPi = $sqlExonPi->select(
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))) as monto_uyu"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3))) as dolar_compra"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3))) as dolar_venta"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3))) as dolar_promedio"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3))) as euro_promedio"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3))) as francosuizo_promedio"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3))) as ui"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3))) as ipc"),
            DB::raw("SUM(CAST(ef.monto_valor AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3))) as ipc_empresa")
        )->first();

        if ($resExonPi) {
            $exonPiMonedas = [
                'amount_uyu_monto_uyu'            => (float) ($resExonPi->monto_uyu ?? 0),
                'amount_uyu_dolar_compra'         => (float) ($resExonPi->dolar_compra ?? 0),
                'amount_uyu_dolar_venta'          => (float) ($resExonPi->dolar_venta ?? 0),
                'amount_uyu_dolar_promedio'       => (float) ($resExonPi->dolar_promedio ?? 0),
                'amount_uyu_euro_promedio'        => (float) ($resExonPi->euro_promedio ?? 0),
                'amount_uyu_francosuizo_promedio' => (float) ($resExonPi->francosuizo_promedio ?? 0),
                'amount_uyu_ui'                   => (float) ($resExonPi->ui ?? 0),
                'amount_uyu_ipc'                  => (float) ($resExonPi->ipc ?? 0),
                'amount_uyu_ipc_empresa'          => (float) ($resExonPi->ipc_empresa ?? 0),
            ];
        }

        // 4. Cálculo de la cuenta IRAE en todas las monedas
        $totalCuentaIraeMonedas = [
            'amount_uyu_monto_uyu'            => 0,
            'amount_uyu_dolar_compra'         => 0,
            'amount_uyu_dolar_venta'          => 0,
            'amount_uyu_dolar_promedio'       => 0,
            'amount_uyu_euro_promedio'        => 0,
            'amount_uyu_francosuizo_promedio' => 0,
            'amount_uyu_ui'                   => 0,
            'amount_uyu_ipc'                  => 0,
            'amount_uyu_ipc_empresa'          => 0,
        ];

        if ($company && !empty($company->cuenta_anticipos_irae)) {
            $sqlIrae = DB::table('in_resultados as r')
                ->join('excelscompanies as ec', 'ec.id', '=', 'r.id_excel')
                ->join('companies as comp', 'comp.id', '=', 'ec.id_company')
                ->leftJoin('tipo_cambios as tc', function ($join) {
                    $join->on('tc.mes', '=', 'r.mes')
                         ->on('tc.ano', '=', 'r.ano')
                         ->on('tc.id_excel', '=', 'r.id_excel');
                })
                ->leftJoin('tipo_cambios_globals as tg', function ($join) {
                    $join->on('tg.id_estudio', '=', 'comp.id_estudio')
                         ->on('tg.mes', '=', 'r.mes')
                         ->on('tg.ano', '=', 'r.ano');
                })
                ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'comp.id')
                ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
                ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
                ->where('usuario_grupoeconomicos.id_users', $user->id)
                ->where('r.cuenta_master', $company->cuenta_anticipos_irae)
                ->where('r.ano', $year)
                ->where('r.mes', $month);

            if ($user->id_company_show > 0) {
                $sqlIrae->where('grupo_economicos_empresas.id_company', $user->id_company_show);
            }

            if ((int)$sucursal > 0) {
                $sucursalas = sucursales::find($sucursal);
                if ($sucursalas) {
                    $sqlIrae->where('r.sucursal', $sucursalas->nombre);
                }
            }

            $resIrae = $sqlIrae->select(
                DB::raw("SUM(r.monto_uyu) as monto_uyu"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3))) as dolar_compra"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3))) as dolar_venta"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3))) as dolar_promedio"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3))) as euro_promedio"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3))) as francosuizo_promedio"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3))) as ui"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3))) as ipc"),
                DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3))) as ipc_empresa")
            )->first();

            if ($resIrae) {
                $totalCuentaIraeMonedas = [
                    'amount_uyu_monto_uyu'            => (float) ($resIrae->monto_uyu ?? 0),
                    'amount_uyu_dolar_compra'         => (float) ($resIrae->dolar_compra ?? 0),
                    'amount_uyu_dolar_venta'          => (float) ($resIrae->dolar_venta ?? 0),
                    'amount_uyu_dolar_promedio'       => (float) ($resIrae->dolar_promedio ?? 0),
                    'amount_uyu_euro_promedio'        => (float) ($resIrae->euro_promedio ?? 0),
                    'amount_uyu_francosuizo_promedio' => (float) ($resIrae->francosuizo_promedio ?? 0),
                    'amount_uyu_ui'                   => (float) ($resIrae->ui ?? 0),
                    'amount_uyu_ipc'                  => (float) ($resIrae->ipc ?? 0),
                    'amount_uyu_ipc_empresa'          => (float) ($resIrae->ipc_empresa ?? 0),
                ];
            }
        }

        return $this->sendResponse([
            'espacio_fiscal' => $datosEspacioFiscal,
            'empresa_config' => [
                'cuenta_anticipos_irae' => $company->cuenta_anticipos_irae ?? null,
                'porcentaje_irae'       => $company->porcentaje_irae ?? 0,
                'cuenta_anticipos_ipat' => $company->cuenta_anticipos_ipat ?? null,
                'porcentaje_ipat'       => $company->porcentaje_ipat ?? 0,
            ],
            'exon_pi'           => $exonPiMonedas,
            'total_cuenta_irae' => $totalCuentaIraeMonedas,
        ], 'Datos de espacio fiscal y configuración recuperados exitosamente');
    }

    public function showReporteRealvsPresupuestoFiscal(Request $request): JsonResponse
    {
        $year     = $request->input('ano') ?? $request->input('year') ?? date('Y');
        $month    = $request->input('mes') ?? $request->input('month') ?? date('n');
        $sucursal = (int) ($request->input('sucursal') ?? $request->input('sucursales') ?? 0);

        $user = auth()->guard('api')->user();
        if ($user->getContainestudios2($user->id)) {
            return $this->sendResponse([], 'No tienes Estudios');
        }
    
        $idCompany = $user->id_company_show;
        $company = DB::table('companies')->where('id', $idCompany)->first();
        
        $mesInicioFiscal = (int) ($company->mes_inicio_fiscal ?? 6);
    
        if ((int)$month >= $mesInicioFiscal) {
            $anoInicio = (int)$year;
            $anoFin    = (int)$year + 1;
        } else {
            $anoInicio = (int)$year - 1;
            $anoFin    = (int)$year;
        }
    
        $mesFinFiscal = ($mesInicioFiscal == 1) ? 12 : ($mesInicioFiscal - 1);
        $anoConsulta  = (int)$year;
        $mesConsulta  = (int)$month;
    
        // 1. CONSULTA DE RESULTADOS REALES
        $sqlReales = DB::table('in_resultados as r')
            ->join('excelscompanies as ec', 'ec.id', '=', 'r.id_excel')
            ->join('companies as comp', 'comp.id', '=', 'ec.id_company')
            ->leftJoin('tipo_cambios as tc', function ($join) {
                $join->on('tc.mes', '=', 'r.mes')
                     ->on('tc.ano', '=', 'r.ano')
                     ->on('tc.id_excel', '=', 'r.id_excel');
            })
            ->leftJoin('tipo_cambios_globals as tg', function ($join) {
                $join->on('tg.id_estudio', '=', 'comp.id_estudio')
                     ->on('tg.mes', '=', 'r.mes')
                     ->on('tg.ano', '=', 'r.ano');
            })
            ->join('clasificacion_cuenta_resuls as c', function ($join) {
                $join->on('c.cuenta', '=', 'r.cuenta_master')
                     ->on('c.id_excel', '=', 'r.id_excel');
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'comp.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id);
    
        if ($user->id_company_show > 0) {
            $sqlReales->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }
    
        if ($sucursal > 0) {
            $sucursalas = sucursales::find($sucursal);
            if ($sucursalas) {
                $sqlReales->join('sucursales', function ($join) {
                    $join->on('sucursales.id_excel', '=', 'r.id_excel')
                         ->on('sucursales.nombre', '=', 'r.sucursal');
                })->where('sucursales.nombre', $sucursalas->nombre);
            }
        }
    
        $sqlReales->where(function ($query) use ($anoInicio, $anoFin, $mesInicioFiscal, $mesFinFiscal) {
            if ($anoInicio === $anoFin) {
                $query->where('r.ano', $anoInicio)
                      ->whereBetween('r.mes', [$mesInicioFiscal, $mesFinFiscal]);
            } else {
                $query->where(function ($q) use ($anoInicio, $mesInicioFiscal) {
                    $q->where('r.ano', $anoInicio)
                      ->where('r.mes', '>=', $mesInicioFiscal);
                })->orWhere(function ($q) use ($anoFin, $mesFinFiscal) {
                    $q->where('r.ano', $anoFin)
                      ->where('r.mes', '<=', $mesFinFiscal);
                });
            }
        });
    
        $reales = $sqlReales->select(
            'c.grupo',
            'c.origen',
            'c.nivel_3',
            'r.ano',
            'r.mes',
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN r.monto_uyu ELSE 0 END) as amount_uyu_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_compra_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_venta_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_euro_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_francosuizo_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ui_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ipc_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((r.ano = {$anoInicio} AND r.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (r.ano = {$anoConsulta} AND r.mes <= {$mesConsulta})) THEN CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ipc_empresa_hasta_mes"),
            DB::raw("SUM(r.monto_uyu) as amount_uyu_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3))) as amount_uyu_dolar_compra_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3))) as amount_uyu_ui_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3))) as amount_uyu_ipc_ejercicio_completo"),
            DB::raw("SUM(CAST(r.monto_uyu AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa_ejercicio_completo")
        )
        ->groupBy('c.grupo', 'c.origen', 'c.nivel_3', 'r.ano', 'r.mes')
        ->orderBy('c.grupo')
        ->orderBy('c.origen')
        ->orderBy('c.nivel_3')
        ->orderBy('r.ano', 'asc')
        ->orderBy('r.mes', 'asc')
        ->get();
    
        // 2. CONSULTA DE PRESUPUESTOS
        $sqlPresupuestos = DB::table('in_presupuestos as p')
            ->join('excelscompanies as ec', 'ec.id', '=', 'p.id_excel')
            ->join('companies as comp', 'comp.id', '=', 'ec.id_company')
            ->leftJoin('tipo_cambios as tc', function ($join) {
                $join->on('tc.mes', '=', 'p.mes')
                     ->on('tc.ano', '=', 'p.ano')
                     ->on('tc.id_excel', '=', 'p.id_excel');
            })
            ->leftJoin('tipo_cambios_globals as tg', function ($join) {
                $join->on('tg.id_estudio', '=', 'comp.id_estudio')
                     ->on('tg.mes', '=', 'p.mes')
                     ->on('tg.ano', '=', 'p.ano');
            })
            ->join('clasificacion_cuenta_resuls as c', function ($join) {
                $join->on('c.cuenta', '=', 'p.cuenta_master')
                     ->on('c.id_excel', '=', 'p.id_excel');
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'comp.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id);
    
        if ($user->id_company_show > 0) {
            $sqlPresupuestos->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }
    
        if ($sucursal > 0) {
            $sucursalas = sucursales::find($sucursal);
            if ($sucursalas) {
                $sqlPresupuestos->join('sucursales', function ($join) {
                    $join->on('sucursales.id_excel', '=', 'p.id_excel')
                         ->on('sucursales.nombre', '=', 'p.sucursal');
                })->where('sucursales.nombre', $sucursalas->nombre);
            }
        }
    
        $sqlPresupuestos->where(function ($query) use ($anoInicio, $anoFin, $mesInicioFiscal, $mesFinFiscal) {
            if ($anoInicio === $anoFin) {
                $query->where('p.ano', $anoInicio)
                      ->whereBetween('p.mes', [$mesInicioFiscal, $mesFinFiscal]);
            } else {
                $query->where(function ($q) use ($anoInicio, $mesInicioFiscal) {
                    $q->where('p.ano', $anoInicio)
                      ->where('p.mes', '>=', $mesInicioFiscal);
                })->orWhere(function ($q) use ($anoFin, $mesFinFiscal) {
                    $q->where('p.ano', $anoFin)
                      ->where('p.mes', '<=', $mesFinFiscal);
                });
            }
        });
    
        $presupuestos = $sqlPresupuestos->select(
            'c.grupo',
            'c.origen',
            'c.nivel_3',
            'p.ano',
            'p.mes',
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN p.monto_uyu ELSE 0 END) as amount_uyu_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_compra_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_venta_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_euro_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_francosuizo_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ui_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ipc_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((p.ano = {$anoInicio} AND p.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (p.ano = {$anoConsulta} AND p.mes <= {$mesConsulta})) THEN CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ipc_empresa_hasta_mes"),
            DB::raw("SUM(p.monto_uyu) as amount_uyu_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3))) as amount_uyu_dolar_compra_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3))) as amount_uyu_ui_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3))) as amount_uyu_ipc_ejercicio_completo"),
            DB::raw("SUM(CAST(p.monto_uyu AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa_ejercicio_completo")
        )
        ->groupBy('c.grupo', 'c.origen', 'c.nivel_3', 'p.ano', 'p.mes')
        ->orderBy('c.grupo')
        ->orderBy('c.origen')
        ->orderBy('c.nivel_3')
        ->orderBy('p.ano', 'asc')
        ->orderBy('p.mes', 'asc')
        ->get();
    
        return $this->sendResponse([
            'reales'       => $reales,
            'presupuestos' => $presupuestos,
        ], 'Datos reales y presupuestos recuperados con éxito');
    }
    /**
     * Reporte de Balances (in_balances) acumulados según el Ejercicio Fiscal.
     * Retorna solo la información de saldos reales (no requiere presupuestos).
     */
   /**
     * Reporte de Balances (in_balances) acumulados según el Ejercicio Fiscal.
     * Muestra solo los niveles 1 y 3 de categorización de balances.
     */
    public function showReporteBalance(Request $request): JsonResponse
    {
        $year     = $request->input('ano') ?? $request->input('year') ?? date('Y');
        $month    = $request->input('mes') ?? $request->input('month') ?? date('n');
        $sucursal = (int) ($request->input('sucursal') ?? $request->input('sucursales') ?? 0);

        $user = auth()->guard('api')->user();
        if ($user->getContainestudios2($user->id)) {
            return $this->sendResponse([], 'No tienes Estudios');
        }

        $idCompany = $user->id_company_show;
        $company   = DB::table('companies')->where('id', $idCompany)->first();

        $mesInicioFiscal = (int) ($company->mes_inicio_fiscal ?? 6);

        // Cálculo del Rango de Fechas Fiscales
        if ((int)$month >= $mesInicioFiscal) {
            $anoInicio = (int)$year;
            $anoFin    = (int)$year + 1;
        } else {
            $anoInicio = (int)$year - 1;
            $anoFin    = (int)$year;
        }

        $mesFinFiscal = ($mesInicioFiscal == 1) ? 12 : ($mesInicioFiscal - 1);
        $anoConsulta  = (int)$year;
        $mesConsulta  = (int)$month;

        // CONSULTA DE BALANCES (in_balances)
        $sqlBalances = DB::table('in_balances as b')
            ->join('excelscompanies as ec', 'ec.id', '=', 'b.id_excel')
            ->join('companies as comp', 'comp.id', '=', 'ec.id_company')
            ->leftJoin('tipo_cambios as tc', function ($join) {
                $join->on('tc.mes', '=', 'b.mes')
                     ->on('tc.ano', '=', 'b.ano')
                     ->on('tc.id_excel', '=', 'b.id_excel');
            })
            ->leftJoin('tipo_cambios_globals as tg', function ($join) {
                $join->on('tg.id_estudio', '=', 'comp.id_estudio')
                     ->on('tg.mes', '=', 'b.mes')
                     ->on('tg.ano', '=', 'b.ano');
            })
            ->join('categorizacion_cts_balances as c', function ($join) {
                $join->on('c.cuenta', '=', 'b.cuenta_master')
                     ->on('c.id_excel', '=', 'b.id_excel');
            })
            ->join('grupo_economicos_empresas', 'grupo_economicos_empresas.id_company', '=', 'comp.id')
            ->join('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_grupoeconomico', '=', 'grupo_economicos_empresas.id_grupoeconomico')
            ->where('grupo_economicos_empresas.id_grupoeconomico', $user->id_group_show)
            ->where('usuario_grupoeconomicos.id_users', $user->id);

        if ($user->id_company_show > 0) {
            $sqlBalances->where('grupo_economicos_empresas.id_company', $user->id_company_show);
        }

        if ($sucursal > 0) {
            $sucursalas = sucursales::find($sucursal);
            if ($sucursalas) {
                $sqlBalances->join('sucursales', function ($join) {
                    $join->on('sucursales.id_excel', '=', 'b.id_excel')
                         ->on('sucursales.nombre', '=', 'b.sucursal');
                })->where('sucursales.nombre', $sucursalas->nombre);
            }
        }

        // Filtro por el Rango de Meses del Ejercicio Fiscal
        $sqlBalances->where(function ($query) use ($anoInicio, $anoFin, $mesInicioFiscal, $mesFinFiscal) {
            if ($anoInicio === $anoFin) {
                $query->where('b.ano', $anoInicio)
                      ->whereBetween('b.mes', [$mesInicioFiscal, $mesFinFiscal]);
            } else {
                $query->where(function ($q) use ($anoInicio, $mesInicioFiscal) {
                    $q->where('b.ano', $anoInicio)
                      ->where('b.mes', '>=', $mesInicioFiscal);
                })->orWhere(function ($q) use ($anoFin, $mesFinFiscal) {
                    $q->where('b.ano', $anoFin)
                      ->where('b.mes', '<=', $mesFinFiscal);
                });
            }
        });

        // Selección limitada a nivel_1 y nivel_3
        $balances = $sqlBalances->select(
            'c.nivel_1',
            'c.nivel_3',
            'b.ano',
            'b.mes',
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN b.saldo_uyu ELSE 0 END) as amount_uyu_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_compra_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_venta_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_dolar_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_euro_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_francosuizo_promedio_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ui_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ipc_hasta_mes"),
            DB::raw("SUM(CASE WHEN ((b.ano = {$anoInicio} AND b.mes >= {$mesInicioFiscal} AND {$anoInicio} < {$anoConsulta}) OR (b.ano = {$anoConsulta} AND b.mes <= {$mesConsulta})) THEN CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3)) ELSE 0 END) as amount_uyu_ipc_empresa_hasta_mes"),
            DB::raw("SUM(b.saldo_uyu) as amount_uyu_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.dolar_compra AS DECIMAL(18,3))) as amount_uyu_dolar_compra_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.dolar_venta AS DECIMAL(18,3))) as amount_uyu_dolar_venta_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.dolar_promedio AS DECIMAL(18,3))) as amount_uyu_dolar_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.euro_promedio AS DECIMAL(18,3))) as amount_uyu_euro_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.francosuizo_promedio AS DECIMAL(18,3))) as amount_uyu_francosuizo_promedio_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.ui AS DECIMAL(18,3))) as amount_uyu_ui_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tg.ipc AS DECIMAL(18,3))) as amount_uyu_ipc_ejercicio_completo"),
            DB::raw("SUM(CAST(b.saldo_uyu AS DECIMAL(18,3))/CAST(tc.ipc_empresa AS DECIMAL(18,3))) as amount_uyu_ipc_empresa_ejercicio_completo")
        )
        ->groupBy('c.nivel_1', 'c.nivel_3', 'b.ano', 'b.mes')
        ->orderBy('c.nivel_1')
        ->orderBy('c.nivel_3')
        ->orderBy('b.ano', 'asc')
        ->orderBy('b.mes', 'asc')
        ->get();

        return $this->sendResponse([
            'reales' => $balances,
        ], 'Datos de balance patrimonial fiscal recuperados con éxito');
    }
}