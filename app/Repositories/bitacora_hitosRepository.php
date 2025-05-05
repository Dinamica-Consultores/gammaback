<?php

namespace App\Repositories;

use App\Models\bitacora_hitos;
use App\Repositories\BaseRepository;

class bitacora_hitosRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id_bitacora',
        'titulo',
        'description',
        'numero'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return bitacora_hitos::class;
    }
}
