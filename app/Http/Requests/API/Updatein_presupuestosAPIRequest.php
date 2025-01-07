<?php

namespace App\Http\Requests\API;

use App\Models\in_presupuestos;
use InfyOm\Generator\Request\APIRequest;

class Updatein_presupuestosAPIRequest extends APIRequest
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
        $rules = in_presupuestos::$rules;
        
        return $rules;
    }
}
