<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class bitacora extends Model
{
     use SoftDeletes;   
      public $table = 'bitacoras';

    public $fillable = [
        'descripcion',
        'id_grupoeconomico',
        'id_estudio',
        'persona_agrega'
    ];

    protected $casts = [
        'descripcion' => 'string',
        'id_grupoeconomico' => 'integer',
        'id_estudio' => 'integer',
        'persona_agrega' => 'string'
    ];

    public static array $rules = [
        'descripcion' => 'required',
        'persona_agrega' => 'required'
    ];

    public function grupoeconomicos() {
        return $this->hasOne(grupo_economicos::class,'id','id_grupoeconomico');
    }
}
