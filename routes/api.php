<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Permiso;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ping', function (Request $request) {
    return 'pong';
});



Route::post('/autentificar', function (Request $request) {
    $permiso = new Permiso();        
    $login = $request->login;
    $password =  md5($request->pass) ;
    $tipo =  'adm';
    
    $usuario = $permiso->obtenerUsuario($login,$password,$tipo);
    
    if($usuario){
        $permisos = $permiso->obtenerPermisosUsuario($usuario->perfil_id,$usuario->tipo);
        $respuesta =['success'=>true,'id'=>$usuario->id,'login'=>$usuario->login,'nombre'=>$usuario->nombre_completo,'perfil_id'=>$usuario->perfil_id,'permisos'=>$permisos];
        return response($respuesta, 200)->header('Content-Type', 'application/json');
    }else{
        $respuesta=['success'=>false,"mensaje"=>"usuario no encontrado"];
        return response($respuesta, 200)->header('Content-Type', 'application/json');
    }
});

// Route::post('/listar-vacaciones', function (Request $request) {
//     $login =$request->login;
//     $pass =$request->pass;
//     if(existe) return '{"success":true}';
// });
