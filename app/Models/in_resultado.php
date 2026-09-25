<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class in_resultado extends Model
{
    public $table = 'in_resultados';

    public $fillable = [
        'mes',
        'ano',
        'sucursal',
        'cuenta_master',
        'monto_uyu',
        'operador',
        'signo',
        'monto_valor',
        'id_excel'
    ];

    protected $casts = [
        'mes' => 'string',
        'ano' => 'string',
        'sucursal' => 'string',
        'cuenta_master' => 'string',
        'monto_uyu' => 'string',
        'operador' => 'string',
        'signo' => 'string',
        'monto_valor' => 'decimal:2',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'mes' => 'required',
        'ano' => 'required',
        'sucursal' => 'required',
        'cuenta_master' => 'required',
        'monto_uyu' => 'required',
        'operador' => 'nullable|string',
        'signo' => 'nullable|string|max:2',
        'monto_valor' => 'nullable|numeric',
        'id_excel' => 'required|exists:excelscompany,id'
    ];

    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
}