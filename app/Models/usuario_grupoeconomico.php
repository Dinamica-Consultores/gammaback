<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class usuario_grupoeconomico extends Model
{
   public $table = 'usuario_grupoeconomicos';

    public $fillable = [
        'id_grupoeconomico',
        'id_users'
    ];

    protected $casts = [
        'id_grupoeconomico' => 'integer',
        'id_users' => 'integer'
    ];

    public static array $rules = [
        'id_grupoeconomico' => 'required|exists:grupo_economicos,id',
        'id_users' => 'required|exists:users,id'
    ];

    
}
