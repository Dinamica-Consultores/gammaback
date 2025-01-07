<?php

namespace App\Http\Requests;

use App\Models\grupo_economicos;
use Illuminate\Foundation\Http\FormRequest;

class Creategrupo_economicosRequest extends FormRequest
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
        return grupo_economicos::$rules;
    }
}
