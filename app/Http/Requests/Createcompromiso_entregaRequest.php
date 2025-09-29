<?php

namespace App\Http\Requests;

use App\Models\compromiso_entrega;
use Illuminate\Foundation\Http\FormRequest;

class Createcompromiso_entregaRequest extends FormRequest
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
        return compromiso_entrega::$rules;
    }
}
