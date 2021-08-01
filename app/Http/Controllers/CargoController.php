<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Departamento;
use App\Libs\Funciones;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public $parControl=[
        'modulo'=>'recurso',
        'funcionalidad'=>'cargos',
        'titulo' =>'Cargos',
    ];

    public function index(Request $request)
    {
        $cargos = new Cargo();
        $buscar=$request->buscar;
        $pagina=$request->pagina;
        $resultado = $cargos->obtenerCargos($buscar,$pagina);
        $mergeData = [
            'cargos'=>$resultado['cargos'],
            'total'=>$resultado['total'],
            'buscar'=>$buscar,
            'parPaginacion'=>$resultado['parPaginacion'],
            'parControl'=>$this->parControl
        ];
        return view('cargos.index',$mergeData);
    }


    public function agregar()
    { 
        $departamento= new Departamento();
        $departamentos = $departamento->obtenerDepartamentosActivos();

        $mergeData = ['parControl'=>$this->parControl,'departamentos'=>$departamentos];
        return view('cargos.agregar',$mergeData);  
    }

    public function insertar(Request $request)
    {
        $request->validate([
            'nombre'=>'required|max:50',
            'departamento_id'=>'required',
            'activo'=>'required',
        ]);

        $cargo = new Cargo();
        $cargo->nombre = $request->nombre;
        $cargo->departamento_id = $request->departamento_id;
        $cargo->activo = $request->activo?true:false;
        $cargo->save();

        return redirect()->route('cargos.mostrar',$cargo->id);
    }
    public function mostrar($id)
    {
        $cargo = Cargo::getCargo($id);
        $mergeData = ['id'=>$id,'cargo'=>$cargo,'parControl'=>$this->parControl];
        return view('cargos.mostrar',$mergeData);
    }

    public function modificar($id)
    {
        $cargo = Cargo::find($id);
        $departamento= new Departamento();
        $departamentos = $departamento->obtenerDepartamentosActivos();

        $mergeData = ['id'=>$id,'cargo'=>$cargo,'departamentos'=>$departamentos,'parControl'=>$this->parControl];
        return view('cargos.modificar',$mergeData);
    }

    public function actualizar(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre'=>'required|max:50',
            'departamento_id'=>'required',
            'activo'=>'required',
        ]);

       
        $cargo->nombre = $request->nombre;
        $cargo->departamento_id = $request->departamento_id;
        $cargo->activo = $request->activo?true:false;
        $cargo->save();

        return redirect()->route('cargos.mostrar',$cargo->id);
    }

    public function eliminar($id)
    {
        $cargo = Cargo::find($id);
        $cargo->eliminado=true;
        $cargo->save();
        return redirect()->route('cargos.index');
    }
}
