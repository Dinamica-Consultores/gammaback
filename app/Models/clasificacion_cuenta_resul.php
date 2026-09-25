<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class clasificacion_cuenta_resul extends Model
{
    public $table = 'clasificacion_cuenta_resuls';

    public $fillable = [
        'cuenta',
        'nombre',
        'origen',
        'grupo',
        'nivel_1',
        'nivel_2',
        'nivel_3',
        'clasificacion_ratios_financ',
        'clasificacion_punto_equilibrio',
        'clasificacion_cuenta_juridica_legal',
        'clasificacion_ebit_ebitda',
        'clasificacion_er',
        'espacio_fiscal_ajuste',
        'irae',
        'id_excel'
    ];

    protected $casts = [
        'cuenta' => 'string',
        'nombre' => 'string',
        'origen'=> 'string',
        'grupo' => 'string',
        'nivel_1' => 'string',
        'nivel_2' => 'string',
        'nivel_3' => 'string',
        'clasificacion_ratios_financ' => 'string',
        'clasificacion_punto_equilibrio' => 'string',
        'clasificacion_cuenta_juridica_legal' => 'string',
        'clasificacion_ebit_ebitda' => 'string',
        'clasificacion_er' => 'string',
        'espacio_fiscal_ajuste' => 'string',
        'irae' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'cuenta' => 'required',
        'nombre' => 'required',
        'origen'=> 'required',
        'grupo' => 'required',
        'nivel_1' => 'required',
        'nivel_2' => 'required',
        'nivel_3' => 'required',
        'clasificacion_ratios_financ' => 'required',
        'clasificacion_punto_equilibrio' => 'required',
        'clasificacion_cuenta_juridica_legal' => 'required',
        'clasificacion_ebit_ebitda' => 'required',
        'clasificacion_er' => 'required',
        'espacio_fiscal_ajuste' => 'nullable|string',
        'irae' => 'nullable|string',
        'id_excel' => 'required|exists:excelscompany,id'
    ];

    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
}