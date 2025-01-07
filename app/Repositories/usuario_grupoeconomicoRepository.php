<?php

namespace App\Repositories;

use App\Models\usuario_grupoeconomico;
use App\Repositories\BaseRepository;

class usuario_grupoeconomicoRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id_grupoeconomico',
        'id_users'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return usuario_grupoeconomico::class;
    }
}
