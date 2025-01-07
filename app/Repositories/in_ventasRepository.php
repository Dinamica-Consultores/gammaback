<?php

namespace App\Repositories;

use App\Models\in_ventas;
use App\Repositories\BaseRepository;

class in_ventasRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'mes',
        'ano',
        'sucursal',
        'codigo_analisis',
        'cantidad_venta_unidades',
        'cantidad_costo',
        'cantidad_margen',
        'ventas_uyu',
        'ganancia_bruta_uyu',
        'costo_uyu',
        'id_company',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return in_ventas::class;
    }
}
