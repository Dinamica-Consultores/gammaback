<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class tipo_documento extends Model
{
     use SoftDeletes;    public $table = 'tipo_documentos';

    public $fillable = [
        'nombre',
        'cantidad_dias_preaviso',
        'id_estudio',
        'is_one'
    ];

    protected $casts = [
        'nombre' => 'string',
        'cantidad_dias_preaviso' => 'integer',
        'id_estudio' => 'integer',
        'is_one'=>'boolean'
    ];

    public static array $rules = [
        'nombre' => 'required',
        'cantidad_dias_preaviso' => 'required|integer|min:0',
        'id_estudio' => 'exists:estudios,id'
    ];

    
}
