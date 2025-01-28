<?php

namespace App\Repositories;

use App\Models\bitacora;
use App\Repositories\BaseRepository;

class bitacoraRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'descripcion',
        'id_grupoeconomico',
        'persona_agrega'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return bitacora::class;
    }
}
