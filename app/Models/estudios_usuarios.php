<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class estudios_usuarios extends Model
{  
     public $table = 'estudios_usuarios';

    public $fillable = [
        'id_users',
        'id_estudios'
    ];

    protected $casts = [
        'id_users' => 'integer',
        'id_estudios' => 'integer'
    ];

    public static array $rules = [
        'id_users' => 'required|exists:users,id',
        'id_estudios' => 'required|exists:estudios,id'
    ];
 
    public function User() {
        return $this->hasOne(User::class,'id','id_users');
    }
    public function estudios() {
        return $this->hasOne(estudios::class,'id','id_estudios');
    }
}
