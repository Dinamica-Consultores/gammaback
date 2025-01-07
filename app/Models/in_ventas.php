<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class in_ventas extends Model
{
      public $table = 'in_ventas';

    public $fillable = [
        'mes',
        'ano',
        'sucursal',
        'codigo_analisis',
        'cantidad_venta_unidades',
        'cantidad_costo',
        'cantidad_margen',
        'ventas_uyu',
        'ganancia_bruta_uyu',
        'ventas_uyu_prom',
        'ganancia_bruta_uyu_prom',
        'costo_uyu',
        'id_excel'
    ];

    protected $casts = [
        'mes' => 'string',
        'ano' => 'string',
        'sucursal' => 'string',
        'codigo_analisis' => 'string',
        'cantidad_venta_unidades' => 'string',
        'cantidad_costo' => 'string',
        'cantidad_margen' => 'string',
        'ventas_uyu' => 'string',
        'ganancia_bruta_uyu' => 'string',
        'ventas_uyu_prom'=> 'string',
        'ganancia_bruta_uyu_prom'=> 'string',
        'costo_uyu' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'mes' => 'required',
        'ano' => 'required',
        'sucursal' => 'required',
        'codigo_analisis' => 'required',
        'cantidad_venta_unidades' => 'required',
        'cantidad_costo' => 'required',
        'cantidad_margen' => 'required',
        'ventas_uyu' => 'required',
        'ganancia_bruta_uyu' => 'required',
        'ventas_uyu_prom'=> 'required',
        'ganancia_bruta_uyu_prom'=> 'required',
        'costo_uyu' => 'required',
        'id_excel' => 'required|exists:excelscompany,id'
    ];
    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
    
}
