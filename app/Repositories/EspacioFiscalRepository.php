<?php

namespace App\Repositories;

use App\Models\EspacioFiscal;
use App\Repositories\BaseRepository;

class EspacioFiscalRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'ano',
        'mes',
        'nivel_3',
        'ajusta',
        'tipo',
        'operador',
        'signo',
        'monto_valor',
        'sucursal',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return EspacioFiscal::class;
    }
}
