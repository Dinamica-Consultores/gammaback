<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createusuario_grupoeconomicoRequest;
use App\Http\Requests\Updateusuario_grupoeconomicoRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\usuario_grupoeconomicoRepository;
use App\Models\usuario_grupoeconomico;
use App\Models\grupo_economicos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\User;

use App\Models\bitacora_enviosemail;
use Flash;

class usuario_grupoeconomicoController extends AppBaseController
{
    /** @var usuario_grupoeconomicoRepository $usuarioGrupoeconomicoRepository*/
    private $usuarioGrupoeconomicoRepository;

    public function __construct(usuario_grupoeconomicoRepository $usuarioGrupoeconomicoRepo)
    {
        $this->usuarioGrupoeconomicoRepository = $usuarioGrupoeconomicoRepo;
    }

    /**
     * Display a listing of the usuario_grupoeconomico.
     */
    public function index(Request $request)
    {
        $usuarioGrupoeconomicos = User::SELECT(DB::raw('users.id,CONCAT(users.name," ",users.surname) as name,users.email,COUNT(usuario_grupoeconomicos.id) as Cantidad'))->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_users','users.id')->join('grupo_economicos','grupo_economicos.id','usuario_grupoeconomicos.id_grupoeconomico')
        ->where('grupo_economicos.id_estudio',auth()->user()->getIdEstudios());
        if(isset($_GET['query'])){
            $usuarioGrupoeconomicos=$usuarioGrupoeconomicos->whereRaw('CONCAT(name," ",surname) Like ?',array('%'.$_GET['query'].'%'));
           
        }
        $usuarioGrupoeconomicos= $usuarioGrupoeconomicos->groupByRaw('users.email,users.id,users.surname,users.name');
        $usuarioGrupoeconomicos= $usuarioGrupoeconomicos->paginate(10);
        $usuarioGrupoeconomicos->appends($request->all());
        return view('usuario_grupoeconomicos.index')
            ->with('usuarioGrupoeconomicos', $usuarioGrupoeconomicos);
    }

