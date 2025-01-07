<?php

namespace App\Http\Requests;

use App\Models\in_balance;
use Illuminate\Foundation\Http\FormRequest;

class Updatein_balanceRequest extends FormRequest
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
        $rules = in_balance::$rules;
        
        return $rules;
    }
}
