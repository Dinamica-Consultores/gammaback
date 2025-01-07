<?php

namespace App\Repositories;

use App\Models\excelscompany;
use App\Repositories\BaseRepository;

class excelscompanyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'path',
        'version',
        'date',
        'id_company'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return excelscompany::class;
    }
}
