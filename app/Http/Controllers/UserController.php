<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Models\estudios_usuarios;
use App\Models\usuario_grupoeconomico;
use App\Models\User;
use Mail;
use Illuminate\Support\Facades\Hash;
use Flash;

class UserController extends AppBaseController
{
    /** @var UserRepository $userRepository*/
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
       
    }

    /**
     * Display a listing of the User.
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
        $users= $users->paginate(10);
        return view('users.index')
            ->with('users', $users);
    }

    /**
     * Show the form for creating a new User.
     */
    function getInfoNiveles($level){
        $niveles=[];
        if($level===0){
            
            $niveles=['0'=>'Administrador','1'=>'Estudio'];
            if(auth()->user()->getIdEstudios()>0){
                $niveles=['0'=>'Administrador','1'=>'Estudio','2'=>'Gestion','3'=>'Final'];
            }
        }else if($level===1){
            $niveles=['2'=>'Gestion','3'=>'Final'];
        }else if($level===2){
            $niveles=['2'=>'Gestion','3'=>'Final'];
        }
        return $niveles;
    }
    public function create()
    {
        $niveles=$this->getInfoNiveles(auth()->user()->level_user);
        return view('users.create')->with('niveles',$niveles);
    }

    /**
     * Store a newly created User in storage.
     */
    public function InsertarInformacionUser($id_user,$level){
        if($level>1){
            $getInf=estudios_usuarios::where('id_users',auth()->user()->id)->get();
            if(count($getInf)>0){
                estudios_usuarios::create(['id_users'=>$id_user,'id_estudios'=>$getInf[0]->id_estudios]);
            }
        }
       
    }

    public function textMensaje($level){
        $texto="";
        if($level===0){
            $texto="Administrador";
        }
        if($level===1){
            $texto="Estudio";
        }
        if($level===2){
            $texto="Gestion";
        }
        if($level===0){
            $texto="Final";
        }
        return $texto;
    }
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $input['pass']=$input['password'];
        if(isset($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            Flash::error('Es necesario la contraseña.');
            return back()->withInput();
        }

        $user = $this->userRepository->create($input);
        if($user->id){
            $this->InsertarInformacionUser($user->id,$user->level_user);
        }
        Flash::success('Usuario Generado.');
        $dato['href']=env('APP_URL_CHANGEPASS');
        $dato['mensaje']=  $this->textMensaje($user->level_user);
        $dato['clave']=  $input['pass'];
        $dato['usuario']=  $user->email;
        $dato['nombre']=  $user->name." ".$user->surname;
        $dato['max_var']=  '5';
        $dato['min_var']=  '-5';
        Mail::send(['html' => 'users.mensaje'], ['dato'=>$dato], function($message)use ($input) {
            $message->to( $input['email'] ,'')->subject('Accede a Informe GAMMA Web desde su computadora');
            $message->from(env('MAIL_USERNAME') , env('MAIL_FROM_NAME'));
            $path = base_path() . '/public/Post-GE-Mockk.jpg';
            $message->attach($path , array(
                'as' => "gamma.jpg")
            );
        });
        return redirect(route('users.index'));
    }

    /**
     * Display the specified User.
     */
    public function show($id)
    {
        $niveles=$this->getInfoNiveles(auth()->user()->level_user);
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            Flash::error('Usuario No encontrado');

            return redirect(route('users.index'));
        }

        return view('users.show')->with('niveles',$niveles)->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     */
    public function edit($id)
    {
        $niveles=$this->getInfoNiveles(auth()->user()->level_user);
        $user = $this->userRepository->find($id);
        $user['password']='';
        if (empty($user)) {
            Flash::error('Useria no Encontrado');

            return redirect(route('users.index'));
        }

        return view('users.edit')->with('niveles',$niveles)->with('user', $user);
    }

    /**
     * Update the specified User in storage.
     */
    public function update($id, UpdateUserRequest $request)
    {
        $input = $request->all();
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('Usuario no encontrado');

            return redirect(route('users.index'));
        }
        if(isset($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input['password']=$user->password;
        }
        $user = $this->userRepository->update($input, $id);

        Flash::success('Usuario Actualizado.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('Usuario no encontrado');
            return redirect(route('users.index'));
        }
        $deleted = estudios_usuarios::where('id_users', $id)->delete();
        $deleted2 = usuario_grupoeconomico::where('id_users', $id)->delete();
        
        $this->userRepository->delete($id);

        Flash::success('Usuario Eliminado.');

        return redirect(route('users.index'));
    }
}
