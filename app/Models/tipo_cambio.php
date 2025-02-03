<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class tipo_cambio extends Model
{
    public $table = 'tipo_cambios';

    public $fillable = [
        'fecha',
        'mes',
        'ano',
        'dolar_compra',
        'dolar_venta',
        'dolar_promedio',
        'euro_promedio',
        'francosuizo_promedio',
        'ui',
        'ipc',
        'ipc_empresa',
        'id_excel'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'dolar_compra' => 'string',
        'dolar_venta' => 'string',
        'mes' => 'string',
        'ano' => 'string',
        'dolar_promedio' => 'string',
        'euro_promedio' => 'string',
        'francosuizo_promedio' => 'string',
        'ui' => 'string',
        'ipc' => 'string',
        'ipc_empresa' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'fecha' => 'required',
        'dolar_compra' => 'required',
        'dolar_venta' => 'required',
        'dolar_promedio' => 'required',
        'euro_promedio' => 'required',
        'francosuizo_promedio' => 'required',
        'ui' => 'required',
        'ipc' => 'required',
        'ipc_empresa' => 'required',
        'id_excel' => 'required|exists:excelscompany,id'
    ];
    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
     
}
