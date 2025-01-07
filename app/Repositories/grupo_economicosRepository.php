<?php

namespace App\Repositories;

use App\Models\grupo_economicos;
use App\Repositories\BaseRepository;

class grupo_economicosRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'nombre'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return grupo_economicos::class;
    }
}
