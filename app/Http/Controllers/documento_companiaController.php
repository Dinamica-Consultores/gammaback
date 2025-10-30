<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createdocumento_companiaRequest;
use App\Http\Requests\Updatedocumento_companiaRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\documento_companiaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\company;
use App\Models\tipo_documento;
use App\Models\documento_compania;
use Illuminate\Support\Str;


use Flash;

class documento_companiaController extends AppBaseController
{
    /** @var documento_companiaRepository $documentoCompaniaRepository*/
    private $documentoCompaniaRepository;

    public function __construct(documento_companiaRepository $documentoCompaniaRepo)
    {
        $this->documentoCompaniaRepository = $documentoCompaniaRepo;
    }

    /**
     * Display a listing of the documento_compania.
     */
    public function index(Request $request)
    {
        
        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        $tipo_documentos = tipo_documento::Select('tipo_documentos.*')->where('tipo_documentos.id_estudio',auth()->user()->getIdEstudios())->pluck('nombre', 'id');
        $documentoCompanias=documento_compania::SELECT('documento_companias.*')->join('tipo_documentos','documento_companias.id_tipodocumento','tipo_documentos.id')->where('tipo_documentos.id_estudio',auth()->user()->getIdEstudios());
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
        if(!empty($datosWhere)){

            $documentoCompanias=$documentoCompanias->whereRaw($datosWhere,$arrayWhere);
        }
        $documentoCompanias= $documentoCompanias->paginate(10);
        $documentoCompanias->appends($request->all());

        return view('documento_companias.index')
            ->with('documentoCompanias', $documentoCompanias)->with('companies',$companies)->with('tipo_documentos',$tipo_documentos);
    }
    /**
     * Show the form for creating a new documento_compania.
     */
    public function create()
    {
        
        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        $tipo_documentos = tipo_documento::Select('tipo_documentos.*')->where('tipo_documentos.id_estudio',auth()->user()->getIdEstudios())->pluck('nombre', 'id');
        return view('documento_companias.create')->with('tipo_documentos',$tipo_documentos)->with('companies',$companies);
    }

    /**
     * Store a newly created documento_compania in storage.
     */
    public function store(Createdocumento_companiaRequest $request)
    {
        $input = $request->all();
        if($request->hasFile('file')){
            $path = $request->file('file')->store('public/documentos');
            $path2 = str_replace('public/', '', $path);
            $input['url']=$path2;
        }else{
        $input['url']='';
        }
        $documentoCompania = $this->documentoCompaniaRepository->create($input);

        Flash::success('Documento Compania saved successfully.');

        return redirect(route('documento_companias.index'));
    }

    /**
     * Display the specified documento_compania.
     */
    public function show($id)
    {
        $documentoCompania = $this->documentoCompaniaRepository->find($id);

        if (empty($documentoCompania)) {
            Flash::error('Documento Compania not found');

            return redirect(route('documento_companias.index'));
        }

        return view('documento_companias.show')->with('documentoCompania', $documentoCompania);
    }

    /**
     * Show the form for editing the specified documento_compania.
     */
    public function edit($id)
    {
        $documentoCompania = $this->documentoCompaniaRepository->find($id);

        if (empty($documentoCompania)) {
            Flash::error('Documento Compania not found');

            return redirect(route('documento_companias.index'));
        }
       
        $companies = company::Select('companies.*')->where('companies.id_estudio',auth()->user()->getIdEstudios())->pluck('razon_social', 'id');
        $tipo_documentos = tipo_documento::Select('tipo_documentos.*')->where('tipo_documentos.id_estudio',auth()->user()->getIdEstudios())->pluck('nombre', 'id');
        return view('documento_companias.edit')->with('documentoCompania', $documentoCompania)->with('tipo_documentos',$tipo_documentos)->with('companies',$companies);
    }

    /**
     * Update the specified documento_compania in storage.
     */
    public function update($id, Updatedocumento_companiaRequest $request)
    {
        $documentoCompania = $this->documentoCompaniaRepository->find($id);

        if (empty($documentoCompania)) {
            Flash::error('Documento Compania not found');

            return redirect(route('documento_companias.index'));
        }
        $input=$request->all();
        if($request->hasFile('file')){
            $path = $request->file('file')->store('public/documentos');
            $path2 = str_replace('public/', '', $path);
            $input['url']=$path2;
            if(isset($documentoCompania->url)&& !empty($documentoCompania->url)){  
            Storage::delete('public/'.$documentoCompania->url);
            }
        }
        $documentoCompania = $this->documentoCompaniaRepository->update($input, $id);

        Flash::success('Documento Compania updated successfully.');

        return redirect(route('documento_companias.index'));
    }

    /**
     * Remove the specified documento_compania from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $documentoCompania = $this->documentoCompaniaRepository->find($id);

        if (empty($documentoCompania)) {
            Flash::error('Documento Compania not found');

            return redirect(route('documento_companias.index'));
        }
        if(isset($documentoCompania->url)&& !empty($documentoCompania->url)){  
            Storage::delete('public/'.$documentoCompania->url);
            }
        $this->documentoCompaniaRepository->delete($id);

        Flash::success('Documento Compania deleted successfully.');

        return redirect(route('documento_companias.index'));
    }
    public function download($id)
{
    $documentoCompania = $this->documentoCompaniaRepository->find($id);
    if (!Storage::exists('public/'.$documentoCompania->url)) {
        abort(500, 'Archivo no encontrado.');
    }
    $filePath = Storage::path('public/'.$documentoCompania->url);
    
    $extension = pathinfo($documentoCompania->url, PATHINFO_EXTENSION);
    $fileName = Str::slug($documentoCompania->nombre) . '.' . $extension; 
    return response()->download($filePath, $fileName);
}
}
