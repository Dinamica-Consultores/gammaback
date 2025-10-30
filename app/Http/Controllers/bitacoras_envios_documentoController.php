<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createbitacoras_envios_documentoRequest;
use App\Http\Requests\Updatebitacoras_envios_documentoRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\bitacoras_envios_documentoRepository;
use Illuminate\Http\Request;

use App\Models\bitacoras_envios_documento;
use App\Models\company;
use App\Models\tipo_documento;
use Flash;

class bitacoras_envios_documentoController extends AppBaseController
{
    /** @var bitacoras_envios_documentoRepository $bitacorasEnviosDocumentoRepository*/
    private $bitacorasEnviosDocumentoRepository;

    public function __construct(bitacoras_envios_documentoRepository $bitacorasEnviosDocumentoRepo)
    {
        $this->bitacorasEnviosDocumentoRepository = $bitacorasEnviosDocumentoRepo;
    }

    /**
     * Display a listing of the bitacoras_envios_documento.
     */
    public function index(Request $request)
    {
        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        $tipo_documentos = tipo_documento::Select('tipo_documentos.*')->where('tipo_documentos.id_estudio',auth()->user()->getIdEstudios())->pluck('nombre', 'id');
        $bitacorasEnviosDocumentos=bitacoras_envios_documento::SELECT('bitacoras_envios_documentos.*')
        ->join('documento_companias','bitacoras_envios_documentos.id_documento_companias','documento_companias.id')
        ->join('tipo_documentos','documento_companias.id_tipodocumento','tipo_documentos.id')
        ->where('tipo_documentos.id_estudio',auth()->user()->getIdEstudios());
        $datosWhere='';
        $arrayWhere=array();
        if(isset($_GET['query']) && !empty($_GET['query'])){
            $datosWhere.="documento_companias.id_compania = ? ";
            array_push($arrayWhere,$_GET['query']);
        }
        if(isset($_GET['query1']) && !empty($_GET['query1'])){
           if(!empty($datosWhere)){
            $datosWhere.="AND ";
           }
           $datosWhere.="documento_companias.id_tipodocumento = ?";
           array_push($arrayWhere,$_GET['query1']);
        }
        if(isset($_GET['query3']) && !empty($_GET['query3'])){
            if(!empty($datosWhere)){
             $datosWhere.=" AND ";
            }
            $datosWhere.=" DATE_FORMAT(documento_companias.fecha_de_vencimiento, '%d/%m/%Y') >= ?";
            array_push($arrayWhere,$_GET['query3']);
        }
        if(isset($_GET['query4']) && !empty($_GET['query4'])){
            if(!empty($datosWhere)){
             $datosWhere.=" AND ";
            }
            $datosWhere.=" DATE_FORMAT(documento_companias.fecha_de_vencimiento, '%d/%m/%Y') <= ?";
            array_push($arrayWhere,$_GET['query4']);
         }
        if(isset($_GET['query5']) && !empty($_GET['query5'])){
            if(!empty($datosWhere)){
             $datosWhere.=" AND ";
            }
            $datosWhere.=" DATE_FORMAT(bitacoras_envios_documentos.fecha_envio, '%d/%m/%Y') >= ?";
            array_push($arrayWhere,$_GET['query5']);
        }
        if(isset($_GET['query6']) && !empty($_GET['query6'])){
            if(!empty($datosWhere)){
             $datosWhere.=" AND ";
            }
            $datosWhere.=" DATE_FORMAT(bitacoras_envios_documentos.fecha_envio, '%d/%m/%Y') <= ?";
            array_push($arrayWhere,$_GET['query6']);
         }
        if(!empty($datosWhere)){

            $bitacorasEnviosDocumentos=$bitacorasEnviosDocumentos->whereRaw($datosWhere,$arrayWhere);
        }
        $bitacorasEnviosDocumentos= $bitacorasEnviosDocumentos->paginate(10);
        $bitacorasEnviosDocumentos->appends($request->all());

        return view('bitacoras_envios_documentos.index')
            ->with('bitacorasEnviosDocumentos', $bitacorasEnviosDocumentos)->with('companies',$companies)->with('tipo_documentos',$tipo_documentos);
    }

    /**
     * Show the form for creating a new bitacoras_envios_documento.
     */
    public function create()
    {
        return view('bitacoras_envios_documentos.create');
    }

    /**
     * Store a newly created bitacoras_envios_documento in storage.
     */
    public function store(Createbitacoras_envios_documentoRequest $request)
    {
        $input = $request->all();

        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->create($input);

        Flash::success('Bitacoras Envios Documento saved successfully.');

        return redirect(route('bitacorasEnviosDocumentos.index'));
    }

    /**
     * Display the specified bitacoras_envios_documento.
     */
    public function show($id)
    {
        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->find($id);

        if (empty($bitacorasEnviosDocumento)) {
            Flash::error('Bitacoras Envios Documento not found');

            return redirect(route('bitacorasEnviosDocumentos.index'));
        }

        return view('bitacoras_envios_documentos.show')->with('bitacorasEnviosDocumento', $bitacorasEnviosDocumento);
    }

    /**
     * Show the form for editing the specified bitacoras_envios_documento.
     */
    public function edit($id)
    {
        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->find($id);

        if (empty($bitacorasEnviosDocumento)) {
            Flash::error('Bitacoras Envios Documento not found');

            return redirect(route('bitacorasEnviosDocumentos.index'));
        }

        return view('bitacoras_envios_documentos.edit')->with('bitacorasEnviosDocumento', $bitacorasEnviosDocumento);
    }

    /**
     * Update the specified bitacoras_envios_documento in storage.
     */
    public function update($id, Updatebitacoras_envios_documentoRequest $request)
    {
        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->find($id);

        if (empty($bitacorasEnviosDocumento)) {
            Flash::error('Bitacoras Envios Documento not found');

            return redirect(route('bitacorasEnviosDocumentos.index'));
        }

        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->update($request->all(), $id);

        Flash::success('Bitacoras Envios Documento updated successfully.');

        return redirect(route('bitacorasEnviosDocumentos.index'));
    }

    /**
     * Remove the specified bitacoras_envios_documento from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->find($id);

        if (empty($bitacorasEnviosDocumento)) {
            Flash::error('Bitacoras Envios Documento not found');

            return redirect(route('bitacorasEnviosDocumentos.index'));
        }

        $this->bitacorasEnviosDocumentoRepository->delete($id);

        Flash::success('Bitacoras Envios Documento deleted successfully.');

        return redirect(route('bitacorasEnviosDocumentos.index'));
    }
}
