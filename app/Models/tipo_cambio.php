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
        'ipc_empresa',
        'id_excel'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'mes' => 'string',
        'ano' => 'string',
        'ipc_empresa' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'fecha' => 'required',
        'ipc_empresa' => 'required',
        'id_excel' => 'required|exists:excelscompany,id'
    ];
    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
     
}
