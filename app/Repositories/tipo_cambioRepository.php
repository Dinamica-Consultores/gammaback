<?php

namespace App\Repositories;

use App\Models\tipo_cambio;
use App\Repositories\BaseRepository;

class tipo_cambioRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'fecha',
        'dolar_compra',
        'dolar_venta',
        'dolar_promedio',
        'id_company',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return tipo_cambio::class;
    }
}
