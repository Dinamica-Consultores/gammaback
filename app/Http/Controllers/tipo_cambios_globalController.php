<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createtipo_cambios_globalRequest;
use App\Http\Requests\Updatetipo_cambios_globalRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\tipo_cambios_globalRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\ExcelInTasas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Excel;
use App\Models\tipo_cambios_global;
use Flash;

class tipo_cambios_globalController extends AppBaseController
{
    /** @var tipo_cambios_globalRepository $tipoCambiosGlobalRepository*/
    private $tipoCambiosGlobalRepository;

    public function __construct(tipo_cambios_globalRepository $tipoCambiosGlobalRepo)
    {
        $this->tipoCambiosGlobalRepository = $tipoCambiosGlobalRepo;
    }

    /**
     * Display a listing of the tipo_cambios_global.
     */
    public function index(Request $request)
    {
        $tipo_cambios_globals = tipo_cambios_global::where('id_estudio',auth()->user()->getIdEstudios())->orderByRaw('fecha desc')->paginate(10);

        return view('tipo_cambios_globals.index')
            ->with('tipoCambiosGlobals', $tipo_cambios_globals);
    }

    /**
     * Show the form for creating a new tipo_cambios_global.
     */
    public function create()
    {
        return view('tipo_cambios_globals.create');
    }

    /**
     * Store a newly created tipo_cambios_global in storage.
     */
    public function store(Createtipo_cambios_globalRequest $request)
    {
        $input = $request->all();
        $input['id_estudio']=auth()->user()->getIdEstudios();
        $fecha = Carbon::parse( $input['fecha']);

        $input['mes']=$fecha->format('m');
        $input['ano']=$fecha->format('Y');

        $existeFecha=tipo_cambios_global::where('mes',$input['mes'])->where('ano',$input['ano'])->get();
        if(count($existeFecha)>0){
        Flash::error('Tienes la Fecha ya registrada.');

        return redirect(route('tipo_cambios_globals.index'));

        }
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->create($input);
        Flash::success('Tipo Cambios Global saved successfully.');

        return redirect(route('tipo_cambios_globals.index'));
    }

    /**
     * Display the specified tipo_cambios_global.
     */
    public function show($id)
    {
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->find($id);

        if (empty($tipoCambiosGlobal)) {
            Flash::error('Tipo Cambios Global no encontrado');

            return redirect(route('tipo_cambios_globals.index'));
        }

        return view('tipo_cambios_globals.show')->with('tipoCambiosGlobal', $tipoCambiosGlobal);
    }

    /**
     * Show the form for editing the specified tipo_cambios_global.
     */
    public function edit($id)
    {
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->find($id);

        if (empty($tipoCambiosGlobal)) {
            Flash::error('Tipo Cambios Global no encontrado');

            return redirect(route('tipo_cambios_globals.index'));
        }

        return view('tipo_cambios_globals.edit')->with('tipoCambiosGlobal', $tipoCambiosGlobal);
    }
    public function uploadfile(Request $request){
      
        $import = new ExcelInTasas;
        $input = $request->all();   
        if($request->hasFile('file')){
            $path = $request->file('file')->store('public/file');
            $path2 = str_replace('public/', '', $path);
            $input['path']=$path2;
        }else{
            Flash::error('No tienes el archivo excel.');
            return route('tipo_cambios_globals.index');
        }
        $array  = Excel::toArray($import, $request->file('file'));
    
        return redirect($this->RegisterInfo($array,$input,$path));
    }   
      public function RegisterInfo($dataarray,$request,$path){
        
        $data= DB::transaction(function () use ($request,$dataarray,$path) {
            try {
            $input=$request;
            $tipo_cambios_globalde=tipo_cambios_global::where('id_estudio',auth()->user()->getIdEstudios())->delete();
            $datoExcel=$this->dataRegistroExcel( $dataarray,auth()->user()->getIdEstudios());
            foreach (array_chunk($datoExcel,1000) as $t)  
            {
                $RetornodatoExcel=tipo_cambios_global::insert($t);
            }
            Flash::success('Datos Agregado Correctamente');
            Storage::delete($path);
            return route('tipo_cambios_globals.index');

            } catch (ValidationException $e) {
                Flash::error('Excels Error Database');
                // Validation failed, return validation errors
                Log::error('Validation failed: ' . $e);
                // Rollback the transaction
                DB::rollBack();

                return route('tipo_cambios_globals.index');
            } catch (\Exception $e) {
                info($e);
                DB::rollBack();
                Flash::error('Excels Error Database'); 

                return route('tipo_cambios_globals.index');
            }
        });
    return $data;
    }
    public function dataRegistroExcel($dataarray,$idEstudio){
        $func = function($valor) use ($idEstudio){
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
            'id_estudio'=>$idEstudio
            ];
        };
        $dataRetorno=array_map($func, $dataarray['TASAS']);
        array_shift($dataRetorno);
        return $dataRetorno;
    }
    /**
     * Update the specified tipo_cambios_global in storage.
     */
    public function update($id, Updatetipo_cambios_globalRequest $request)
    {
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->find($id);

        if (empty($tipoCambiosGlobal)) {
            Flash::error('Tipo Cambios Global no encontrado');

            return redirect(route('tipo_cambios_globals.index'));
        }
        $input=$request->all();
          $input['id_estudio']=auth()->user()->getIdEstudios();
        $fecha = Carbon::parse( $input['fecha']);

        $input['mes']=$fecha->format('m');
        $input['ano']=$fecha->format('Y');

        $existeFecha=tipo_cambios_global::where('mes',$input['mes'])->where('ano',$input['ano'])->where('id','<>',$id)->get();
        if(count($existeFecha)>0){
        Flash::error('Tienes la Fecha ya registrada.');

        return redirect(route('tipo_cambios_globals.index'));

        }
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->update($input, $id);

        Flash::success('Tipo Cambios Global Actualizado Correctamente.');

        return redirect(route('tipo_cambios_globals.index'));
    }

    /**
     * Remove the specified tipo_cambios_global from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tipoCambiosGlobal = $this->tipoCambiosGlobalRepository->find($id);

        if (empty($tipoCambiosGlobal)) {
            Flash::error('Tipo Cambios Global no encontrado');

            return redirect(route('tipo_cambios_globals.index'));
        }

        $this->tipoCambiosGlobalRepository->delete($id);

        Flash::success('Tipo Cambios Global Borrados.');

        return redirect(route('tipo_cambios_globals.index'));
    }
}
