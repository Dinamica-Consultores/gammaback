<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatebitacoraRequest;
use App\Http\Requests\UpdatebitacoraRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\bitacoraRepository;
use App\Models\grupo_economicos;
use App\Models\bitacora;
use App\Models\User;
use App\Models\bitacora_hitos;
use App\Models\bitacora_enviosemail;
use Mail;
use Illuminate\Http\Request;
use Flash;

class bitacoraController extends AppBaseController
{
    /** @var bitacoraRepository $bitacoraRepository*/
    private $bitacoraRepository;

    public function __construct(bitacoraRepository $bitacoraRepo)
    {
        $this->bitacoraRepository = $bitacoraRepo;
    }

    /**
     * Display a listing of the bitacora.
     */
    public function index(Request $request)
    {
        $bitacoras = bitacora::where('id_estudio',auth()->user()->getIdEstudios())->paginate(10);

        return view('bitacoras.index')
            ->with('bitacoras', $bitacoras);
    }
    public function index2($id,Request $request)
    {
        $bitacoras = bitacora::where('id_estudio',auth()->user()->getIdEstudios())->where('id_grupoeconomico',$id)->paginate(10);

        return view('bitacoras.index')
            ->with('bitacoras', $bitacoras)->with('id',$id);
    }
    
    /**
     * Show the form for creating a new bitacora.
     */
    public function create2($id2)
    {
        $grupoEconomico=grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios())->pluck('grupo_economicos.nombre','grupo_economicos.id');
        
        return view('bitacoras.create')->with('grupoEconomico',$grupoEconomico)->with('id2',$id2);
    }

    /**
     * Store a newly created bitacora in storage.
     */
    public function store($id2,CreatebitacoraRequest $request)
    {
        $input = $request->all();
        $input['id_grupoeconomico']=$id2;
        $input['id_estudio']=auth()->user()->getIdEstudios();
        $bitacora = $this->bitacoraRepository->create($input);

        Flash::success('Bitacora Guardada.');

        return redirect(route('bitacoras.index2',$id2));
    }

    /**
     * Display the specified bitacora.
     */
    public function show($id2,$id)
    {
        $bitacora = $this->bitacoraRepository->find($id);
        $allHitos=bitacora_hitos::where('id_bitacora',$bitacora->id)->orderByRaw('numero Asc')->paginate(10);
        if (empty($bitacora)) {
            Flash::error('Bitacora  No Encontrada');

            return redirect(route('bitacoras.index'));
        }

        return view('bitacoras.show')->with('bitacora', $bitacora)->with('id2',$id2)->with('allHitos',$allHitos);
    }
    public function EnviosEmail($id,$id2){
        $bitacora=bitacora::where('id',$id2)->first();
        $grupoEconomico=grupo_economicos::where('id',$id)->first();
        if(isset($bitacora)){
            $bitacoraHitos=bitacora_hitos::where('id_bitacora',$id2)->orderByRaw('numero Asc')->get();
            $bitacoraCorreos=User::join('bitacora_enviosemails','bitacora_enviosemails.id_user','users.id')->where('bitacora_enviosemails.id_bitacora',$id2)->get();
            $datoEnvio=array();
            $datoEmails=array();
            if(count($bitacoraHitos)>0){
                $datoEnvio=collect($bitacoraHitos)->map(function($data,$key) {
                    return [
                        'numero'=>$data->numero,
                        'titulo'=>$data->titulo,
                        'descripcion'=>$data->description
                    ];
                })->toArray();
            }else{
                Flash::error('Es necesario tener un hito.');
                return back()->withInput();
            }
            if(count($bitacoraCorreos)>0){
                $datoEmails=collect($bitacoraCorreos)->map(function($data,$key) {
                    return $data->email;
                })->toArray();
            }else{
                Flash::error('Es necesario selecionar un Usuario.');
                return back()->withInput();
            }
            $dato['titulo']= $bitacora->descripcion;
            $dato['nombre']= $grupoEconomico->nombre;
            $responsable= $bitacora->persona_agrega;
            $dato['hitos']=$datoEnvio;
            info($dato);
            Mail::send(['html' => 'bitacoras.emailbitacoras'], ['dato'=>$dato], function($message) use ($datoEmails,$responsable) {
                $message->to( $datoEmails)->subject('Hitos Enviado por '.$responsable);
                $message->from(env('MAIL_USERNAME') , env('MAIL_FROM_NAME'));
            });
            
        Flash::success('Enviado de Hitos.');

        return redirect(route('bitacoras.index2',$id));
        }else{
            
            Flash::error('Es necesario selecionar una bitacora.');
            return back()->withInput();
        }
        
    }

    /**
     * Show the form for editing the specified bitacora.
     */
    public function edit($id2,$id)
    {
        $bitacora = $this->bitacoraRepository->find($id);
        $grupoEconomico=grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios())->pluck('grupo_economicos.nombre','grupo_economicos.id');
      
        if (empty($bitacora)) {
            Flash::error('Bitacora No Encontrada');

            return redirect(route('bitacoras.index2',$id2));
        }

        return view('bitacoras.edit')->with('grupoEconomico',$grupoEconomico)->with('bitacora', $bitacora)->with('id2',$id2);
    }

    /**
     * Update the specified bitacora in storage.
     */
    public function update($id2,$id, UpdatebitacoraRequest $request)
    {
        $input = $request->all();
        $bitacora = $this->bitacoraRepository->find($id);
        $input['id_grupoeconomico']=$id2;
        if (empty($bitacora)) {
            Flash::error('Bitacora  No Encontrada');

            return redirect(route('bitacoras.index2',$id2));
        }

        $bitacora = $this->bitacoraRepository->update( $input, $id);

        Flash::success('Bitacora Actualizada.');

        return redirect(route('bitacoras.index2',$id2));
    }

    /**
     * Remove the specified bitacora from storage.
     *
     * @throws \Exception
     */
    public function destroy($id,$id2)
    {
        $bitacora = $this->bitacoraRepository->find($id2);

        if (empty($bitacora)) {
            Flash::error('Bitacora No Encontrada');

            return redirect(route('bitacoras.index2',$id));
        }

        $this->bitacoraRepository->delete($id2);

        Flash::success('Bitacora Eliminada.');

        return redirect(route('bitacoras.index2',$id));
    }
}
