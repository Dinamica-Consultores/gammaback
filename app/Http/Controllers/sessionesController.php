<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatesessionesRequest;
use App\Http\Requests\UpdatesessionesRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\sessionesRepository;
use Illuminate\Http\Request;
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
        $sesion=sessiones::SELECT('sessiones.opcion','sessiones.created_at','users.email')->join('users','users.id','sessiones.user_id')->join('estudios_usuarios','estudios_usuarios.id_users','users.id')->where('estudios_usuarios.id_estudios',auth()->user()->getIdEstudios());
        $datosWhere='';
        $arrayWhere=array();
        if(isset($_GET['query']) && !empty($_GET['query'])){
            $datosWhere.="users.email Like ? ";
            array_push($arrayWhere,'%'.$_GET['query'].'%');
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
            $datosWhere.=" DATE_FORMAT(sessiones.created_at, '%m/%d/%Y') = ?";
            array_push($arrayWhere,$_GET['query3']);
         }
        if(!empty($datosWhere)){

            $sesion=$sesion->whereRaw($datosWhere,$arrayWhere);
        }
        $sesion= $sesion->paginate(10);
        $sesion->appends($request->all());

        return view('sessiones.index')
            ->with('sessiones', $sesion);
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
