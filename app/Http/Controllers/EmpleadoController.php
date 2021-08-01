<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Persona;
use App\Models\TipoEmpleado;
use App\Models\Sucursal;
use App\Models\Cargo;
use App\Libs\Funciones;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public $parControl=[
        'modulo'=>'seguridad',
        'funcionalidad'=>'empleados',
        'titulo' =>'Empleados',
    ];

    public function index(Request $request)
    {
        $empleado = new Empleado();
        $buscar=$request->buscar;
        $pagina=$request->pagina;
        $resultado = $empleado->obtenerEmpleados($buscar,$pagina);
        $mergeData = [
            'empleados'=>$resultado['empleados'],
            'total'=>$resultado['total'],
            'buscar'=>$buscar,
            'parPaginacion'=>$resultado['parPaginacion'],
            'parControl'=>$this->parControl
        ];
        return view('empleados.index',$mergeData);
    }
    
    public function mostrar($id)
    {
        $empleado = Empleado::getEmpleado($id);
        
        $mergeData = ['id'=>$id,'empleado'=>$empleado,'parControl'=>$this->parControl];
        return view('empleados.mostrar',$mergeData);
    }

    public function agregar()
    { 
        $tipo_empleado = new  TipoEmpleado();
        $tipos_empleados=$tipo_empleado->obtenerTiposEmpleadosActivos();

        $sucursal = new  Sucursal();
        $sucursales=$sucursal->obtenerSucursalesActivos();
        
        $cargo = new  Cargo();
        $cargos=$cargo->obtenerCargosActivos();

        $mergeData = ['parControl'=>$this->parControl, 'tipos_empleados'=>$tipos_empleados,'sucursales'=>$sucursales,'cargos'=>$cargos];
        return view('empleados.agregar',$mergeData);  
    }

    public function insertar(Request $request)
    {
        $request->validate([
          'id'=>'required',
          'correo_corporativo'=>'required|max:100',
     
          'profesion'=>'required',

      
          'tipo_empleado_id'=>'required',
          'cargo_id'=>'required',
          'sucursal_id'=>'required',
          'activo'=>'required',
        ]);
        $persona = Persona::find($request->id);
        $empleado = new Empleado();
        $empleado->id = $request->id;
        $empleado->correo_corporativo = $request->correo_corporativo;
        $empleado->pass = md5($persona->ci) ;
        $empleado->profesion = $request->profesion;
        $empleado->usuario = $persona->ci ;
        $empleado->perfil_id = 1;
        $empleado->tipo_empleado_id = $request->tipo_empleado_id;
        $empleado->cargo_id = $request->cargo_id;
        $empleado->sucursal_id = $request->sucursal_id;
        $empleado->activo = $request->activo?true:false;
        $empleado->save();

        return redirect()->route('empleados.mostrar',$request->id);
    }

    public function modificar($id)
    {
        $empleado = Empleado::find($id);
        $oPersona = new Persona();
        $nombrePersona = $oPersona->getNombreCompleto($id);

        $tipo_empleado = new  TipoEmpleado();
        $tipos_empleados=$tipo_empleado->obtenerTiposEmpleadosActivos();

        $sucursal = new  Sucursal();
        $sucursales=$sucursal->obtenerSucursalesActivos();
        
        $cargo = new  Cargo();
        $cargos=$cargo->obtenerCargosActivos();

        $mergeData = ['id'=>$id,'empleado'=>$empleado,'nombreCompleto'=>$nombrePersona,'parControl'=>$this->parControl,'tipos_empleados'=>$tipos_empleados,'sucursales'=>$sucursales,'cargos'=>$cargos];

        return view('empleados.modificar',$mergeData);
    }

    public function actualizar(Request $request, Empleado $empleado)
    {
        $request->validate([
        
            'correo_corporativo'=>'required|max:100',
            'profesion'=>'required|max:50',
            'tipo_empleado_id'=>'required',
            'cargo_id'=>'required',
            'sucursal_id'=>'required',
            'activo'=>'required',
          ]);
       
       
          $empleado->correo_corporativo = $request->correo_corporativo;
          $empleado->profesion = $request->profesion;
          $empleado->tipo_empleado_id = $request->tipo_empleado_id;
          $empleado->cargo_id = $request->cargo_id;
          $empleado->sucursal_id = $request->sucursal_id;
          
          $empleado->activo = $request->activo?true:false;
          $empleado->save();


        return redirect()->route('empleados.mostrar',$empleado->id);
    }

    public function eliminar($id)
    {
        $empleado = Empleado::find($id);
        $empleado->eliminado=true;
        $empleado->save();
        return redirect()->route('empleados.index');
    }

    public function personasActivas(Request $request)
    {
        $buscar=$request->q;
        $empleado = new Empleado();
        $personas = $empleado->buscarPersonas($buscar);
        $resultado=[];
        foreach ($personas as $persona){
            $resultado[]=(object)['name'=>$persona->nombre,'id'=>$persona->id];
        }
        return json_encode($resultado);
    }
}
