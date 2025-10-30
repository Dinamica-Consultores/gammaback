<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createbitacoras_envios_documentoAPIRequest;
use App\Http\Requests\API\Updatebitacoras_envios_documentoAPIRequest;
use App\Models\bitacoras_envios_documento;
use App\Repositories\bitacoras_envios_documentoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class bitacoras_envios_documentoAPIController
 */
class bitacoras_envios_documentoAPIController extends AppBaseController
{
    private bitacoras_envios_documentoRepository $bitacorasEnviosDocumentoRepository;

    public function __construct(bitacoras_envios_documentoRepository $bitacorasEnviosDocumentoRepo)
    {
        $this->bitacorasEnviosDocumentoRepository = $bitacorasEnviosDocumentoRepo;
    }

    /**
     * Display a listing of the bitacoras_envios_documentos.
     * GET|HEAD /bitacoras_envios_documentos
     */
    public function index(Request $request): JsonResponse
    {
        $bitacorasEnviosDocumentos = $this->bitacorasEnviosDocumentoRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($bitacorasEnviosDocumentos->toArray(), 'Bitacoras Envios Documentos retrieved successfully');
    }

    /**
     * Store a newly created bitacoras_envios_documento in storage.
     * POST /bitacoras_envios_documentos
     */
    public function store(Createbitacoras_envios_documentoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->create($input);

        return $this->sendResponse($bitacorasEnviosDocumento->toArray(), 'Bitacoras Envios Documento saved successfully');
    }

    /**
     * Display the specified bitacoras_envios_documento.
     * GET|HEAD /bitacoras_envios_documentos/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var bitacoras_envios_documento $bitacorasEnviosDocumento */
        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->find($id);

        if (empty($bitacorasEnviosDocumento)) {
            return $this->sendError('Bitacoras Envios Documento not found');
        }

        return $this->sendResponse($bitacorasEnviosDocumento->toArray(), 'Bitacoras Envios Documento retrieved successfully');
    }

    /**
     * Update the specified bitacoras_envios_documento in storage.
     * PUT/PATCH /bitacoras_envios_documentos/{id}
     */
    public function update($id, Updatebitacoras_envios_documentoAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var bitacoras_envios_documento $bitacorasEnviosDocumento */
        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->find($id);

        if (empty($bitacorasEnviosDocumento)) {
            return $this->sendError('Bitacoras Envios Documento not found');
        }

        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->update($input, $id);

        return $this->sendResponse($bitacorasEnviosDocumento->toArray(), 'bitacoras_envios_documento updated successfully');
    }

    /**
     * Remove the specified bitacoras_envios_documento from storage.
     * DELETE /bitacoras_envios_documentos/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var bitacoras_envios_documento $bitacorasEnviosDocumento */
        $bitacorasEnviosDocumento = $this->bitacorasEnviosDocumentoRepository->find($id);

        if (empty($bitacorasEnviosDocumento)) {
            return $this->sendError('Bitacoras Envios Documento not found');
        }

        $bitacorasEnviosDocumento->delete();

        return $this->sendSuccess('Bitacoras Envios Documento deleted successfully');
    }
}
