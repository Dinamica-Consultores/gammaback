<?php

namespace App\Http\Requests;

use App\Models\grupo_economicos_empresas;
use Illuminate\Foundation\Http\FormRequest;

class Creategrupo_economicos_empresasRequest extends FormRequest
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
        return grupo_economicos_empresas::$rules;
    }
}
