<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class bitacora_hitos extends Model
{
        public $table = 'bitacora_hitos';

    public $fillable = [
        'id_bitacora',
        'titulo',
        'description',
        'numero'
    ];

    protected $casts = [
        'id_bitacora' => 'integer',
        'titulo' => 'string',
        'description' => 'string',
        'numero' => 'integer'
    ];

    public static array $rules = [
        'titulo' => 'required',
        'description' => 'required'
    ];

    
}
