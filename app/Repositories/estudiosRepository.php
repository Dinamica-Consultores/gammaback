<?php

namespace App\Repositories;

use App\Models\estudios;
use App\Repositories\BaseRepository;

class estudiosRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'razon_social',
        'per_cont_name',
        'per_cont_email',
        'per_cont_phone',
        'logo',
        'campo'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return estudios::class;
    }
}
