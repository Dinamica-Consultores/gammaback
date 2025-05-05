<?php

namespace App\Http\Requests\API;

use App\Models\bitacora_hitos;
use InfyOm\Generator\Request\APIRequest;

class Updatebitacora_hitosAPIRequest extends APIRequest
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
        $rules = bitacora_hitos::$rules;
        
        return $rules;
    }
}
