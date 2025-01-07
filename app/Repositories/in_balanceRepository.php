<?php

namespace App\Repositories;

use App\Models\in_balance;
use App\Repositories\BaseRepository;

class in_balanceRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'mes',
        'ano',
        'sucursal',
        'cuenta_master',
        'saldo_uyu',
        'monto_uyu',
        'id_company',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return in_balance::class;
    }
}
