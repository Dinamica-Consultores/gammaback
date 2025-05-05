<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createbitacora_enviosemailRequest;
use App\Http\Requests\Updatebitacora_enviosemailRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\bitacora_enviosemailRepository;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\bitacora_enviosemail;
use Flash;

class bitacora_enviosemailController extends AppBaseController
{
    /** @var bitacora_enviosemailRepository $bitacoraEnviosemailRepository*/
    private $bitacoraEnviosemailRepository;

    public function __construct(bitacora_enviosemailRepository $bitacoraEnviosemailRepo)
    {
        $this->bitacoraEnviosemailRepository = $bitacoraEnviosemailRepo;
    }

    /**
     * Display a listing of the bitacora_enviosemail.
     */
    public function index(Request $request)
    {
        $bitacoraEnviosemails = $this->bitacoraEnviosemailRepository->paginate(10);

        return view('bitacora_enviosemails.index')
            ->with('bitacoraEnviosemails', $bitacoraEnviosemails);
    }

    /**
     * Show the form for creating a new bitacora_enviosemail.
     */
    public function create($id,$id2)
    {
      
        $TodosLosUsuarios=User::SELECT('users.id','users.email')->join('bitacora_enviosemails','bitacora_enviosemails.id_user','users.id')->distinct()->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_users','users.id');
        $TodosLosUsuarios=$TodosLosUsuarios->where('bitacora_enviosemails.id_bitacora',$id2)->get();
        $users = User::Select('users.id','users.email');
        $users= $users->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_users','users.id');
        $users= $users->where('usuario_grupoeconomicos.id_grupoeconomico',$id);
        $users=$users->get();
        $id_user=array();
        $infoEdit= array();
        $infoEdit['id_user']= $TodosLosUsuarios;
        return view('bitacora_enviosemails.create')->with('infoEdit', $infoEdit)->with('users',$users)->with('id',$id)->with('id_user',$id_user)->with('id2',$id2);
    }

    /**
     * Store a newly created bitacora_enviosemail in storage.
     */
    public function store($id,$id2,Createbitacora_enviosemailRequest $request)
    {
        $input = $request->all();
        if(!isset($input['id_user'])){
            Flash::error('Es necesario selecionar un Usuario.');
            return back()->withInput();
        }
        bitacora_enviosemail::where('id_bitacora',$id2)->delete();
        $info = collect($input['id_user'])->map(function($data) use ($id2){
            return [
                'id_user'=>$data,
                'id_bitacora'=>$id2
            ];
        })->reject(function ( $name) {
            return [];
        });
        $data=bitacora_enviosemail::insert( $info->toArray());

        Flash::success('Usuarios Registrados');

        return redirect(route('bitacoras.show',[$id,$id2]));
    }

    /**
     * Display the specified bitacora_enviosemail.
     */
    public function show($id)
    {
        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->find($id);

        if (empty($bitacoraEnviosemail)) {
            Flash::error('Bitacora Enviosemail not found');

            return redirect(route('bitacoraEnviosemails.index'));
        }

        return view('bitacora_enviosemails.show')->with('bitacoraEnviosemail', $bitacoraEnviosemail);
    }

    /**
     * Show the form for editing the specified bitacora_enviosemail.
     */
    public function edit($id)
    {
        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->find($id);

        if (empty($bitacoraEnviosemail)) {
            Flash::error('Bitacora Enviosemail not found');

            return redirect(route('bitacoraEnviosemails.index'));
        }

        return view('bitacora_enviosemails.edit')->with('bitacoraEnviosemail', $bitacoraEnviosemail);
    }

    /**
     * Update the specified bitacora_enviosemail in storage.
     */
    public function update($id, Updatebitacora_enviosemailRequest $request)
    {
        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->find($id);

        if (empty($bitacoraEnviosemail)) {
            Flash::error('Bitacora Enviosemail not found');

            return redirect(route('bitacoraEnviosemails.index'));
        }

        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->update($request->all(), $id);

        Flash::success('Bitacora Enviosemail updated successfully.');

        return redirect(route('bitacoraEnviosemails.index'));
    }

    /**
     * Remove the specified bitacora_enviosemail from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->find($id);

        if (empty($bitacoraEnviosemail)) {
            Flash::error('Bitacora Enviosemail not found');

            return redirect(route('bitacoraEnviosemails.index'));
        }

        $this->bitacoraEnviosemailRepository->delete($id);

        Flash::success('Bitacora Enviosemail deleted successfully.');

        return redirect(route('bitacoraEnviosemails.index'));
    }
}
