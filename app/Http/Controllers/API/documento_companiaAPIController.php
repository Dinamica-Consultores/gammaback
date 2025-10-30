<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createdocumento_companiaAPIRequest;
use App\Http\Requests\API\Updatedocumento_companiaAPIRequest;
use App\Models\documento_compania;
use App\Repositories\documento_companiaRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;
/**
 * Class documento_companiaAPIController
 */
class documento_companiaAPIController extends AppBaseController
{
    private documento_companiaRepository $documentoCompaniaRepository;

    public function __construct(documento_companiaRepository $documentoCompaniaRepo)
    {
        $this->documentoCompaniaRepository = $documentoCompaniaRepo;
    }

    /**
     * Display a listing of the documento_companias.
     * GET|HEAD /documento_companias
     */
    public function index(Request $request): JsonResponse
    {
        $documentoCompanias = $this->documentoCompaniaRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($documentoCompanias->toArray(), 'Documento Companias retrieved successfully');
    }
    public function getAllDocumentosCompanias():JsonResponse{
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $documentoCompania=documento_compania::SELECT('documento_companias.*');
        $documentoCompania= $documentoCompania->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','documento_companias.id_compania');
        $documentoCompania= $documentoCompania->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
        $documentoCompania= $documentoCompania->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
        $documentoCompania= $documentoCompania->where('usuario_grupoeconomicos.id_users',$user->id);
        if($user->id_company_show>0){
            $documentoCompania= $documentoCompania->where('grupo_economicos_empresas.id_company',$user->id_company_show);
        }
        $documentoCompania=$documentoCompania->get();
        return $this->sendResponse($documentoCompania->toArray(), 'Tipo Documentos retrieved successfully');
    }

    public function download($id)
    {
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $documento = documento_compania::find($id);
        if (!$documento) {
            return response()->json(['message' => 'Documento no encontrado.'], 404);
        }
        $filePath='public/'.$documento->url;
        $extension = pathinfo($documento->url, PATHINFO_EXTENSION);
        $fileName = Str::slug($documento->nombre) . '.' . $extension; 
        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'El archivo físico no existe.'], 404);
        }
        return Storage::download(
            $filePath,
            $fileName, 
            [
                'Content-Type' => Storage::mimeType($filePath),
            ]
        );

    }
    /**
     * Store a newly created documento_compania in storage.
     * POST /documento_companias
     */
    public function store(Createdocumento_companiaAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $documentoCompania = $this->documentoCompaniaRepository->create($input);

        return $this->sendResponse($documentoCompania->toArray(), 'Documento Compania saved successfully');
    }

    /**
     * Display the specified documento_compania.
     * GET|HEAD /documento_companias/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var documento_compania $documentoCompania */
        $documentoCompania = $this->documentoCompaniaRepository->find($id);

        if (empty($documentoCompania)) {
            return $this->sendError('Documento Compania not found');
        }

        return $this->sendResponse($documentoCompania->toArray(), 'Documento Compania retrieved successfully');
    }

    /**
     * Update the specified documento_compania in storage.
     * PUT/PATCH /documento_companias/{id}
     */
    public function update($id, Updatedocumento_companiaAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var documento_compania $documentoCompania */
        $documentoCompania = $this->documentoCompaniaRepository->find($id);

        if (empty($documentoCompania)) {
            return $this->sendError('Documento Compania not found');
        }

        $documentoCompania = $this->documentoCompaniaRepository->update($input, $id);

        return $this->sendResponse($documentoCompania->toArray(), 'documento_compania updated successfully');
    }

    /**
     * Remove the specified documento_compania from storage.
     * DELETE /documento_companias/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var documento_compania $documentoCompania */
        $documentoCompania = $this->documentoCompaniaRepository->find($id);

        if (empty($documentoCompania)) {
            return $this->sendError('Documento Compania not found');
        }

        $documentoCompania->delete();

        return $this->sendSuccess('Documento Compania deleted successfully');
    }
}
