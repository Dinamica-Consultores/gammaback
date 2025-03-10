<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class controlcuentas extends Model
{
    
     public $table = 'controlcuentas';

    public $fillable = [
        'cuenta',
        'tipo'
    ];

    protected $casts = [
        'cuenta' => 'string',
        'tipo' => 'string'
    ];

    public static array $rules = [
        'cuenta' => 'required',
        'tipo' => 'required'
    ];

    
}
