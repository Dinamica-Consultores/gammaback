<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\Createbitacora_enviosemailAPIRequest;
use App\Http\Requests\API\Updatebitacora_enviosemailAPIRequest;
use App\Models\bitacora_enviosemail;
use App\Repositories\bitacora_enviosemailRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class bitacora_enviosemailAPIController
 */
class bitacora_enviosemailAPIController extends AppBaseController
{
    private bitacora_enviosemailRepository $bitacoraEnviosemailRepository;

    public function __construct(bitacora_enviosemailRepository $bitacoraEnviosemailRepo)
    {
        $this->bitacoraEnviosemailRepository = $bitacoraEnviosemailRepo;
    }

    /**
     * Display a listing of the bitacora_enviosemails.
     * GET|HEAD /bitacora_enviosemails
     */
    public function index(Request $request): JsonResponse
    {
        $bitacoraEnviosemails = $this->bitacoraEnviosemailRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($bitacoraEnviosemails->toArray(), 'Bitacora Enviosemails retrieved successfully');
    }

    /**
     * Store a newly created bitacora_enviosemail in storage.
     * POST /bitacora_enviosemails
     */
    public function store(Createbitacora_enviosemailAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->create($input);

        return $this->sendResponse($bitacoraEnviosemail->toArray(), 'Bitacora Enviosemail saved successfully');
    }

    /**
     * Display the specified bitacora_enviosemail.
     * GET|HEAD /bitacora_enviosemails/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var bitacora_enviosemail $bitacoraEnviosemail */
        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->find($id);

        if (empty($bitacoraEnviosemail)) {
            return $this->sendError('Bitacora Enviosemail not found');
        }

        return $this->sendResponse($bitacoraEnviosemail->toArray(), 'Bitacora Enviosemail retrieved successfully');
    }

    /**
     * Update the specified bitacora_enviosemail in storage.
     * PUT/PATCH /bitacora_enviosemails/{id}
     */
    public function update($id, Updatebitacora_enviosemailAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var bitacora_enviosemail $bitacoraEnviosemail */
        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->find($id);

        if (empty($bitacoraEnviosemail)) {
            return $this->sendError('Bitacora Enviosemail not found');
        }

        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->update($input, $id);

        return $this->sendResponse($bitacoraEnviosemail->toArray(), 'bitacora_enviosemail updated successfully');
    }

    /**
     * Remove the specified bitacora_enviosemail from storage.
     * DELETE /bitacora_enviosemails/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var bitacora_enviosemail $bitacoraEnviosemail */
        $bitacoraEnviosemail = $this->bitacoraEnviosemailRepository->find($id);

        if (empty($bitacoraEnviosemail)) {
            return $this->sendError('Bitacora Enviosemail not found');
        }

        $bitacoraEnviosemail->delete();

        return $this->sendSuccess('Bitacora Enviosemail deleted successfully');
    }
}
