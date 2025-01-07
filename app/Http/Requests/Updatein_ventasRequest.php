<?php

namespace App\Http\Requests;

use App\Models\in_ventas;
use Illuminate\Foundation\Http\FormRequest;

class Updatein_ventasRequest extends FormRequest
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
        $rules = in_ventas::$rules;
        
        return $rules;
    }
}
