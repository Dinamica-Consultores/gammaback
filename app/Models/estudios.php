<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class estudios extends Model
{
     use SoftDeletes;    public $table = 'estudios';

    public $fillable = [
        'razon_social',
        'per_cont_name',
        'per_cont_phone',
        'cantida_empresa_max',
        'es_empresa',
        'logo',
        'campo'
    ];

    protected $casts = [
        'razon_social' => 'string',
        'cantida_empresa_max' => 'integer',
        'es_empresa'=>'boolean',
        'per_cont_name' => 'string',
        'per_cont_phone' => 'string',
        'logo' => 'string',
        'campo' => 'string'
    ];

    public static array $rules = [
        'razon_social' => 'required|min:3|max:255',
        'per_cont_name' => 'required|min:3|max:255',
        'per_cont_phone' => 'required|min:3|max:255'
    ];

    
}
