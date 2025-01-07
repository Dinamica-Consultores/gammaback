<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class grupo_economicos_empresas extends Model
{
public $table = 'grupo_economicos_empresas';

    public $fillable = [
        'id_grupoeconomico',
        'id_company'
    ];

    protected $casts = [
        'id_grupoeconomico' => 'integer',
        'id_company' => 'integer'
    ];

    public static array $rules = [
        'id_grupoeconomico' => 'required|exists:grupo_economicos,id',
        'id_company' => 'required|exists:companies,id'
    ];

    
}