    /**
     * Show the form for creating a new usuario_grupoeconomico.
     */
    public function create()
    {
        $grupoEconomico=grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios())->join('grupo_economicos_empresas','grupo_economicos_empresas.id_grupoeconomico','grupo_economicos.id')->distinct()->pluck('grupo_economicos.nombre','grupo_economicos.id');
        $Usuarios = User::select(
        'users.id',
        DB::raw("CONCAT(users.name, ' ', users.surname, ' (', users.email, ')') as label_completo")
    )
    ->leftJoin('usuario_grupoeconomicos', 'usuario_grupoeconomicos.id_users', '=', 'users.id')
    ->join('estudios_usuarios', 'estudios_usuarios.id_users', '=', 'users.id')
    ->where('estudios_usuarios.id_estudios', auth()->user()->getIdEstudios())
    ->whereNull('usuario_grupoeconomicos.id')
    ->distinct() // Recomendado para evitar duplicados por los joins
    ->pluck('label_completo', 'id');
     return view('usuario_grupoeconomicos.create')->with('grupoEconomico',$grupoEconomico)->with('usuarios',$Usuarios);
    }

    /**
     * Store a newly created usuario_grupoeconomico in storage.
     */
    public function store(Createusuario_grupoeconomicoRequest $request)
    {
        $input = $request->all();

        if(!isset($input['id_users'])){

            Flash::error('Es necesario selecionar un Usuario.');
            return back()->withInput();
        }
        if(!isset($input['id_grupoeconomico'])){

            Flash::error('Es necesario selecionar un Grupo Economico.');
            return back()->withInput();
        }
        $info = collect($input['id_grupoeconomico'])->map(function($data) use ($input){
            return [
                'id_grupoeconomico'=>$data,
                'id_users'=>$input['id_users']
            ];
        })->reject(function ( $name) {
            return [];
        });
        $data=usuario_grupoeconomico::insert( $info->toArray());
        Flash::success('Usuarios y Red Comercial ');
        return redirect(route('usuario_grupoeconomicos.index'));
    }

    /**
     * Display the specified usuario_grupoeconomico.
     */
    public function show($id)
    {
        $usuarioGrupoeconomico = $this->usuarioGrupoeconomicoRepository->find($id);

        if (empty($usuarioGrupoeconomico)) {
            Flash::error('Usuario Grupoeconomico not found');

            return redirect(route('usuarioGrupoeconomicos.index'));
        }

        return view('usuario_grupoeconomicos.show')->with('usuarioGrupoeconomico', $usuarioGrupoeconomico);
    }

    /**
     * Show the form for editing the specified usuario_grupoeconomico.
     */
    public function edit($id)
    {
        $todoslasCredenciales=usuario_grupoeconomico::SELECT('id_grupoeconomico')->where('id_users',$id)->get();
        $email=User::find($id);
        $nuevoid_company=[];
        foreach ($todoslasCredenciales as &$valor) {
            $nuevoid_company[]=$valor->id_grupoeconomico;
        }
        $infoEdit=[
            'id_users'=>$email->id,
            'email'=>$email->email,
            'grupoEconomico'=>$nuevoid_company
        ];
        $grupoEconomico=grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios())->join('grupo_economicos_empresas','grupo_economicos_empresas.id_grupoeconomico','grupo_economicos.id')->distinct()->pluck('grupo_economicos.nombre','grupo_economicos.id');
        
        if (empty($nuevoid_company)) {
            Flash::error('Usuario y Red Comercial no econtrada');

            return redirect(route('usuario_grupoeconomicos.index'));
        }

        return view('usuario_grupoeconomicos.edit')->with('grupoEconomico', $grupoEconomico)->with('infoEdit',$infoEdit);
    }

    /**
     * Update the specified usuario_grupoeconomico in storage.
     */
    public function update($id, Updateusuario_grupoeconomicoRequest $request)
    {
        $input = $request->all();
        if(!isset($input['id_users'])){

            Flash::error('Es necesario selecionar un Usuario.');
            return back()->withInput();
        }
        if(!isset($input['id_grupoeconomico'])){

            Flash::error('Es necesario selecionar una Red Comercial.');
            return back()->withInput();
        }
        $info = collect($input['id_grupoeconomico'])->map(function($data) use ($input){
            return [
                'id_grupoeconomico'=>$data,
                'id_users'=>$input['id_users']
            ];
        })->reject(function ( $name) {
            return [];
        });

        usuario_grupoeconomico::where('id_users',$id)->delete();
        $data=usuario_grupoeconomico::insert( $info->toArray());

        bitacora_enviosemail::join('bitacoras','bitacoras.id','bitacora_enviosemails.id_bitacora')  ->leftjoin('usuario_grupoeconomicos', function($joins)
        {
            $joins->on('usuario_grupoeconomicos.id_users','=','bitacora_enviosemails.id_user')
            ->on('usuario_grupoeconomicos.id_grupoeconomico','=','bitacoras.id_grupoeconomico');
        })-> where('bitacora_enviosemails.id_user',$id)->delete();
        Flash::success('Usuario y Red Comercial Actualizado');

        return redirect(route('usuario_grupoeconomicos.index'));
    }

    /**
     * Remove the specified usuario_grupoeconomico from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
      
        usuario_grupoeconomico::where('id_users',$id)->delete();
        bitacora_enviosemail::join('bitacoras','bitacoras.id','bitacora_enviosemails.id_bitacora')  ->leftjoin('usuario_grupoeconomicos', function($joins)
        {
            $joins->on('usuario_grupoeconomicos.id_users','=','bitacora_enviosemails.id_user')
            ->on('usuario_grupoeconomicos.id_grupoeconomico','=','bitacoras.id_grupoeconomico');
        })-> where('bitacora_enviosemails.id_user',$id)->delete();
        Flash::success('Usuario y Red Comercial Eliminado.');

        return redirect(route('usuario_grupoeconomicos.index'));
    }
}
