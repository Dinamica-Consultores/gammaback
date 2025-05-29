<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class tipo_cambios_global extends Model
{   
     public $table = 'tipo_cambios_globals';

    public $fillable = [
        'id_estudio',
        'mes',
        'ano',
        'dolar_compra',
        'dolar_venta',
        'dolar_promedio',
        'euro_promedio',
        'francosuizo_promedio',
        'ui',
        'ipc',
        'fecha'
    ];

    protected $casts = [
        'dolar_compra' => 'string',
        'dolar_venta' => 'string',
        'dolar_promedio' => 'string',
        'euro_promedio' => 'string',
        'francosuizo_promedio' => 'string',
        'ui' => 'string',
        'ipc' => 'string',
        'fecha' => 'datetime',
        'mes' => 'string',
        'ano' => 'string',
    ];

    public static array $rules = [
        'dolar_compra' => 'required',
        'dolar_venta' => 'required',
        'dolar_promedio' => 'required',
        'euro_promedio' => 'required',
        'francosuizo_promedio' => 'required',
        'ui' => 'required',
        'ipc' => 'required',
        'fecha' => 'required'
    ];

    public function idEstudio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\estudios::class, 'id_estudio', 'id', 'cascade');
    }
}
