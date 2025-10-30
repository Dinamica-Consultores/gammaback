<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createtipo_documentoAPIRequest;
use App\Http\Requests\API\Updatetipo_documentoAPIRequest;
use App\Models\tipo_documento;
use App\Repositories\tipo_documentoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class tipo_documentoAPIController
 */
class tipo_documentoAPIController extends AppBaseController
{
    private tipo_documentoRepository $tipoDocumentoRepository;

    public function __construct(tipo_documentoRepository $tipoDocumentoRepo)
    {
        $this->tipoDocumentoRepository = $tipoDocumentoRepo;
    }
    public function getAllTipoDocumentos():JsonResponse{
        $user =auth()->guard('api')->user();
        if($user->getContainestudios2($user->id)){
            return  $this->sendResponse([], 'No tienes Estudios');
        }
        $tipoDocumentos=tipo_documento::SELECT('tipo_documentos.*')->join('estudios_usuarios','estudios_usuarios.id_estudios','tipo_documentos.id_estudio')->where('estudios_usuarios.id_users', $user->id)->get();
        return $this->sendResponse($tipoDocumentos->toArray(), 'Tipo Documentos retrieved successfully');
    }
    /**
     * Display a listing of the tipo_documentos.
     * GET|HEAD /tipo_documentos
     */
    public function index(Request $request): JsonResponse
    {
        $tipoDocumentos = $this->tipoDocumentoRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($tipoDocumentos->toArray(), 'Tipo Documentos retrieved successfully');
    }

    /**
     * Store a newly created tipo_documento in storage.
     * POST /tipo_documentos
     */
    public function store(Createtipo_documentoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $tipoDocumento = $this->tipoDocumentoRepository->create($input);

        return $this->sendResponse($tipoDocumento->toArray(), 'Tipo Documento saved successfully');
    }

    /**
     * Display the specified tipo_documento.
     * GET|HEAD /tipo_documentos/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var tipo_documento $tipoDocumento */
        $tipoDocumento = $this->tipoDocumentoRepository->find($id);

        if (empty($tipoDocumento)) {
            return $this->sendError('Tipo Documento not found');
        }

        return $this->sendResponse($tipoDocumento->toArray(), 'Tipo Documento retrieved successfully');
    }

    /**
     * Update the specified tipo_documento in storage.
     * PUT/PATCH /tipo_documentos/{id}
     */
    public function update($id, Updatetipo_documentoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var tipo_documento $tipoDocumento */
        $tipoDocumento = $this->tipoDocumentoRepository->find($id);

        if (empty($tipoDocumento)) {
            return $this->sendError('Tipo Documento not found');
        }

        $tipoDocumento = $this->tipoDocumentoRepository->update($input, $id);

        return $this->sendResponse($tipoDocumento->toArray(), 'tipo_documento updated successfully');
    }

    /**
     * Remove the specified tipo_documento from storage.
     * DELETE /tipo_documentos/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var tipo_documento $tipoDocumento */
        $tipoDocumento = $this->tipoDocumentoRepository->find($id);

        if (empty($tipoDocumento)) {
            return $this->sendError('Tipo Documento not found');
        }

        $tipoDocumento->delete();

        return $this->sendSuccess('Tipo Documento deleted successfully');
    }
}
