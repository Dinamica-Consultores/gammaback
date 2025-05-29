<?php

namespace App\Repositories;

use App\Models\tipo_cambios_global;
use App\Repositories\BaseRepository;

class tipo_cambios_globalRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id_estudio',
        'dolar_compra',
        'dolar_venta',
        'dolar_promedio',
        'euro_promedio',
        'francosuizo_promedio',
        'ui',
        'ipc',
        'fecha'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return tipo_cambios_global::class;
    }
}
