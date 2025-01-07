<?php

namespace App\Repositories;

use App\Models\sucursales;
use App\Repositories\BaseRepository;

class sucursalesRepository extends BaseRepository
{
    protected $fieldSearchable = [
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
        return sucursales::class;
    }
}
