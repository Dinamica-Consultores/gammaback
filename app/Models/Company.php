<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class company extends Model
{
     use SoftDeletes;    public $table = 'companies';

    public $fillable = [
        'razon_social',
        'per_cont_name',
        'per_cont_email',
        'per_cont_phone',
        'responsable',
        'logo',
        'campo',
        'ispresupuesto',
        'isestadosp',
        'mes',
        'ano',
        'id_estudio',
        'id_moneda'
    ];

    protected $casts = [
        'razon_social' => 'string',
        'per_cont_name' => 'string',
        'per_cont_email' => 'string',
        'per_cont_phone' => 'string',
        'responsable'=>'string',
        'logo' => 'string',
        'campo' => 'string',
        'ispresupuesto' => 'boolean',
        'isestadosp' => 'boolean',
        'mes' => 'string',
        'ano' => 'string',
        'id_estudio'=>'integer',
        'id_moneda'=>'integer'
    ];

    public static array $rules = [
        'razon_social' => 'required|min:3|max:255',
        'per_cont_name' => 'required|min:3|max:255',
        'per_cont_email' => 'required|min:3|max:255',
        'responsable' => 'required|min:3|max:255',
        'per_cont_phone' => 'required|min:3|max:255',
        'id_moneda'=>'required'
    ];
    
    
}
