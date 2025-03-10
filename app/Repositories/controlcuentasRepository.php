<?php

namespace App\Repositories;

use App\Models\controlcuentas;
use App\Repositories\BaseRepository;

class controlcuentasRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'cuenta',
        'tipo'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return controlcuentas::class;
    }
}
