<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class compromiso_entrega extends Model
{
     use SoftDeletes;    public $table = 'compromiso_entregas';

    public $fillable = [
        'fecha_entrega',
        'fecha_entregado',
        'fecha_reunion',
        'usuario',
        'usuario_entregado',
        'id_company',
        'descripcion_entregado',
        'descripcion_entrega'
    ];

    protected $casts = [
        'usuario' => 'integer',
        'usuario_entregado'=> 'integer',
        'id_company' => 'integer',
        'descripcion_entregado' => 'string',
        'descripcion_entrega' => 'string',
        'fecha_entregado' => 'date',
        'fecha_reunion' => 'date',
        'fecha_entrega' => 'date',
    ];

    public static array $rules = [
        'usuario' => 'required|exists:users,id',
        'id_company' => 'required|exists:companies,id',
        'fecha_reunion' => 'required',
        'fecha_entrega' => 'required',
    ];
    public function company() {
        return $this->hasOne(company::class,'id','id_company');
    }
    public function user() {
        return $this->hasOne(user::class,'id','usuario');
    }
    public function userEntrega() {
        return $this->hasOne(user::class,'id','usuario_entregado');
    }
    
}
