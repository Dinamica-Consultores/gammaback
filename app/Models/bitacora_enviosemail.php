<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class bitacora_enviosemail extends Model
{
      public $table = 'bitacora_enviosemails';

    public $fillable = [
        'id_bitacora',
        'id_user'
    ];

    protected $casts = [
        'id_bitacora' => 'integer',
        'id_user' => 'string'
    ];

    public static array $rules = [
        'id_user' => 'required'
    ];

    
}
