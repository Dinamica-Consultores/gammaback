<?php

namespace App\Repositories;

use App\Models\bitacora_enviosemail;
use App\Repositories\BaseRepository;

class bitacora_enviosemailRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id_bitacora',
        'id_user'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return bitacora_enviosemail::class;
    }
}
