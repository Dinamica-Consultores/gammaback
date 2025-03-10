<?php

namespace App\Http\Controllers;

use App\Http\Requests\Creategrupo_economicosRequest;
use App\Http\Requests\Updategrupo_economicosRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\grupo_economicos_empresas;
use App\Models\grupo_economicos;
use App\Models\usuario_grupoeconomico;
use App\Repositories\grupo_economicosRepository;
use Illuminate\Http\Request;
use Flash;

class grupo_economicosController extends AppBaseController
{
    /** @var grupo_economicosRepository $grupoEconomicosRepository*/
    private $grupoEconomicosRepository;

    public function __construct(grupo_economicosRepository $grupoEconomicosRepo)
    {
        $this->grupoEconomicosRepository = $grupoEconomicosRepo;
    }

    /**
     * Display a listing of the grupo_economicos.
     */
    public function index(Request $request)
    {
        $grupoEconomicos = grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios());
        if(isset($_GET['query'])){
            $grupoEconomicos=$grupoEconomicos->whereRaw('CONCAT(nombre) Like ?',array('%'.$_GET['query'].'%'));
           
        }
        $grupoEconomicos= $grupoEconomicos->paginate(10);
        $grupoEconomicos->appends($request->all());
        return view('grupo_economicos.index')
            ->with('grupoEconomicos', $grupoEconomicos);
    }

    /**
     * Show the form for creating a new grupo_economicos.
     */
    public function create()
    {
        return view('grupo_economicos.create');
    }

    /**
     * Store a newly created grupo_economicos in storage.
     */
    public function store(Creategrupo_economicosRequest $request)
    {
        $input = $request->all();
        $idEstudios=auth()->user()->getIdEstudios();
        $input['id_estudio']=$idEstudios;
        $grupoEconomicos = $this->grupoEconomicosRepository->create($input);

        Flash::success('Red Comercial Guardados.');

        return redirect(route('grupo_economicos.index'));
    }

    /**
     * Display the specified grupo_economicos.
     */
    public function show($id)
    {
        $grupoEconomicos = $this->grupoEconomicosRepository->find($id);

        if (empty($grupoEconomicos)) {
            Flash::error('Red Comercial No encontrado');

            return redirect(route('grupo_economicos.index'));
        }

        return view('grupo_economicos.show')->with('grupoEconomicos', $grupoEconomicos);
    }

    /**
     * Show the form for editing the specified grupo_economicos.
     */
    public function edit($id)
    {
        $grupoEconomicos = $this->grupoEconomicosRepository->find($id);

        if (empty($grupoEconomicos)) {
            Flash::error('Red Comercial No encontrado');

            return redirect(route('grupo_economicos.index'));
        }

        return view('grupo_economicos.edit')->with('grupoEconomicos', $grupoEconomicos);
    }

    /**
     * Update the specified grupo_economicos in storage.
     */
    public function update($id, Updategrupo_economicosRequest $request)
    {
        $grupoEconomicos = $this->grupoEconomicosRepository->find($id);
        $input=$request->all();
        if (empty($grupoEconomicos)) {
            Flash::error('Red Comercial No encontrado');

            return redirect(route('grupo_economicos.index'));
        }
        $cantidad=grupo_economicos_empresas::join('companies','companies.id','=','grupo_economicos_empresas.id_company')->where('grupo_economicos_empresas.id_grupoeconomico',$id)->where('companies.id_moneda','<>',$input['id_moneda'])->get();
        if(count($cantidad)>0){
            
            Flash::error('Tienes Compañias registradas con una moneda que no debe estar');

            return redirect(route('grupo_economicos.index'));
        }
        $grupoEconomicos = $this->grupoEconomicosRepository->update( $input, $id);

        Flash::success('Red Comercial Actualizado.');

        return redirect(route('grupo_economicos.index'));
    }

    /**
     * Remove the specified grupo_economicos from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $grupoEconomicos = $this->grupoEconomicosRepository->find($id);

        if (empty($grupoEconomicos)) {
            Flash::error('Red Comercial No encontrado');

            return redirect(route('grupo_economicos.index'));
        }

        $this->grupoEconomicosRepository->delete($id);
        grupo_economicos_empresas::where('id_grupoeconomico',$id)->delete();
        usuario_grupoeconomico::where('id_grupoeconomico',$id)->delete();
        Flash::success('Red Comercial Borrado.');

        return redirect(route('grupo_economicos.index'));
    }
}
