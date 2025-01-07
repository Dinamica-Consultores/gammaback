<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class excelscompany extends Model
{
        public $table = 'excelscompanies';

    public $fillable = [
        'path',
        'version',
        'date',
        'id_company'
    ];

    protected $casts = [
        'path' => 'string',
        'version' => 'string',
        'id_company' => 'integer'
    ];

    public static array $rules = [
        'version' => 'required|min:3|max:255',
        'date' => 'required',
        'id_company' => 'required|exists:companies,id'
    ];
    public function company() {
        return $this->hasOne(company::class,'id','id_company');
    }
    
}
