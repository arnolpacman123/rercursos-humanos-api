<?php

use Illuminate\Support\Facades\Route;

use App\Models\User;

use App\Http\Controllers\SessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModuloController;
// use App\Http\Controllers\FuncionalidadController;
// use App\Http\Controllers\EstudianteController;
// use App\Http\Controllers\DocenteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/', HomeController::class)->middleware('sesion.iniciada')->name('index');
Route::post('iniciar-session', [SessionController::class,'iniciar'])->name('iniciar.session');
Route::get('cerrar-session', [SessionController::class,'cerrar'])->name('cerrar.session');

Route::get('login',  function () {
    return view('login');
})->name('login');

include_once 'webroutes/ModulosRoutes.php';
//will
include_once 'webroutes/PersonasRoutes.php';
include_once 'webroutes/BonosRoutes.php';

include_once 'webroutes/PerfilesRoutes.php';
include_once 'webroutes/UsuariosRoutes.php';

include_once 'webroutes/FuncionalidadesRoutes.php';
include_once 'webroutes/DescuentosRoutes.php';
//XD
//omla
include_once 'webroutes/PostulantesRoutes.php';


////////////////////////henry holasas
include_once 'webroutes/TareasRoutes.php';
include_once 'webroutes/DepartamentosRoutes.php';
include_once 'webroutes/TiposEmpleadosRoutes.php';
include_once 'webroutes/SucursalesRoutes.php';
include_once 'webroutes/CargosRoutes.php';

include_once 'webroutes/VacacionesRoutes.php';
include_once 'webroutes/FaltasRoutes.php';
include_once 'webroutes/HorariosRoutes.php';
include_once 'webroutes/ConvocatoriasRoutes.php';
include_once 'webroutes/PostulacionesRoutes.php';
include_once 'webroutes/EntrevistasProgramadas.php';





include_once 'webroutes/EmpleadosRoutes.php';
///////////////////////carlos
include_once 'webroutes/PreguntasRoutes.php';
include_once 'webroutes/PlantillasRoutes.php';





//////////////////////patricio








//////////////////////wilfredo

