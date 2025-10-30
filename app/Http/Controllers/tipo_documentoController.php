<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createtipo_documentoRequest;
use App\Http\Requests\Updatetipo_documentoRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\tipo_documentoRepository;
use Illuminate\Http\Request;
use Flash;

class tipo_documentoController extends AppBaseController
{
    /** @var tipo_documentoRepository $tipoDocumentoRepository*/
    private $tipoDocumentoRepository;

    public function __construct(tipo_documentoRepository $tipoDocumentoRepo)
    {
        $this->tipoDocumentoRepository = $tipoDocumentoRepo;
    }

    /**
     * Display a listing of the tipo_documento.
     */
    public function index(Request $request)
    {
        $tipoDocumentos = $this->tipoDocumentoRepository->paginate(10);

        return view('tipo_documentos.index')
            ->with('tipoDocumentos', $tipoDocumentos);
    }

    /**
     * Show the form for creating a new tipo_documento.
     */
    public function create()
    {
        return view('tipo_documentos.create');
    }

    /**
     * Store a newly created tipo_documento in storage.
     */
    public function store(Createtipo_documentoRequest $request)
    {
        $input = $request->all();
        $input['id_estudio']=auth()->user()->getIdEstudios();
        $tipoDocumento = $this->tipoDocumentoRepository->create($input);

        Flash::success('Tipo Documento saved successfully.');

        return redirect(route('tipo_documentos.index'));
    }

    /**
     * Display the specified tipo_documento.
     */
    public function show($id)
    {
        $tipoDocumento = $this->tipoDocumentoRepository->find($id);

        if (empty($tipoDocumento)) {
            Flash::error('Tipo Documento not found');

            return redirect(route('tipo_documentos.index'));
        }

        return view('tipo_documentos.show')->with('tipoDocumento', $tipoDocumento);
    }

    /**
     * Show the form for editing the specified tipo_documento.
     */
    public function edit($id)
    {
        $tipoDocumento = $this->tipoDocumentoRepository->find($id);

        if (empty($tipoDocumento)) {
            Flash::error('Tipo Documento not found');

            return redirect(route('tipo_documentos.index'));
        }

        return view('tipo_documentos.edit')->with('tipoDocumento', $tipoDocumento);
    }

    /**
     * Update the specified tipo_documento in storage.
     */
    public function update($id, Updatetipo_documentoRequest $request)
    {
        $tipoDocumento = $this->tipoDocumentoRepository->find($id);

        if (empty($tipoDocumento)) {
            Flash::error('Tipo Documento not found');

            return redirect(route('tipo_documentos.index'));
        }

        $input = $request->all();
        $input['id_estudio']=auth()->user()->getIdEstudios();
        $tipoDocumento = $this->tipoDocumentoRepository->update($input, $id);

        Flash::success('Tipo Documento updated successfully.');

        return redirect(route('tipo_documentos.index'));
    }

    /**
     * Remove the specified tipo_documento from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tipoDocumento = $this->tipoDocumentoRepository->find($id);

        if (empty($tipoDocumento)) {
            Flash::error('Tipo Documento not found');

            return redirect(route('tipo_documentos.index'));
        }

        $this->tipoDocumentoRepository->delete($id);

        Flash::success('Tipo Documento deleted successfully.');

        return redirect(route('tipo_documentos.index'));
    }
}
