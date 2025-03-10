<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateexcelscompanyRequest;
use App\Http\Requests\UpdateexcelscompanyRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\excelscompanyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\company;
use Flash;
use App\Imports\ExcelIn;
use Excel;
use App\Models\excelscompany;
use App\Models\sucursales;
use App\Models\categorizacion_cts_balance;
use App\Models\clasificacion_cuenta_resul;
use App\Models\in_balance;
use App\Models\in_presupuestos;
use App\Models\in_resultado;
use App\Models\in_ventas;
use App\Models\setup_analisis;
use App\Models\tipo_cambio;
use App\Models\controlcuentas;

class excelscompanyController extends AppBaseController
{
    /** @var excelscompanyRepository $excelscompanyRepository*/
    private $excelscompanyRepository;

    public function __construct(excelscompanyRepository $excelscompanyRepo)
    {
        
        $this->excelscompanyRepository = $excelscompanyRepo;

    }

    /**
     * Display a listing of the excelscompany.
     */
    public function index(Request $request)
    {
        $excelscompanies = excelscompany::Select('excelscompanies.*')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->paginate(10);

        return view('excelscompanies.index')
            ->with('excelscompanies', $excelscompanies);
    }

    /**
     * Show the form for creating a new excelscompany.
     */
    public function create()
    {
       
        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        return view('excelscompanies.create')->with("company",$companies);
    }

    public function uploadfile($request,$tipo){
      
        $import = new ExcelIn;
        $input = $request->all();   
        if($request->hasFile('file')){
            $path = $request->file('file')->store('public/file');
            $path2 = str_replace('public/', '', $path);
            $input['path']=$path2;
        }else{
            Flash::error('No tienes el archivo excel.');
            return route('excelscompanies.index');
        }
        $array  = Excel::toArray($import, $request->file('file'));
    
        return $this->RegisterInfo($array,$tipo,$input,$path);
    }

