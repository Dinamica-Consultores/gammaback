<?php

namespace App\Repositories;

use App\Models\documento_compania;
use App\Repositories\BaseRepository;

class documento_companiaRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'nombre',
        'fecha_de_vencimiento',
        'fecha_de_generacion',
        'id_tipodocumento',
        'id_compania'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return documento_compania::class;
    }
}
