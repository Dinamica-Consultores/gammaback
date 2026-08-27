<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class company extends Model
{
    use SoftDeletes;

    public $table = 'companies';

    public $fillable = [
        'razon_social',
        'per_cont_name',
        'per_cont_email',
        'per_cont_phone',
        'responsable',
        'logo',
        'campo',
        'ispresupuesto',
        'isestadosp',
        'mes',
        'ano',
        'id_estudio',
        'id_moneda',
        'destinatario_documentos',
        'cuenta_anticipos_ipat',
        'cuenta_anticipos_irae',
        'porcentaje_irae',
        'porcentaje_ipat',
    ];

    protected $casts = [
        'razon_social'           => 'string',
        'per_cont_name'          => 'string',
        'per_cont_email'         => 'string',
        'per_cont_phone'         => 'string',
        'responsable'            => 'string',
        'logo'                   => 'string',
        'campo'                  => 'string',
        'ispresupuesto'          => 'boolean',
        'isestadosp'             => 'boolean',
        'mes'                    => 'string',
        'ano'                    => 'string',
        'id_estudio'             => 'integer',
        'id_moneda'              => 'integer',
        'destinatario_documentos'=> 'string',
        'cuenta_anticipos_ipat'  => 'string',
        'cuenta_anticipos_irae'  => 'string',
        
        'porcentaje_irae'        => 'float',
        'porcentaje_ipat'        => 'float',
    ];

    /**
     * Devuelve las reglas de validación dinámicamente según el ID de la compañía.
     */
    public static function getRules($companyId = null): array
    {
        return [
            'razon_social'   => 'required|min:3|max:255',
            'per_cont_name'  => 'required|min:3|max:255',
            'per_cont_email' => 'required|min:3|max:255',
            'responsable'    => 'required|min:3|max:255',
            'per_cont_phone' => 'required|min:3|max:255',
            'id_moneda'      => 'required',
            'cuenta_anticipos_irae' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($companyId) {
                    if ($value) {
                        $exists = \DB::table('clasificacion_cuenta_resuls')
                            ->join('excelscompanies', 'excelscompanies.id', '=', 'clasificacion_cuenta_resuls.id_excel')
                            ->where('excelscompanies.id_company', $companyId)
                            ->where('clasificacion_cuenta_resuls.cuenta', $value)
                            ->exists();

                        if (!$exists) {
                            $fail('La cuenta de anticipos especificada no existe en el plan de cuentas importado del cliente.');
                        }
                    }
                },
            ],            
            'cuenta_anticipos_ipat' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($companyId) {
                    if ($value) {
                        $exists = \DB::table('categorizacion_cts_balances')
                            ->join('excelscompanies', 'excelscompanies.id', '=', 'categorizacion_cts_balances.id_excel')
                            ->where('excelscompanies.id_company', $companyId)
                            ->where('categorizacion_cts_balances.cuenta', $value)
                            ->exists();

                        if (!$exists) {
                            $fail('La cuenta de anticipos especificada no existe en el plan de cuentas importado del cliente.');
                        }
                    }
                },
            ],
            'porcentaje_irae' => 'required|numeric|between:0,999.99',
            'porcentaje_ipat' => 'required|numeric|between:0,999.99',
        ];
    }
}