<?php

namespace App\Repositories;

use App\Models\setup_analisis;
use App\Repositories\BaseRepository;

class setup_analisisRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'codigo',
        'nombre',
        'id_company',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return setup_analisis::class;
    }
}
