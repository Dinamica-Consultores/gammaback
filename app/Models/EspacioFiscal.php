<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class EspacioFiscal extends Model
{
     public $table = 'espacio_fiscals';

    public $fillable = [
        'ano',
        'mes',
        'nivel_3',
        'ajusta',
        'tipo',
        'operador',
        'signo',
        'monto_valor',
        'sucursal',
        'id_excel'
    ];

    protected $casts = [
        'ano' => 'integer',
        'mes' => 'integer',
        'nivel_3' => 'string',
        'ajusta' => 'string',
        'tipo' => 'string',
        'operador' => 'string',
        'signo' => 'string',
        'monto_valor' => 'string',
        'sucursal' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'ano' => 'required|integer',
        'mes' => 'required|integer',
        'nivel_3' => 'nullable|string|max:255',
        'ajusta' => 'nullable|string|max:255',
        'tipo' => 'nullable|string|max:255',
        'operador' => 'nullable|string|max:15',
        'signo' => 'nullable|string|max:10',
        'monto_valor' => 'nullable|string',
        'sucursal' => 'nullable|string',
        'id_excel' => 'required|exists:excelscompany,id'
    ];

    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
}
