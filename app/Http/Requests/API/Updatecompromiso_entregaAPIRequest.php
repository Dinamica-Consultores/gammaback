<?php

namespace App\Http\Requests\API;

use App\Models\compromiso_entrega;
use InfyOm\Generator\Request\APIRequest;

class Updatecompromiso_entregaAPIRequest extends APIRequest
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
        $rules = compromiso_entrega::$rules;
        
        return $rules;
    }
}
