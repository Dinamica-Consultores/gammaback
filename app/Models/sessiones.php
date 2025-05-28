<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class sessiones extends Model
{ 
      public $table = 'sessiones';

    public $fillable = [
        'user_id',
        'opcion'
    ];

    protected $casts = [
        'opcion' => 'string'
    ];

    public static array $rules = [
        'user_id' => 'required|exists:User,id',
        'opcion' => 'required'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id', 'cascade');
    }
}