    public function RegisterInfo($dataarray,$tipo,$request,$path){
        
        $data= DB::transaction(function () use ($request,$tipo,$dataarray,$path) {
            try {
            $input=$request;
            $this->DeleteAllByIDCompany($input['id_company']);
            $input['date']=date('Y-m-d H:i:s', strtotime($request['date']));
            $excelscompany = $this->excelscompanyRepository->create($input);

            $DataSucursales=$this->dataReturnSucursales( $dataarray,$excelscompany->id);
            foreach (array_chunk($DataSucursales,1000) as $t)  
            {
                $RetornoSucursal=sucursales::insert($t);
            }
            $DataCLASIFICACION_CUENTA_RESULTADOS=$this->dataReturnCLASIFICACION_CUENTA_RESULTADOS( $dataarray,$excelscompany->id);
         
            foreach (array_chunk($DataCLASIFICACION_CUENTA_RESULTADOS,1000) as $t)  
            {
                $RetornoCLASIFICACION_CUENTA_RESULTADOS=clasificacion_cuenta_resul::insert($t);
            }

            $DataIN_RESULTADOS=$this->dataReturnIN_RESULTADOS($dataarray,$excelscompany->id);
            foreach (array_chunk($DataIN_RESULTADOS,1000) as $t)  
            {
                $RetornoIN_RESULTADOS=in_resultado::insert($t);
            }

            $DataIN_PRESUPUESTOS=$this->dataReturnIN_PRESUPUESTOS($dataarray,$excelscompany->id);
            foreach (array_chunk($DataIN_PRESUPUESTOS,1000) as $t)  
            {
                $RetornoIN_PRESUPUESTOS=in_presupuestos::insert($t);
            }

            $DataTIPO_DE_CAMBIO=$this->dataReturnTIPO_DE_CAMBIO($dataarray,$excelscompany->id);
            foreach (array_chunk($DataTIPO_DE_CAMBIO,1000) as $t)  
            {
                $RetornoTIPO_DE_CAMBIO=tipo_cambio::insert($t);
            }

            $DataSETUP_ANALISIS=$this->dataReturnSETUP_ANALISIS($dataarray,$excelscompany->id);
            foreach (array_chunk($DataSETUP_ANALISIS,1000) as $t)  
            {
                $RetornoSETUP_ANALISIS=setup_analisis::insert($t);
            }

            $DataIN_VENTAS=$this->dataReturnIN_VENTAS($dataarray,$excelscompany->id);
            foreach (array_chunk($DataIN_VENTAS,1000) as $t)  
            {
                $RetornoIN_VENTAS=in_ventas::insert($t);
            }
            
            $DataCATEGORIZACION_CTAS_BALANCE=$this->dataReturnCATEGORIZACION_CTAS_BALANCE($dataarray,$excelscompany->id);
            foreach (array_chunk($DataCATEGORIZACION_CTAS_BALANCE,1000) as $t)  
            {
                $RetornoCATEGORIZACION_CTAS_BALANCE=categorizacion_cts_balance::insert($t);
            }
          
            $DataIN_BALANCE=$this->dataReturnIN_BALANCE($dataarray,$excelscompany->id);
            foreach (array_chunk($DataIN_BALANCE,1000) as $t)  
            {
                $RetornoIN_BALANCE=in_balance::insert($t);
            }
     
            $cuentasResultados = DB::select('SELECT DISTINCT in_resultados.cuenta_master as cuenta, "Resultado" as tipo,in_resultados.id_excel FROM in_resultados LEFT JOIN clasificacion_cuenta_resuls ON clasificacion_cuenta_resuls.cuenta=in_resultados.cuenta_master AND clasificacion_cuenta_resuls.id_excel= in_resultados.id_excel WHERE clasificacion_cuenta_resuls.cuenta is null  AND in_resultados.id_excel=  ?',[$excelscompany->id]);         
            info( $this->valorCargaControlCuentas($cuentasResultados));
            foreach (array_chunk($this->valorCargaControlCuentas($cuentasResultados),1000) as $t)  
            {
                $Retornocontrolcuentas=controlcuentas::insert($t);
            }
            $cuentasPresupuesto = DB::select('SELECT DISTINCT in_presupuestos.cuenta_master as cuenta, "Presupuesto" as tipo,in_presupuestos.id_excel FROM in_presupuestos LEFT JOIN clasificacion_cuenta_resuls ON clasificacion_cuenta_resuls.cuenta=in_presupuestos.cuenta_master AND clasificacion_cuenta_resuls.id_excel= in_presupuestos.id_excel WHERE clasificacion_cuenta_resuls.cuenta is null  AND in_presupuestos.id_excel=  ?',[$excelscompany->id]);
            foreach (array_chunk($this->valorCargaControlCuentas($cuentasPresupuesto),1000) as $t)  
            {
                $Retornocontrolcuentas=controlcuentas::insert($t);
            }
            $cuentasBalances = DB::select('SELECT DISTINCT in_balances.cuenta_master as cuenta,"Balance" as tipo ,in_balances.id_excel FROM in_balances  LEFT JOIN categorizacion_cts_balances ON categorizacion_cts_balances.cuenta=in_balances.cuenta_master AND in_balances.id_excel=categorizacion_cts_balances.id_excel WHERE categorizacion_cts_balances.cuenta is null AND in_balances.id_excel = ?',[$excelscompany->id]);
            foreach (array_chunk($this->valorCargaControlCuentas($cuentasBalances),1000) as $t)  
            {
                $Retornocontrolcuentas=controlcuentas::insert($t);
            }
            $cuentasVentas = DB::select('SELECT DISTINCT in_ventas.codigo_analisis as cuenta, "Ventas" as tipo  ,in_ventas.id_excel FROM in_ventas LEFt JOIN setup_analises ON setup_analises.id_excel=in_ventas.id_excel AND in_ventas.codigo_analisis=setup_analises.codigo WHERE setup_analises.codigo is null AND  in_ventas.id_excel = ?',[$excelscompany->id]);
            foreach (array_chunk($this->valorCargaControlCuentas($cuentasVentas),1000) as $t)  
            {
                $Retornocontrolcuentas=controlcuentas::insert($t);
            }
            Flash::success('Datos Agregado Correctamente');
            Storage::delete($path);
            return route('excelscompanies.index');

            } catch (ValidationException $e) {
                Flash::error('Excels Error Database');
                // Validation failed, return validation errors
                Log::error('Validation failed: ' . $e);
                // Rollback the transaction
                DB::rollBack();

                return route('excelscompanies.index');
            } catch (\Exception $e) {
                info($e);
                DB::rollBack();
                Flash::error('Excels Error Database'); 

                return route('excelscompanies.index');
            }
        });
    return $data;
    }
   
