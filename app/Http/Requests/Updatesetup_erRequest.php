<?php

namespace App\Http\Requests;

use App\Models\setup_er;
use Illuminate\Foundation\Http\FormRequest;

class Updatesetup_erRequest extends FormRequest
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
        $rules = setup_er::$rules;
        
        return $rules;
    }
}
