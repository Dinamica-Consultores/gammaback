<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class sucursales extends Model
{
    public $table = 'sucursales';

    public $fillable = [
        'nombre',
        'id_excel'
    ];

    protected $casts = [
        'nombre' => 'string',
        'id_excel' => 'integer'
    ];

    public static array $rules = [
        'nombre' => 'required',
        'id_excel' => 'required|exists:excelscompany,id'
    ];
    public function excel() {
        return $this->hasOne(excelscompany::class,'id','id_excel');
    }
    
}