    public function dataReturnSucursales($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany){
            return [
                'nombre'=>$valor[0]==NULL?'':$valor[0],
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['SUCURSALES']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnsetup_ER($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'order'=>$valor[0]==NULL?'':$valor[0],
                'grupo'=>$valor[1]==NULL?'':$valor[1],
                'nivel_1'=>$valor[2]==NULL?'':$valor[2],
                'nivel_2'=>$valor[3]==NULL?'':$valor[3],
                'nivel_3'=>$valor[4]==NULL?'':$valor[4],
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['SETUP ER']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnCLASIFICACION_CUENTA_RESULTADOS($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'cuenta'=>$valor[0]==NULL?'':$valor[0],
                'nombre'=>$valor[1]==NULL?'':$valor[1],
                'origen'=>$valor[2]==NULL?'':$valor[2],
                'grupo'=>$valor[3]==NULL?'':$valor[3],
                'nivel_1'=>$valor[4]==NULL?'':$valor[4],
                'nivel_2'=>$valor[5]==NULL?'':$valor[5],
                'nivel_3'=>$valor[6]==NULL?'':$valor[6],
                'clasificacion_ratios_financ'=>$valor[7]==NULL?'':$valor[7],
                'clasificacion_punto_equilibrio'=>$valor[8]==NULL?'':$valor[8],
                'clasificacion_cuenta_juridica_legal'=>$valor[9]==NULL?'':$valor[9],
                'clasificacion_ebit_ebitda'=>$valor[10]==NULL?'':$valor[10],
                'clasificacion_er'=>$valor[11]==NULL?'':$valor[11],
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['CLASIFICACION CUENTAS RESULTADO']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnIN_RESULTADOS($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'mes'=>$valor[0]==NULL?'':$valor[0],
                'ano'=>$valor[1]==NULL?'':$valor[1],
                'sucursal'=>$valor[2]==NULL?'':$valor[2],
                'cuenta_master'=>$valor[3]==NULL?'':$valor[3],
                'monto_uyu'=>$valor[5]==NULL?'':floatval(str_replace(',','',$valor[5])),
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['IN RESULTADOS']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnIN_PRESUPUESTOS($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'mes'=>$valor[0]==NULL?'':$valor[0],
                'ano'=>$valor[1]==NULL?'':$valor[1],
                'sucursal'=>$valor[2]==NULL?'':$valor[2],
                'cuenta_master'=>$valor[3]==NULL?'':$valor[3],
                'monto_uyu'=>$valor[5]==NULL?'':str_replace(',','',$valor[5]),
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['IN PRESUPUESTOS']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnTIPO_DE_CAMBIO($dataarray,$idExcelCompany){       
         $func = function($valor) use ($idExcelCompany) {
         $fecha=is_numeric($valor[0])?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor[0])->format('Y-m-d'):$valor[0];
        return [
            'fecha'=>date('Y-m-d H:i:s', strtotime($fecha==NULL?'':$fecha)),
            'mes'=>date('n', strtotime($fecha==NULL?'':$fecha)),
            'ano'=>date('Y', strtotime($fecha==NULL?'':$fecha)),
            'dolar_compra'=>$valor[1]==NULL?'1':str_replace(',','',$valor[1]),
            'dolar_venta'=>$valor[2]==NULL?'1':str_replace(',','',$valor[2]),
            'dolar_promedio'=>$valor[3]==NULL?'1':str_replace(',','',$valor[3]),
            'euro_promedio'=>$valor[4]==NULL?'1':str_replace(',','',$valor[4]),
            'francosuizo_promedio'=>$valor[5]==NULL?'1':str_replace(',','',$valor[5]),
            'ui'=>$valor[6]==NULL?'1':str_replace(',','',$valor[6]),
            'ipc'=>$valor[7]==NULL?'1':str_replace(',','',$valor[7]),
            'ipc_empresa'=>$valor[8]==NULL?'1':str_replace(',','',$valor[8]),
            'id_excel'=>$idExcelCompany
        ];
    };
    $dataRetorno=array_map($func, $dataarray['TIPO DE CAMBIO']);
    array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnSETUP_ANALISIS($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'codigo'=>$valor[0]==NULL?'':$valor[0],
                'nombre'=>$valor[1]==NULL?'':$valor[1],
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['SETUP ANALISIS']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnIN_VENTAS($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'mes'=>$valor[0]==NULL?'':$valor[0],
                'ano'=>$valor[1]==NULL?'':$valor[1],
                'sucursal'=>$valor[2]==NULL?'':$valor[2],
                'codigo_analisis'=>$valor[3]==NULL?'':$valor[3],
                'cantidad_venta_unidades'=>$valor[5]==NULL?'':str_replace(',','',$valor[5]),
                'cantidad_costo'=>$valor[6]==NULL?'':str_replace(',','',$valor[6]),
                'cantidad_margen'=>$valor[7]==NULL?'':str_replace(',','',$valor[7]),
                'ventas_uyu'=>$valor[8]==NULL?'':str_replace(',','',$valor[8]),
                'ganancia_bruta_uyu'=>$valor[9]==NULL?'':str_replace(',','',$valor[9]),
                'ventas_uyu_prom'=>$valor[10]==NULL?'':str_replace(',','',$valor[10]),
                'ganancia_bruta_uyu_prom'=>$valor[11]==NULL?'':str_replace(',','',$valor[11]),
                'costo_uyu'=>$valor[12]==NULL?'':str_replace(',','',$valor[12]),
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['IN VENTAS']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnCATEGORIZACION_CTAS_BALANCE($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'cuenta'=>$valor[0]==NULL?'':$valor[0],
                'nombre'=>$valor[1]==NULL?'':$valor[1],
                'origen'=>$valor[2]==NULL?'':$valor[2],
                'nivel_1'=>$valor[3]==NULL?'':$valor[3],
                'nivel_2'=>$valor[4]==NULL?'':$valor[4],
                'nivel_3'=>$valor[5]==NULL?'':$valor[5],
                'nivel_4'=>$valor[6]==NULL?'':$valor[6],
                'posicion_moneda'=>$valor[7]==NULL?'':$valor[7],
                'posicion_fiscal'=>$valor[8]==NULL?'':$valor[8],
                'posicion_socios'=>$valor[9]==NULL?'':$valor[9],
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['CATEGORIZACION CTAS BALANCE']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function valorCargaControlCuentas($dataarray){
        $func = function($valor)  {
            return [
                'cuenta'=>$valor->cuenta,
                'tipo'=>$valor->tipo,
                'id_excel'=>$valor->id_excel
            ];
        };
        $dataRetorno=array_map($func, $dataarray);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    public function dataReturnIN_BALANCE($dataarray,$idExcelCompany){
        $func = function($valor) use ($idExcelCompany) {
            return [
                'mes'=>$valor[0]==NULL?'':$valor[0],
                'ano'=>$valor[1]==NULL?'':$valor[1],
                'sucursal'=>$valor[2]==NULL?'':$valor[2],
                'cuenta_master'=>$valor[3]==NULL?'':$valor[3],
                'saldo_uyu'=>$valor[5]==NULL?'':str_replace(',','',$valor[5]),
                'id_excel'=>$idExcelCompany
            ];
        };
        $dataRetorno=array_map($func, $dataarray['IN BALANCE']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
   
    /**
     * Store a newly created excelscompany in storage.
     */
    public function store(CreateexcelscompanyRequest $request)
    {
        return redirect($this->uploadfile($request,"stored"));
    }

    /**
     * Display the specified excelscompany.
     */
    public function show($id)
    {
        $excelscompany = $this->excelscompanyRepository->find($id);

        if (empty($excelscompany)) {
            Flash::error('Excelscompany not found');

            return redirect(route('excelscompanies.index'));
        }

        return view('excelscompanies.show')->with('excelscompany', $excelscompany);
    }

    /**
     * Show the form for editing the specified excelscompany.
     */
    public function edit($id)
    {
        $excelscompany = $this->excelscompanyRepository->find($id);

        if (empty($excelscompany)) {
            Flash::error('Excelscompany not found');

            return redirect(route('excelscompanies.index'));
        }
       
        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        return view('excelscompanies.edit')->with('excelscompany', $excelscompany)->with("company",$companies);;
    }

    /**
     * Update the specified excelscompany in storage.
     */
    public function update($id, UpdateexcelscompanyRequest $request)
    {
        $excelscompany = $this->excelscompanyRepository->find($id);

        if (empty($excelscompany)) {
            Flash::error('Excelscompany not found');

            return redirect(route('excelscompanies.index'));
        }

        $excelscompany = $this->excelscompanyRepository->update($request->all(), $id);

        Flash::success('Excelscompany updated successfully.');

        return redirect(route('excelscompanies.index'));
    }
    public function DeleteAllByID($idExcelCompany){
        $SucursalesDelete=sucursales::where('id_excel',$idExcelCompany)->delete();
        $clasificacion_cuenta_resulDelete=clasificacion_cuenta_resul::where('id_excel',$idExcelCompany)->delete();
        $in_resultadoDelete=in_resultado::where('id_excel',$idExcelCompany)->delete();
        $in_presupuestosDelete=in_presupuestos::where('id_excel',$idExcelCompany)->delete();
        $tipo_cambioDelete=tipo_cambio::where('id_excel',$idExcelCompany)->delete();
        $setup_analisisDelete=setup_analisis::where('id_excel',$idExcelCompany)->delete();
        $in_ventasDelete=in_ventas::where('id_excel',$idExcelCompany)->delete();
        $categorizacion_cts_balanceDelete=categorizacion_cts_balance::where('id_excel',$idExcelCompany)->delete();
        $in_balanceDelete=in_balance::where('id_excel',$idExcelCompany)->delete();
        $controldecuentas=controlcuentas::where('id_excel',$idExcelCompany)->delete();

    }
    public function DeleteAllByIDCompany($idExcelCompany){
        $SucursalesDelete=sucursales::join('excelscompanies','excelscompanies.id','sucursales.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $clasificacion_cuenta_resulDelete=clasificacion_cuenta_resul::join('excelscompanies','excelscompanies.id','clasificacion_cuenta_resuls.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $in_resultadoDelete=in_resultado::join('excelscompanies','excelscompanies.id','in_resultados.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $in_presupuestosDelete=in_presupuestos::join('excelscompanies','excelscompanies.id','in_presupuestos.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $tipo_cambioDelete=tipo_cambio::join('excelscompanies','excelscompanies.id','tipo_cambios.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $setup_analisisDelete=setup_analisis::join('excelscompanies','excelscompanies.id','setup_analises.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $in_ventasDelete=in_ventas::join('excelscompanies','excelscompanies.id','in_ventas.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $categorizacion_cts_balanceDelete=categorizacion_cts_balance::join('excelscompanies','excelscompanies.id','categorizacion_cts_balances.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $in_balanceDelete=in_balance::join('excelscompanies','excelscompanies.id','in_balances.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
        $excelCompanye=excelscompany::where('id_company',$idExcelCompany)->delete();
        $controldecuentas=controlcuentas::join('excelscompanies','excelscompanies.id','controlcuentas.id_excel')->where('excelscompanies.id_company',$idExcelCompany)->delete();
    }
    /**
     * Remove the specified excelscompany from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $excelscompany = $this->excelscompanyRepository->find($id);

        if (empty($excelscompany)) {
            Flash::error('Excelscompany not found');

            return redirect(route('excelscompanies.index'));
        }

        $this->excelscompanyRepository->delete($id);
        $this->DeleteAllByID($id);
        Flash::success('Datos Subidos Eliminados.');

        return redirect(route('excelscompanies.index'));
    }
}
