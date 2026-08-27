<?php
namespace App\Http\Requests;

use App\Models\company;
use Illuminate\Foundation\Http\FormRequest;

class CreatecompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return company::getRules();
    }
}
