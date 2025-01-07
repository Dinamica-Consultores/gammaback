<?php

namespace App\Repositories;

use App\Models\grupo_economicos_empresas;
use App\Repositories\BaseRepository;

class grupo_economicos_empresasRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id_grupoeconomico',
        'id_company'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return grupo_economicos_empresas::class;
    }
}
