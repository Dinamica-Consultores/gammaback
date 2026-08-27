<?php
namespace App\Http\Requests;

use App\Models\company;
use Illuminate\Foundation\Http\FormRequest;

class UpdatecompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Obtiene el ID de la compañía desde la ruta
        $companyId = $this->route('company'); 

        return company::getRules($companyId);
    }
}
