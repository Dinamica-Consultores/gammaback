<?php

namespace App\Http\Requests;

use App\Models\controlcuentas;
use Illuminate\Foundation\Http\FormRequest;

class UpdatecontrolcuentasRequest extends FormRequest
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
        $rules = controlcuentas::$rules;
        
        return $rules;
    }
}
