<?php

namespace App\Http\Requests;

use App\Models\bitacora;
use Illuminate\Foundation\Http\FormRequest;

class UpdatebitacoraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = bitacora::$rules;
        
        return $rules;
    }
}
