<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class in_presupuestos extends Model
{
      public $table = 'in_presupuestos';

    public $fillable = [
        'mes',
        'ano',
        'sucursal',
        'cuenta_master',
        'monto_uyu',
        'id_excel'
    ];

    protected $casts = [
        'mes' => 'string',
        'ano' => 'string',
        'sucursal' => 'string',
        'cuenta_master' => 'string',
        'monto_uyu' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'mes' => 'required',
        'ano' => 'required',
        'sucursal' => 'required',
        'cuenta_master' => 'required',
        'monto_uyu' => 'required',
        'id_excel' => 'required|exists:excelscompany,id'
    ];
    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
}
