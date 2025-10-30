<?php

namespace App\Repositories;

use App\Models\bitacoras_envios_documento;
use App\Repositories\BaseRepository;

class bitacoras_envios_documentoRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id_documento_companias',
        'fecha_envio',
        'correos',
        'texto_data'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return bitacoras_envios_documento::class;
    }
}
