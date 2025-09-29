<?php

namespace App\Repositories;

use App\Models\compromiso_entrega;
use App\Repositories\BaseRepository;

class compromiso_entregaRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'fecha_entrega',
        'fecha_entregado',
        'fecha_reunion',
        'usuario',
        'usuario_entregado',
        'id_company',
        'descripcion_entregado',
        'descripcion_entrega'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return compromiso_entrega::class;
    }
}
