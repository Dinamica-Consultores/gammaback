<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createbitacora_hitosRequest;
use App\Http\Requests\Updatebitacora_hitosRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\bitacora_hitosRepository;
use App\Models\bitacora_hitos;
use Illuminate\Http\Request;
use Flash;

class bitacora_hitosController extends AppBaseController
{
    /** @var bitacora_hitosRepository $bitacoraHitosRepository*/
    private $bitacoraHitosRepository;

    public function __construct(bitacora_hitosRepository $bitacoraHitosRepo)
    {
        $this->bitacoraHitosRepository = $bitacoraHitosRepo;
    }

    /**
     * Display a listing of the bitacora_hitos.
     */
    public function index(Request $request)
    {
        $bitacoraHitos = $this->bitacoraHitosRepository->paginate(10);

        return view('bitacora_hitos.index')
            ->with('bitacoraHitos', $bitacoraHitos);
    }

    /**
     * Show the form for creating a new bitacora_hitos.
     */
    public function create()
    {
        return view('bitacora_hitos.create');
    }
    public function create2($id,$id2)
    {
        return view('bitacora_hitos.create')->with('id',$id)->with('id2',$id2);
    }
    /**
     * Store a newly created bitacora_hitos in storage.
     */
    public function store($id,$id2,Createbitacora_hitosRequest $request)
    {
        $input = $request->all();
        $input['id_bitacora']=$id2;
        $input['numero']=bitacora_hitos::where('id_bitacora',$id2)->count()+1;
        $bitacoraHitos = $this->bitacoraHitosRepository->create($input);

        Flash::success('Hito Creado.');

        return redirect(route('bitacoras.show',[$id,$id2]));
    }

    /**
     * Display the specified bitacora_hitos.
     */
    public function show($id,$id2,$id3)
    {
        $bitacoraHitos = $this->bitacoraHitosRepository->find($id3);

        if (empty($bitacoraHitos)) {
            Flash::error('Bitacora Hitos not found');

            return redirect(route('bitacoras.show',[$id,$id2]));
        }

        return view('bitacora_hitos.show')->with('bitacoraHitos', $bitacoraHitos)->with('id',$id)->with('id2',$id2);
    }

    /**
     * Show the form for editing the specified bitacora_hitos.
     */
    public function edit($id,$id2,$id3)
    {
        $bitacoraHitos = $this->bitacoraHitosRepository->find($id3);

        if (empty($bitacoraHitos)) {
            Flash::error('Bitacora Hitos not found');

            return redirect(route('bitacoras.show',[$id,$id2]));
        }

        return view('bitacora_hitos.edit')->with('bitacoraHitos', $bitacoraHitos)->with('id',$id)->with('id2',$id2);
    }

    /**
     * Update the specified bitacora_hitos in storage.
     */
    public function update($id,$id2,$id3, Updatebitacora_hitosRequest $request)
    {
        $input=$request->all();
        $bitacoraHitos = $this->bitacoraHitosRepository->find($id3);
        $input['id_bitacora']=$id2;
        $input['numero']=$bitacoraHitos->numero;
        if (empty($bitacoraHitos)) {
            Flash::error('Bitacora Hitos not found');

            return redirect(route('bitacoras.show',[$id,$id2]));
        }

        $bitacoraHitos = $this->bitacoraHitosRepository->update($input, $id3);

        Flash::success('Hitos Actualizados');

        return redirect(route('bitacoras.show',[$id,$id2]));
    }

    /**
     * Remove the specified bitacora_hitos from storage.
     *
     * @throws \Exception
     */
    public function destroy($id,$id2,$id3)
    {
        $bitacoraHitos = $this->bitacoraHitosRepository->find($id3);
        
        if (empty($bitacoraHitos)) {
            Flash::error('Bitacora Hitos not found');

            return redirect(route('bitacoras.show',[$id,$id2]));
        }

        $this->bitacoraHitosRepository->delete($id3);
        $getAllHitos=bitacora_hitos::where('id_bitacora',$id2)->orderByRaw('numero Asc')->get();

        if(count($getAllHitos)>0){
            collect($getAllHitos)->map(function($data,$key) {
                $valor['titulo']= $data->titulo;
                $valor['numero']=$key+1;
                $valor['id_bitacora']=$data->id_bitacora;
                $valor['description']=$data->description;
                $this->bitacoraHitosRepository->update($valor, $data->id);
            });
        }
     
        Flash::success('Hito eliminado.');

        return redirect(route('bitacoras.show',[$id,$id2]));
    }
}
