<?php

namespace App\Http\Requests\API;

use App\Models\clasificacion_cuenta_resul;
use InfyOm\Generator\Request\APIRequest;

class Createclasificacion_cuenta_resulAPIRequest extends APIRequest
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
        return clasificacion_cuenta_resul::$rules;
    }
}
