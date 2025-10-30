<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class bitacoras_envios_documento extends Model
{
     use SoftDeletes;    public $table = 'bitacoras_envios_documentos';

    public $fillable = [
        'id_documento_companias',
        'fecha_envio',
        'correos',
        'texto_data'
    ];

    protected $casts = [
        'id_documento_companias' => 'integer',
        'fecha_envio' => 'date',
        'correos' => 'string',
        'texto_data' => 'string'
    ];

    public static array $rules = [
        'id_documento_companias' => 'exists:documento_companias,id',
        'fecha_envio' => 'nullable|date|after:today'
    ];
    public function documento_companias() {
        return $this->hasOne(documento_compania::class,'id','id_documento_companias');
    }
    
}
