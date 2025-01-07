<?php

namespace App\Http\Controllers;

use App\Http\Requests\Creategrupo_economicos_empresasRequest;
use App\Http\Requests\Updategrupo_economicos_empresasRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\grupo_economicos_empresas;
use App\Models\usuario_grupoeconomico;
use App\Repositories\grupo_economicos_empresasRepository;
use Illuminate\Http\Request;
use App\Models\company;
use App\Models\grupo_economicos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Flash;

class grupo_economicos_empresasController extends AppBaseController
{
    /** @var grupo_economicos_empresasRepository $grupoEconomicosEmpresasRepository*/
    private $grupoEconomicosEmpresasRepository;

    public function __construct(grupo_economicos_empresasRepository $grupoEconomicosEmpresasRepo)
    {
        $this->grupoEconomicosEmpresasRepository = $grupoEconomicosEmpresasRepo;
    }

    /**
     * Display a listing of the grupo_economicos_empresas.
     */
    public function index(Request $request)
    {
        $grupoEconomicosEmpresas = grupo_economicos_empresas::SELECT(DB::raw('grupo_economicos.id,grupo_economicos.nombre,COUNT(grupo_economicos.id) as Cantidad'))->join('grupo_economicos','grupo_economicos.id','grupo_economicos_empresas.id_grupoeconomico')
        ->where('grupo_economicos.id_estudio',auth()->user()->getIdEstudios())->groupByRaw('grupo_economicos.nombre,grupo_economicos.id')->paginate(10);
        return view('grupo_economicos_empresas.index')
            ->with('grupoEconomicosEmpresas', $grupoEconomicosEmpresas);
    }

    /**
     * Show the form for creating a new grupo_economicos_empresas.
     */
    public function create()
    {
        $grupoEconomico=grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios())->leftjoin('grupo_economicos_empresas','grupo_economicos_empresas.id_grupoeconomico','grupo_economicos.id')->whereNull('grupo_economicos_empresas.id')->pluck('grupo_economicos.nombre','grupo_economicos.id');
        $grupoEconomico2=grupo_economicos::SELECT('grupo_economicos.*')->where('id_estudio',auth()->user()->getIdEstudios())->leftjoin('grupo_economicos_empresas','grupo_economicos_empresas.id_grupoeconomico','grupo_economicos.id')->whereNull('grupo_economicos_empresas.id')->get();
        $company=company::where('id_estudio',auth()->user()->getIdEstudios())->get();
        return view('grupo_economicos_empresas.create')->with('grupoEconomico',$grupoEconomico)->with('grupoEconomico2',$grupoEconomico2)->with('company',$company);
    }

    /**
     * Store a newly created grupo_economicos_empresas in storage.
     */
    public function store(Creategrupo_economicos_empresasRequest $request)
    {
        $input = $request->all();

        if(!isset($input['id_company'])){

            Flash::error('Es necesario selecionar una compañia.');
            return back()->withInput();
        }
        if(!isset($input['id_grupoeconomico'])){

            Flash::error('Es necesario selecionar una Red Comercial.');
            return back()->withInput();
        }
        $info = collect($input['id_company'])->map(function($data) use ($input){
            return [
                'id_company'=>$data,
                'id_grupoeconomico'=>$input['id_grupoeconomico']
            ];
        })->reject(function ( $name) {
            return [];
        });
        $data=grupo_economicos_empresas::insert( $info->toArray());
        Flash::success('Red Comercial y Empresas Guardados.');
        return redirect(route('grupo_economicos_empresas.index'));
    }

    /**
     * Display the specified grupo_economicos_empresas.
     */
    public function show($id)
    {
        $grupoEconomicosEmpresas = $this->grupoEconomicosEmpresasRepository->find($id);

        if (empty($grupoEconomicosEmpresas)) {
            Flash::error('Red Comercial y Empresas no Encontrado');

            return redirect(route('grupo_economicos_empresas.index'));
        }

        return view('grupo_economicos_empresas.show')->with('grupoEconomicosEmpresas', $grupoEconomicosEmpresas);
    }

    /**
     * Show the form for editing the specified grupo_economicos_empresas.
     */
    public function edit($id)
    {
        $todoslasCredenciales=grupo_economicos_empresas::SELECT('id_company')->where('id_grupoeconomico',$id)->get();
        $email=grupo_economicos::find($id);
        $nuevoid_company=[];
        foreach ($todoslasCredenciales as &$valor) {
            $nuevoid_company[]=$valor->id_company;
        }
        $infoEdit=[
            'id_grupoeconomico'=>$email->id,
            'id_grupoeconomiconame'=>$email->nombre,
            'id_company'=>$nuevoid_company
        ];
        $company=company::where('id_estudio',auth()->user()->getIdEstudios())->get();
       
        if (empty($nuevoid_company)) {
            Flash::error('Red Comercial y Empresas no Encontrado');

            return redirect(route('grupo_economicos_empresas.index'));
        }
        return view('grupo_economicos_empresas.edit')->with('grupoEconomico2',array($email))->with('infoEdit', $infoEdit)->with('company',$company);;
    }

    /**
     * Update the specified grupo_economicos_empresas in storage.
     */
    public function update($id, Updategrupo_economicos_empresasRequest $request)
    {
        $input = $request->all();
        if(!isset($input['id_company'])){

            Flash::error('Es necesario selecionar una compañia.');
            return back()->withInput();
        }
        if(!isset($input['id_grupoeconomico'])){

            Flash::error('Es necesario selecionar una Red Comercial.');
            return back()->withInput();
        }
        $info = collect($input['id_company'])->map(function($data) use ($input){
            return [
                'id_company'=>$data,
                'id_grupoeconomico'=>$input['id_grupoeconomico']
            ];
        })->reject(function ( $name) {
            return [];
        });
        
        grupo_economicos_empresas::where('id_grupoeconomico',$id)->delete();
        $data=grupo_economicos_empresas::insert( $info->toArray());


        Flash::success('Red Comercial y Empresas Actualizado.');

        return redirect(route('grupo_economicos_empresas.index'));
    }

    /**
     * Remove the specified grupo_economicos_empresas from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {

        grupo_economicos_empresas::where('id_grupoeconomico',$id)->delete();
        usuario_grupoeconomico::where('id_grupoeconomico',$id)->delete();
        Flash::success('Red Comercial y Empresas Borrado.');

        return redirect(route('grupo_economicos_empresas.index'));
    }
}
