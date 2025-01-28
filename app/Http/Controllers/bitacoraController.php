<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatebitacoraRequest;
use App\Http\Requests\UpdatebitacoraRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\bitacoraRepository;
use App\Models\grupo_economicos;
use App\Models\bitacora;
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

    /**
     * Show the form for creating a new bitacora.
     */
    public function create()
    {
        $grupoEconomico=grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios())->pluck('grupo_economicos.nombre','grupo_economicos.id');
        
        return view('bitacoras.create')->with('grupoEconomico',$grupoEconomico);
    }

    /**
     * Store a newly created bitacora in storage.
     */
    public function store(CreatebitacoraRequest $request)
    {
        $input = $request->all();
        $input['id_estudio']=auth()->user()->getIdEstudios();
        $bitacora = $this->bitacoraRepository->create($input);

        Flash::success('Bitacora Guardada.');

        return redirect(route('bitacoras.index'));
    }

    /**
     * Display the specified bitacora.
     */
    public function show($id)
    {
        $bitacora = $this->bitacoraRepository->find($id);

        if (empty($bitacora)) {
            Flash::error('Bitacora  No Encontrada');

            return redirect(route('bitacoras.index'));
        }

        return view('bitacoras.show')->with('bitacora', $bitacora);
    }

    /**
     * Show the form for editing the specified bitacora.
     */
    public function edit($id)
    {
        $bitacora = $this->bitacoraRepository->find($id);
        $grupoEconomico=grupo_economicos::where('id_estudio',auth()->user()->getIdEstudios())->pluck('grupo_economicos.nombre','grupo_economicos.id');
      
        if (empty($bitacora)) {
            Flash::error('Bitacora No Encontrada');

            return redirect(route('bitacoras.index'));
        }

        return view('bitacoras.edit')->with('grupoEconomico',$grupoEconomico)->with('bitacora', $bitacora);
    }

    /**
     * Update the specified bitacora in storage.
     */
    public function update($id, UpdatebitacoraRequest $request)
    {
        $input = $request->all();
        $bitacora = $this->bitacoraRepository->find($id);
        
        if (empty($bitacora)) {
            Flash::error('Bitacora  No Encontrada');

            return redirect(route('bitacoras.index'));
        }

        $bitacora = $this->bitacoraRepository->update( $input, $id);

        Flash::success('Bitacora Actualizada.');

        return redirect(route('bitacoras.index'));
    }

    /**
     * Remove the specified bitacora from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $bitacora = $this->bitacoraRepository->find($id);

        if (empty($bitacora)) {
            Flash::error('Bitacora No Encontrada');

            return redirect(route('bitacoras.index'));
        }

        $this->bitacoraRepository->delete($id);

        Flash::success('Bitacora Eliminada.');

        return redirect(route('bitacoras.index'));
    }
}
