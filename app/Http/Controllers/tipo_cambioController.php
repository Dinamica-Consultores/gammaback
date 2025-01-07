<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createtipo_cambioRequest;
use App\Http\Requests\Updatetipo_cambioRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\tipo_cambioRepository;
use App\Models\excelscompany;
use App\Models\tipo_cambio;
use Illuminate\Http\Request;
use Flash;

class tipo_cambioController extends AppBaseController
{
    /** @var tipo_cambioRepository $tipoCambioRepository*/
    private $tipoCambioRepository;

    public function __construct(tipo_cambioRepository $tipoCambioRepo)
    {
        $this->tipoCambioRepository = $tipoCambioRepo;
    }

    /**
     * Display a listing of the tipo_cambio.
     */
    public function index(Request $request)
    {
        $tipoCambios = tipo_cambio::Select('tipo_cambios.*')
        ->join('excelscompanies','excelscompanies.id','tipo_cambios.id_excel')
      
->join('companies','companies.id','excelscompanies.id_company')
->where('companies.id_estudio',auth()->user()->getIdEstudios())
 ->paginate(10);
        return view('tipo_cambios.index')
            ->with('tipoCambios', $tipoCambios);
    }

    /**
     * Show the form for creating a new tipo_cambio.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
           
->join('companies','companies.id','excelscompanies.id_company')
->where('companies.id_estudio',auth()->user()->getIdEstudios())
 ->pluck('version', 'id');
        return view('tipo_cambios.create')->with('excels',$excels);
    }

    /**
     * Store a newly created tipo_cambio in storage.
     */
    public function store(Createtipo_cambioRequest $request)
    {
        $input = $request->all();
        $input['mes']="";
        $input['ano']="";
        if(isset($input['fecha'])){
            $input['ano']=date("Y",strtotime($input['fecha']));
            $input['mes']=date("n",strtotime($input['fecha']));
        }
        $tipoCambio = $this->tipoCambioRepository->create($input);

        Flash::success('Tipo Cambio Guardado.');

        return redirect(route('tipo_cambios.index'));
    }

    /**
     * Display the specified tipo_cambio.
     */
    public function show($id)
    {
        $tipoCambio = $this->tipoCambioRepository->find($id);

        if (empty($tipoCambio)) {
            Flash::error('Tipo Cambio No encontrado');

            return redirect(route('tipo_cambios.index'));
        }

        return view('tipo_cambios.show')->with('tipoCambio', $tipoCambio);
    }

    /**
     * Show the form for editing the specified tipo_cambio.
     */
    public function edit($id)
    {
        $tipoCambio = $this->tipoCambioRepository->find($id);

        if (empty($tipoCambio)) {
            Flash::error('Tipo Cambio No encontrado');

            return redirect(route('tipo_cambios.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
           
->join('companies','companies.id','excelscompanies.id_company')
->where('companies.id_estudio',auth()->user()->getIdEstudios())
 ->pluck('version', 'id');
        return view('tipo_cambios.edit')->with('tipoCambio', $tipoCambio)->with('excels',$excels);
    }

    /**
     * Update the specified tipo_cambio in storage.
     */
    public function update($id, Updatetipo_cambioRequest $request)
    {
        $tipoCambio = $this->tipoCambioRepository->find($id);
        $input=$request->all();
        $input['mes']=$tipoCambio->mes;
        $input['ano']=$tipoCambio->ano;
        if(isset($input['fecha'])){
            $input['ano']=date("Y",strtotime($input['fecha']));
            $input['mes']=date("n",strtotime($input['fecha']));
        }

        if (empty($tipoCambio)) {
            Flash::error('Tipo Cambio No encontrado');

            return redirect(route('tipo_cambios.index'));
        }

        $tipoCambio = $this->tipoCambioRepository->update($input, $id);

        Flash::success('Tipo Cambio Actualizado.');

        return redirect(route('tipo_cambios.index'));
    }

    /**
     * Remove the specified tipo_cambio from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tipoCambio = $this->tipoCambioRepository->find($id);

        if (empty($tipoCambio)) {
            Flash::error('Tipo Cambio No encontrado');

            return redirect(route('tipo_cambios.index'));
        }

        $this->tipoCambioRepository->delete($id);

        Flash::success('Tipo Cambio Eliminado.');

        return redirect(route('tipo_cambios.index'));
    }
}
