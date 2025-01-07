<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class grupo_economicos extends Model
{
     use SoftDeletes;    public $table = 'grupo_economicos';

    public $fillable = [
        'nombre',
        'id_estudio',
        'id_moneda'
    ];

    protected $casts = [
        'nombre' => 'string',
        'id_estudio' => 'integer',
        'id_moneda'=>'integer'
    ];

    public static array $rules = [
        'nombre' => 'required|min:3|max:255',
        'id_moneda'=>'required'
    ];

    
}
