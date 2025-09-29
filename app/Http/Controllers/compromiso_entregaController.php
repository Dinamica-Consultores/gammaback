<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createcompromiso_entregaRequest;
use App\Http\Requests\Updatecompromiso_entregaRequest;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use App\Models\company;
use App\Models\compromiso_entrega;
use App\Repositories\compromiso_entregaRepository;
use Illuminate\Http\Request;

use App\Models\User;
use Flash;

class compromiso_entregaController extends AppBaseController
{
    /** @var compromiso_entregaRepository $compromisoEntregaRepository*/
    private $compromisoEntregaRepository;

    public function __construct(compromiso_entregaRepository $compromisoEntregaRepo)
    {
        $this->compromisoEntregaRepository = $compromisoEntregaRepo;
    }

    /**
     * Display a listing of the compromiso_entrega.
     */
    public function index(Request $request)
    {
        $compromisoEntregas = compromiso_entrega::SELECT('compromiso_entregas.*')->join('companies','companies.id','compromiso_entregas.id_company')->where('companies.id_estudio',auth()->user()->getIdEstudios())->paginate(10);

        return view('compromiso_entregas.index')->with('compromisoEntregas', $compromisoEntregas);
    }

    /**
     * Show the form for creating a new compromiso_entrega.
     */
    public function create()
    {
        
        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        $usuariosdata = User::Select('users.*');
        $usuariosdata=$usuariosdata->join('estudios_usuarios','estudios_usuarios.id_users','users.id');
        if(auth()->user()->level_user==0){

            $usuariosdata=$usuariosdata->where('users.level_user','>=',0);  
        }else{
            
        $usuariosdata=$usuariosdata->where('users.level_user','>',0);  
        }      
        $usuariosdata=$usuariosdata->where('estudios_usuarios.id_estudios','=',auth()->user()->getIdEstudios()); 
        $usuariosdata=$usuariosdata->pluck('email', 'id');
        return view('compromiso_entregas.create')->with('company',$companies)->with('usuariosdata',$usuariosdata);
    }

    /**
     * Store a newly created compromiso_entrega in storage.
     */
    public function store(Createcompromiso_entregaRequest $request)
    {
        $input = $request->all();
        
$formattedDate = date('Y-m-d H:i:s', strtotime($input['fecha_entrega']));
$input['fecha_entrega']=$formattedDate;
$formattedDate2 = date('Y-m-d H:i:s', strtotime($input['fecha_reunion']));
$input['fecha_reunion']=$formattedDate2;
        $compromisoEntrega = $this->compromisoEntregaRepository->create($input);

        Flash::success('Compromiso Entrega saved successfully.');

        return redirect(route('compromiso_entregas.index'));
    }

    /**
     * Display the specified compromiso_entrega.
     */
    public function show($id)
    {
        $compromisoEntrega = $this->compromisoEntregaRepository->find($id);

        if (empty($compromisoEntrega)) {
            Flash::error('Compromiso Entrega  no Encontrado');

            return redirect(route('compromiso_entregas.index'));
        }

        return view('compromiso_entregas.show')->with('compromisoEntrega', $compromisoEntrega);
    }

    /**
     * Show the form for editing the specified compromiso_entrega.
     */
    public function edit($id)
    {
        $compromisoEntrega = $this->compromisoEntregaRepository->find($id);

        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        $usuariosdata = User::Select('users.*');
        $usuariosdata=$usuariosdata->join('estudios_usuarios','estudios_usuarios.id_users','users.id');
        if(auth()->user()->level_user==0){

            $usuariosdata=$usuariosdata->where('users.level_user','>=',0);  
        }else{
            
        $usuariosdata=$usuariosdata->where('users.level_user','>',0);  
        }      
        $usuariosdata=$usuariosdata->where('estudios_usuarios.id_estudios','=',auth()->user()->getIdEstudios()); 
        $usuariosdata=$usuariosdata->pluck('email', 'id');
        if (empty($compromisoEntrega)) {
            Flash::error('Compromiso Entrega  no Encontrado');

            return redirect(route('compromiso_entregas.index'));
        }
        if($compromisoEntrega)
        return view('compromiso_entregas.edit')->with('compromisoEntrega', $compromisoEntrega)->with('company',$companies)->with('usuariosdata',$usuariosdata);
    }

    /**
     * Update the specified compromiso_entrega in storage.
     */
    public function update($id, Updatecompromiso_entregaRequest $request)
    {
        $compromisoEntrega = $this->compromisoEntregaRepository->find($id);

        if (empty($compromisoEntrega)) {
            Flash::error('Compromiso Entrega  no Encontrado');

            return redirect(route('compromiso_entregas.index'));
        }
        $input=$request->all();
    
        
$formattedDate = date('Y-m-d H:i:s', strtotime($input['fecha_entrega']));
$input['fecha_entrega']=$formattedDate;
$formattedDate2 = date('Y-m-d H:i:s', strtotime($input['fecha_reunion']));
$input['fecha_reunion']=$formattedDate2;

        $compromisoEntrega = $this->compromisoEntregaRepository->update($input, $id);

        Flash::success('Compromiso Entrega updated successfully.');

        return redirect(route('compromiso_entregas.index'));
    }
    public function getAllCompromisoEntregar(Request $request)
    {
        $companyId = $request->input('company_id');
        $company = company::find($companyId);
        if (!$company) {
            return response()->json(['error' => 'Compañia no encontrada'], 404);
        }
        $userId = $request->input('user_id');
        $usuario = User::find($userId);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $compromisoPorUsuario = compromiso_entrega::SELECT('compromiso_entregas.*')->where('id_company',$companyId)->whereNull('fecha_entregado')
        ->with('user')
        ->with('company');
        if($usuario->level_user>0){
            $compromisoPorUsuario = $compromisoPorUsuario->where('usuario',$userId);
        }
        $compromisoPorUsuario = $compromisoPorUsuario->orderBy('fecha_entrega', 'asc')->first();
        
        
        // Devolver la respuesta en formato JSON
        return response()->json($compromisoPorUsuario);
    }

    public function procesar(Request $request)
    {
        // El valor se recupera usando el nombre de la clave enviado ('mi_valor')
        $id_usuario = $request->input('id_usuario');
        $usuario = User::find($id_usuario);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $id_compromiso = $request->input('id_compromiso');
        $compromiso = compromiso_entrega::find($id_compromiso);
        if (!$compromiso) {
            return response()->json(['error' => 'Compromiso no Encontrado'], 404);
        }
        $compromisotexto = $request->input('compromiso');
        $compromiso->fecha_entregado =  now();
        $compromiso->usuario_entregado =  $id_usuario;
        
        $compromiso->descripcion_entregado =  $compromisotexto;
        $compromiso->save();
        return response()->json([
            'mensaje' => 'Registrado',
            'valor_procesado' => $compromiso
        ]);
    }
    /**
     * Remove the specified compromiso_entrega from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $compromisoEntrega = $this->compromisoEntregaRepository->find($id);

        if (empty($compromisoEntrega)) {
            Flash::error('Compromiso Entrega  no Encontrado');

            return redirect(route('compromiso_entregas.index'));
        }

        $this->compromisoEntregaRepository->delete($id);

        Flash::success('Compromiso Entrega deleted successfully.');

        return redirect(route('compromiso_entregas.index'));
    }
}
