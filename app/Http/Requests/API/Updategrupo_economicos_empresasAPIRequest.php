<?php

namespace App\Http\Requests\API;

use App\Models\grupo_economicos_empresas;
use InfyOm\Generator\Request\APIRequest;

class Updategrupo_economicos_empresasAPIRequest extends APIRequest
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
        $rules = grupo_economicos_empresas::$rules;
        
        return $rules;
    }
}
