<?php

namespace App\Http\Requests\API;

use App\Models\client_user;
use InfyOm\Generator\Request\APIRequest;

class Updateclient_userAPIRequest extends APIRequest
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
        $rules = client_user::$rules;
        
        return $rules;
    }
}
