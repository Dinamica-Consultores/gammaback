<?php

namespace App\Repositories;

use App\Models\tipo_documento;
use App\Repositories\BaseRepository;

class tipo_documentoRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'nombre',
        'cantidad_dias_preaviso',
        'id_estudio'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return tipo_documento::class;
    }
}
