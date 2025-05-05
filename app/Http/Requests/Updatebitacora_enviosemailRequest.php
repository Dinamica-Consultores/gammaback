<?php

namespace App\Http\Requests;

use App\Models\bitacora_enviosemail;
use Illuminate\Foundation\Http\FormRequest;

class Updatebitacora_enviosemailRequest extends FormRequest
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
        $rules = bitacora_enviosemail::$rules;
        
        return $rules;
    }
}
