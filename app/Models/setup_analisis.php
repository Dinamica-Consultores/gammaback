<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class setup_analisis extends Model
{
     public $table = 'setup_analises';

    public $fillable = [
        'codigo',
        'nombre',
        'id_excel'
    ];

    protected $casts = [
        'codigo' => 'string',
        'nombre' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'codigo' => 'required',
        'nombre' => 'required',
        'id_excel' => 'required|exists:excelscompany,id'
    ];

    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
}
