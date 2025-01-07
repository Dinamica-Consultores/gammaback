<?php

namespace App\Http\Requests;

use App\Models\setup_analisis;
use Illuminate\Foundation\Http\FormRequest;

class Updatesetup_analisisRequest extends FormRequest
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
        $rules = setup_analisis::$rules;
        
        return $rules;
    }
}
