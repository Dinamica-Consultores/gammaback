<?php

namespace App\Repositories;

use App\Models\categorizacion_cts_balance;
use App\Repositories\BaseRepository;

class categorizacion_cts_balanceRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'cuenta',
        'nombre',
        'origen',
        'nivel_1',
        'nivel_2',
        'nivel_3',
        'nivel_4',
        'posicion_moneda',
        'posicion_fiscal',
        'id_company',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return categorizacion_cts_balance::class;
    }
}
