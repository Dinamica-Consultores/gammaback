<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatesessionesRequest;
use App\Http\Requests\UpdatesessionesRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\sessionesRepository;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\sessiones;
use Flash;

class sessionesController extends AppBaseController
{
    /** @var sessionesRepository $sessionesRepository*/
    private $sessionesRepository;

    public function __construct(sessionesRepository $sessionesRepo)
    {
        $this->sessionesRepository = $sessionesRepo;
    }

    /**
     * Display a listing of the sessiones.
     */
    public function index(Request $request)
    {
        if(auth()->user()->level_user==0){
            $users = User::Select('users.*');
            $users=$users->where('users.level_user','=',1);
            $users=$users->orWhere('users.level_user','=',0);
            if(auth()->user()->getIdEstudios()>0){
                $usuarioConcatenar=User::Select('users.*');
                $usuarioConcatenar=$usuarioConcatenar->join('estudios_usuarios','estudios_usuarios.id_users','users.id');
                $usuarioConcatenar=$usuarioConcatenar->where('users.level_user','>',1);        
                $usuarioConcatenar=$usuarioConcatenar->where('estudios_usuarios.id_estudios','=',auth()->user()->getIdEstudios());  
                $users=$users->union($usuarioConcatenar);
            }
        }else if(auth()->user()->level_user==1){
            $users = User::Select('users.*');
            $users=$users->join('estudios_usuarios','estudios_usuarios.id_users','users.id');
            $users=$users->where('users.level_user','>',0);        
            $users=$users->where('estudios_usuarios.id_estudios','=',auth()->user()->getIdEstudios());    
        }else if(auth()->user()->level_user==2){
            $users = User::Select('users.*');
            $users=$users->join('estudios_usuarios','estudios_usuarios.id_users','users.id');
            $users=$users->where('users.level_user','>',1); 
            $users=$users->where('estudios_usuarios.id_estudios','=',auth()->user()->getIdEstudios());          
        }else if(auth()->user()->level_user==3){
            $users = User::Select('users.*');
            $users=$users->join('estudios_usuarios','estudios_usuarios.id_users','users.id');
            $users=$users->where('users.level_user','>',1); 
            $users=$users->where('estudios_usuarios.id_estudios','=',auth()->user()->getIdEstudios());   
            $users=$users->where('users.id','=',auth()->user()->id);       
        }
        
        $users= $users->pluck('email', 'id');
        $sesion=sessiones::SELECT('sessiones.opcion','sessiones.created_at','users.email')->join('users','users.id','sessiones.user_id')->join('estudios_usuarios','estudios_usuarios.id_users','users.id')->where('estudios_usuarios.id_estudios',auth()->user()->getIdEstudios());
        $datosWhere='';
        $arrayWhere=array();
        if(isset($_GET['query']) && !empty($_GET['query'])){
            $datosWhere.="users.id = ? ";
            array_push($arrayWhere,$_GET['query']);
        }
        if(isset($_GET['query1']) && !empty($_GET['query1'])){
           if(!empty($datosWhere)){
            $datosWhere.="AND ";
           }
           $datosWhere.="sessiones.opcion Like ?";
           array_push($arrayWhere,'%'.$_GET['query1'].'%');
        }
        if(isset($_GET['query3']) && !empty($_GET['query3'])){
            if(!empty($datosWhere)){
             $datosWhere.=" AND ";
            }
            $datosWhere.=" DATE_FORMAT(sessiones.created_at, '%m/%d/%Y') >= ?";
            array_push($arrayWhere,$_GET['query3']);
         }
         if(isset($_GET['query4']) && !empty($_GET['query4'])){
            if(!empty($datosWhere)){
             $datosWhere.=" AND ";
            }
            $datosWhere.=" DATE_FORMAT(sessiones.created_at, '%m/%d/%Y') <= ?";
            array_push($arrayWhere,$_GET['query4']);
         }
        if(!empty($datosWhere)){

            $sesion=$sesion->whereRaw($datosWhere,$arrayWhere);
        }
        $sesion= $sesion->paginate(10);
        $sesion->appends($request->all());

        return view('sessiones.index')
            ->with('sessiones', $sesion)->with('users',$users);
    }

    /**
     * Show the form for creating a new sessiones.
     */
    public function create()
    {
        return view('sessiones.create');
    }

    /**
     * Store a newly created sessiones in storage.
     */
    public function store(CreatesessionesRequest $request)
    {
        $input = $request->all();

        $sessiones = $this->sessionesRepository->create($input);

        Flash::success('Sessiones saved successfully.');

        return redirect(route('sessiones.index'));
    }

    /**
     * Display the specified sessiones.
     */
    public function show($id)
    {
        $sessiones = $this->sessionesRepository->find($id);

        if (empty($sessiones)) {
            Flash::error('Sessiones not found');

            return redirect(route('sessiones.index'));
        }

        return view('sessiones.show')->with('sessiones', $sessiones);
    }

    /**
     * Show the form for editing the specified sessiones.
     */
    public function edit($id)
    {
        $sessiones = $this->sessionesRepository->find($id);

        if (empty($sessiones)) {
            Flash::error('Sessiones not found');

            return redirect(route('sessiones.index'));
        }

        return view('sessiones.edit')->with('sessiones', $sessiones);
    }

    /**
     * Update the specified sessiones in storage.
     */
    public function update($id, UpdatesessionesRequest $request)
    {
        $sessiones = $this->sessionesRepository->find($id);

        if (empty($sessiones)) {
            Flash::error('Sessiones not found');

            return redirect(route('sessiones.index'));
        }

        $sessiones = $this->sessionesRepository->update($request->all(), $id);

        Flash::success('Sessiones updated successfully.');

        return redirect(route('sessiones.index'));
    }

    /**
     * Remove the specified sessiones from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $sessiones = $this->sessionesRepository->find($id);

        if (empty($sessiones)) {
            Flash::error('Sessiones not found');

            return redirect(route('sessiones.index'));
        }

        $this->sessionesRepository->delete($id);

        Flash::success('Sessiones deleted successfully.');

        return redirect(route('sessiones.index'));
    }
}
