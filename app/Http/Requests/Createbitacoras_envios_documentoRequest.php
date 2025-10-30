<?php

namespace App\Http\Requests;

use App\Models\bitacoras_envios_documento;
use Illuminate\Foundation\Http\FormRequest;

class Createbitacoras_envios_documentoRequest extends FormRequest
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
        return bitacoras_envios_documento::$rules;
    }
}
