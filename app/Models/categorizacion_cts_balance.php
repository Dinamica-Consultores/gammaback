<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categorizacion_cts_balance extends Model
{
    public $table = 'categorizacion_cts_balances';

    public $fillable = [
        'cuenta',
        'nombre',
        'origen',
        'nivel_1',
        'nivel_2',
        'nivel_3',
        'nivel_4',
        'posicion_moneda',
        'posicion_fiscal',
        'posicion_socios',
        'espacio_fiscal_ajuste',
        'id_excel'
    ];

    protected $casts = [
        'cuenta' => 'string',
        'nombre' => 'string',
        'origen' => 'string',
        'nivel_1' => 'string',
        'nivel_2' => 'string',
        'nivel_3' => 'string',
        'nivel_4' => 'string',
        'posicion_moneda' => 'string',
        'posicion_fiscal' => 'string',
        'posicion_socios' => 'string',
        'espacio_fiscal_ajuste' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'cuenta' => 'required',
        'nombre' => 'required',
        'origen' => 'required',
        'nivel_1' => 'required',
        'nivel_2' => 'required',
        'nivel_3' => 'required',
        'nivel_4' => 'required',
        'posicion_moneda' => 'required',
        'posicion_fiscal' => 'required',
        'posicion_socios' => 'required',
        'espacio_fiscal_ajuste' => 'nullable|string',
        'id_excel' => 'required|exists:excelscompany,id'
    ];

    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
}