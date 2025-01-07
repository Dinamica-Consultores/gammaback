<?php

namespace App\Repositories;

use App\Models\clasificacion_cuenta_resul;
use App\Repositories\BaseRepository;

class clasificacion_cuenta_resulRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'cuenta',
        'nombre',
        'grupo',
        'nivel_1',
        'nivel_2',
        'nivel_3',
        'clasificacion_ratios_financ',
        'clasificacion_punto_equilibrio',
        'clasificacion_cuenta_juridica_legal',
        'clasificacion_ebit_ebitda',
        'id_company',
        'id_excel'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return clasificacion_cuenta_resul::class;
    }
}
