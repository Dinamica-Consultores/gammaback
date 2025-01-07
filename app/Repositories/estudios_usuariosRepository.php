<?php

namespace App\Repositories;

use App\Models\estudios_usuarios;
use App\Repositories\BaseRepository;

class estudios_usuariosRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id_users',
        'id_estudios'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return estudios_usuarios::class;
    }
}
