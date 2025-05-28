<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Auth;
use App\Models\estudios_usuarios;
use App\Models\controlcuentas;
use App\Models\usuarios_empresas;
class User extends Model implements AuthenticatableContract
{
    use HasApiTokens,Notifiable,Authenticatable;
    public $table = 'users';

    public $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'mes' ,
        'ano' ,
        'max_var',
        'min_var',
        'level_user',
        'id_company_show',
        'id_group_show',
        'code_forgetpassword'
    ];

    protected $casts = [
        'name' => 'string',
        'surname' => 'string',
        'email' => 'string',
        'password' => 'string',
        'mes' => 'string',
        'ano' => 'string',
        'max_var' => 'string',
        'min_var' => 'string',
        'level_user'=> 'integer',
        'id_company_show'=> 'integer',
        'id_group_show'=>'integer',
        'code_forgetpassword'=>'string'
    ];

    public static array $rules = [
        'name' => 'required|min:3|max:255',
        'surname' => 'required|min:3|max:255',
        'email' => 'required|min:3|max:255|unique:users,email',
        'level_user' => 'required',
    ];
    public function getContainestudios()
    {
        $userestudios = estudios_usuarios::WHERE('id_users',Auth::user()->id)->get();
        return count($userestudios)>0?true:false;
    }
    public function getContainestudios2($id)
    {
        $userestudios = estudios_usuarios::WHERE('id_users',$id)->get();
        return count($userestudios)>0?false:true;
    }
    public function getusuarios_empresas2($id)
    {
        $userestudios = usuarios_empresas::WHERE('id_users',$id)->get();
        return count($userestudios)>0?false:true;
    }
    
    public function getIdEstudios()
    {
        $userestudios = estudios_usuarios::WHERE('id_users',Auth::user()->id)->get();
        return count($userestudios)>0?$userestudios[0]->id_estudios:0;
    }
    public function getControlStudioCantidades()
    {
        if(isset(Auth::user()->id)){
            $userestudios = estudios_usuarios::WHERE('id_users',Auth::user()->id)->get();
            $cantidadEmpresasControl = controlcuentas::SELECT('controlcuentas.id_excel')->JOIN('excelscompanies','excelscompanies.id','=','controlcuentas.id_excel')->join('companies','companies.id','=','excelscompanies.id_company')->WHERE('companies.id_estudio',$userestudios[0]->id_estudios)->distinct()->get();
           
            return count($cantidadEmpresasControl);
        }
        return 0;
    }
    public function getIdEstudios2($id)
    {
        $userestudios = estudios_usuarios::WHERE('id_users',$id)->get();
        return count($userestudios)>0?$userestudios[0]->id_estudios:0;
    }
}
