<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use App\Models\in_resultado;
use App\Models\in_presupuestos;
use Illuminate\Support\Facades\Hash;
use App\Models\company;
use App\Models\sucursales;
use App\Models\usuario_grupoeconomico;
use App\Models\sessiones;
use App\Models\grupo_economicos;
use Carbon\Carbon;
use Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
  

    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
                $user = Auth::user();
                $grupoEconomicos=usuario_grupoeconomico::SELECT(DB::raw('grupo_economicos.id,grupo_economicos.nombre'))->join('grupo_economicos','grupo_economicos.id','usuario_grupoeconomicos.id_grupoeconomico')
                ->where('usuario_grupoeconomicos.id_users',$user->id)->get();
                if(count($grupoEconomicos)<=0){
                    return response()->json([
                        'message' => 'No puedes ingresar es necesario tener una Red Comercial',
                        'errors' => 'No show',
                    ], 422);
                }else{  
                    sessiones::insert([
                        'user_id' => $user->id,
                        'opcion' => 'Login',
                        'created_at'=>Carbon::now()
                    ]);
                    $accessToken = $user->createToken('GammaBack')->accessToken;
                    $token_detail = $user->createToken('GammaBack');
                    return response()->json([
                        'user' => $user,
                        'grupoEconomicos'=> $grupoEconomicos,
                        'access_token' => $accessToken
                    ]);
                }
              
            }

            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], 401);
        } catch (ValidationException $e) {
            info($e);
            // Validation failed, return validation errors
            return response()->json([
                'message' => 'Login error necesitas verificar datos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            info($e);
            // Handle other exceptions
            return response()->json([
                'message' => 'Login failed due to an unexpected error.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function forgetpassword(Request $request)
    {
        $input = $request->all();
        $user = User::where('email', $input['email'])->get();
        $codigo=Str::upper(Str::random(16));
        if (count($user)<1) {
            return response()->json([
                'message' => 'Este correo electronico no existe.',
            ], 422);
        }
        $users = User::where('email', $input['email'])->update(['code_forgetpassword'=> $codigo]);
        $dataSend=[
        'date'=>date("Y-m-d H:i:s", strtotime("+1 days", strtotime(date("Y/m/d H:i:s ")))),
        'user'=>$user[0]->id,
        'codigo'=>$codigo];
        $dato['href']=env('APP_URL_CHANGEPASS').'change-password/'.base64_encode(json_encode($dataSend));
        Mail::send(['html' => 'users.mail'], ['dato'=>$dato], function($message)use ($input) {
            $message->to( $input['email'] ,'')->subject('GAMMA - Link de Restablecer contrasena');
            $message->from(env('MAIL_USERNAME') , env('MAIL_FROM_NAME'));
        });
        return response()->json([
            'message' => 'Su nueva contraseña fue enviada.',
        ], 200);
    }
    public function checkforget($data){
        $dataUser=json_decode(base64_decode($data), true);
        $usuario=User::where('id',$dataUser['user'])->where('code_forgetpassword',$dataUser['codigo'])->first();
        if(isset($usuario)){
            
        $usuario=User::where('id',$dataUser['user'])->where('code_forgetpassword',$dataUser['codigo'])->update(['code_forgetpassword' => '']);
            return response()->json([
                'message' => 'correcto',
            ], 200);
        }else{
            return response()->json([
                'message' => 'Vencida recuperacion',
            ], 200);
        }
    }
    public function changePassword(Request $request){
        $input=$request->all();
        $dataUser=json_decode(base64_decode($input['token']), true);
        $password = Hash::make($input['password']);
        $usuario=User::where('id',$dataUser['user'])->update(['password' => $password]);

        return response()->json([
            'message' => 'Actualizado',
        ], 200);
    }
    public function logout(Request $request)
    {
        try {
            // Intentar obtener el usuario de la petición/bearer token
            $user = auth()->guard('api')->user();
    
            if ($user) {
                // Revocar únicamente el token activo
                if ($user->token()) {
                    $user->token()->revoke();
                }
    
                // Registrar log de sesión
                sessiones::insert([
                    'user_id' => $user->id,
                    'opcion'  => 'Logout',
                    'created_at' => Carbon::now()
                ]);
            }
    
            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
    
        } catch (\Exception $e) {
            info($e);
            // Responder siempre 200 OK en logout para no trancar al cliente si el token ya no existe
            return response()->json([
                'message' => 'Token expirado o inexistente',
            ], 200);
        }
    }

    public function updateDataInfo(Request $request)
    {
        try {
            $input=$request->all();
            $user =auth()->guard('api')->user();
            if(isset($user->id_group_show)){
                if(isset($input['id_group_show'])){
                    if((int)$user->id_group_show===(int)$input['id_group_show']){
                    }else{
                        $nombreGrupoEconomico=grupo_economicos::find($input['id_group_show']);
                        sessiones::insert([
                            'user_id' => $user->id,
                            'opcion' => 'Cambio de Grupo Economico '.$nombreGrupoEconomico->nombre,
                            'created_at'=>Carbon::now()
                        ]);
                        
                    }
                }
             
            }
            $data=User::where('id',$user->id)->update($input);
            $user =User::find($user->id);
            $todas='Todas';
            $datasucursales=sucursales::SELECT('sucursales.*')
            ->join('excelscompanies', 'excelscompanies.id', 'sucursales.id_excel')
            ->join('companies', 'companies.id', 'excelscompanies.id_company');
            $datasucursales= $datasucursales->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
            $datasucursales= $datasucursales->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
            $datasucursales= $datasucursales->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
            $datasucursales= $datasucursales->where('usuario_grupoeconomicos.id_users',$user->id);

            $getMaxAnosResultado=in_resultado::selectRaw('MAX(in_resultados.ano) as ANOMAX,min(in_resultados.ano) as ANOMIN')
            ->join('excelscompanies','excelscompanies.id','in_resultados.id_excel')
            ->join('companies','excelscompanies.id_company','companies.id');
            $getMaxAnosResultado= $getMaxAnosResultado->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
            $getMaxAnosResultado= $getMaxAnosResultado->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
            $getMaxAnosResultado= $getMaxAnosResultado->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
            $getMaxAnosResultado= $getMaxAnosResultado->where('usuario_grupoeconomicos.id_users',$user->id);
            $getMaxAnosResultado= $getMaxAnosResultado->where('in_resultados.ano','<>','');
            
            $getMaxAnosPresupuesto=in_presupuestos::selectRaw('MAX(in_presupuestos.ano) as ANOMAX,min(in_presupuestos.ano) as ANOMIN')
            ->join('excelscompanies','excelscompanies.id','in_presupuestos.id_excel')
            ->join('companies','excelscompanies.id_company','companies.id');
            $getMaxAnosPresupuesto= $getMaxAnosPresupuesto->join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id');
            $getMaxAnosPresupuesto= $getMaxAnosPresupuesto->join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico');
            $getMaxAnosPresupuesto= $getMaxAnosPresupuesto->where('grupo_economicos_empresas.id_grupoeconomico',$user->id_group_show);
            $getMaxAnosPresupuesto= $getMaxAnosPresupuesto->where('usuario_grupoeconomicos.id_users',$user->id);
            $getMaxAnosPresupuesto= $getMaxAnosPresupuesto->where('in_presupuestos.ano','<>','');
           
            if($user->id_company_show>0){
                $datasucursales= $datasucursales->where('grupo_economicos_empresas.id_company',$user->id_company_show);
                
                $getMaxAnosResultado= $getMaxAnosResultado->where('grupo_economicos_empresas.id_company',$user->id_company_show);
                $getMaxAnosPresupuesto= $getMaxAnosPresupuesto->where('grupo_economicos_empresas.id_company',$user->id_company_show);

                $dataEmpresa=company::find($user->id_company_show);
                $todas=$dataEmpresa->razon_social;
                $dataEmpresa['logo']=asset('storage/' . $dataEmpresa['logo']);
                sessiones::insert([
                    'user_id' => $user->id,
                    'opcion' => 'Cambio de compañias '.$dataEmpresa->nombre,
                    'created_at'=>Carbon::now()
                ]);
                
            }else{ 
                $cantidadCompanies=company::SELECT('companies.*')->
                join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id')->
                join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico')->
                where('usuario_grupoeconomicos.id_users',$user->id)
                ->where('usuario_grupoeconomicos.id_grupoeconomico',$user->id_group_show)
                ->get()->count();
                $dataEmpresa=company::SELECT('companies.*')->
                join('grupo_economicos_empresas','grupo_economicos_empresas.id_company','companies.id')->
                join('usuario_grupoeconomicos','usuario_grupoeconomicos.id_grupoeconomico','grupo_economicos_empresas.id_grupoeconomico')->
                where('usuario_grupoeconomicos.id_users',$user->id)
                ->where('usuario_grupoeconomicos.id_grupoeconomico',$user->id_group_show)
                ->first();
                $grupoEconomicos=grupo_economicos::where('id',$user->id_group_show)->first();
                if($cantidadCompanies==1){
                  
                $getMaxAnosResultado= $getMaxAnosResultado->where('grupo_economicos_empresas.id_company',$dataEmpresa->id);
                $getMaxAnosPresupuesto= $getMaxAnosPresupuesto->where('grupo_economicos_empresas.id_company',$dataEmpresa->id);

                    $todas=$dataEmpresa->razon_social;
                    sessiones::insert([
                        'user_id' => $user->id,
                        'opcion' => 'Cambio de compañias '.$dataEmpresa->razon_social,
                        'created_at'=>Carbon::now()
                    ]);
                $dataEmpresa['logo']=asset('storage/' . $dataEmpresa['logo']);
                }else if($cantidadCompanies==0){
                    return response()->json([
                        'message' => 'No Tienes ninguna compania ligada.',
                        'error' => $e->getMessage(),
                    ], 500);
                }else{
                    sessiones::insert([
                        'user_id' => $user->id,
                        'opcion' => 'Cambio de compañias Todas',
                        'created_at'=>Carbon::now()
                    ]);
                    
                $dataEmpresa['logo']=asset('storage/' . $grupoEconomicos['logo']);
                }
                
            }
            $accessToken = $user->createToken('GammaBack')->accessToken;
            $token_detail = $user->createToken('GammaBack');
            $users =User::where('id',$user->id)->get();
            return  response()->json(['message' => 'Successfully logged out','data'=>$users,'infoClient'=>[
                'client'=> $dataEmpresa,
                'name_empresa'=>$todas,
                'sucursales'=>$datasucursales->get(),
                'anosResultado'=> $getMaxAnosResultado->get(),
                'anosPresupuesto'=> $getMaxAnosPresupuesto->get()
            ]]);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'message' => 'Logout failed due to an unexpected error.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
