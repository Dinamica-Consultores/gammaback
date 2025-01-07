<?php

namespace App\Repositories;

use App\Models\company;
use App\Repositories\BaseRepository;

class companyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'razon_social',
        'per_cont_name',
        'per_cont_email',
        'per_cont_phone',
        'logo',
        'campo',
        'ispresupuesto',
        'isestadosp',
        'mes',
        'ano'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return company::class;
    }
}
