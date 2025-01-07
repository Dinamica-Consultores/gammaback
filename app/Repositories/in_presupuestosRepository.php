<?php

namespace App\Repositories;

use App\Models\in_presupuestos;
use App\Repositories\BaseRepository;

class in_presupuestosRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'mes',
        'ano',
        'sucursal',
        'cuenta_master',
        'monto_uyu',
        'monto_uyu_sinajuste_corriente',
        'monto_enuyu_sinajuste_corriente',
        'id_company',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return in_presupuestos::class;
    }
}
