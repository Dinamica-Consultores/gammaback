<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class documento_compania extends Model
{
     use SoftDeletes;    public $table = 'documento_companias';

    public $fillable = [
        'nombre',
        'fecha_de_vencimiento',
        'fecha_de_generacion',
        'id_tipodocumento',
        'id_compania',
        'url',
        'notificacion_enviada_venicimiento'
    ];

    protected $casts = [
        'nombre' => 'string',
        'id_tipodocumento' => 'integer',
        'id_compania' => 'integer',
        'url' => 'string',
        'notificacion_enviada_venicimiento'=>'boolean'
    ];

    public static array $rules = [
        'nombre' => 'required',
        'fecha_de_vencimiento' => 'nullable',
        'fecha_de_generacion' => 'required',
        'id_tipodocumento' => 'exists:tipo_documentos,id',
        'id_compania' => 'exists:estudios,id',
        'url' => 'nullable|max:2000'
    ];
    protected function fechaDeGeneracion(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::parse($value)->format('Y-m-d'),
        );
    }
    protected function fechaDeVencimiento(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::parse($value)->format('Y-m-d'),
        );
    }
    public function company() {
        return $this->hasOne(company::class,'id','id_compania');
    }
    public function tipo_documento() {
        return $this->hasOne(tipo_documento::class,'id','id_tipodocumento');
    }
}
