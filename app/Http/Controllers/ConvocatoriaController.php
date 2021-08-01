<?php

namespace App\Http\Controllers;

use App\Models\Convocatoria;
use App\Models\Cargo;
use App\Libs\Funciones;
use Illuminate\Http\Request;

class ConvocatoriaController extends Controller
{
    public $parControl=[
        'modulo'=>'seleccion',
        'funcionalidad'=>'convocatorias',
        'titulo' =>'Convocatorias',
    ];

    public function index(Request $request)
    {
        $convocatorias = new Convocatoria();
        $buscar=$request->buscar;
        $pagina=$request->pagina;
        $resultado = $convocatorias->obtenerConvocatorias($buscar,$pagina);
        $mergeData = [
            'convocatorias'=>$resultado['convocatorias'],
            'total'=>$resultado['total'],
            'buscar'=>$buscar,
            'parPaginacion'=>$resultado['parPaginacion'],
            'parControl'=>$this->parControl
        ];
        return view('convocatorias.index',$mergeData);
    }


    public function agregar()
    { 
        $cargo= new Cargo();
        $cargos = $cargo->obtenerCargosActivos();

        $mergeData = ['parControl'=>$this->parControl,'cargos'=>$cargos];
        return view('convocatorias.agregar',$mergeData);  
    }

    public function insertar(Request $request)
    {
        $request->validate([
            'nombre'=>'required|max:250',
            'cargo_id'=>'required',
            'codigo'=>'required',
            'activo'=>'required',
        ]);

        $convocatoria = new Convocatoria();
        $convocatoria->nombre = $request->nombre;
        $convocatoria->cargo_id = $request->cargo_id;
        $convocatoria->codigo = $request->codigo;
        $convocatoria->activo = $request->activo?true:false;
        $convocatoria->save();

        return redirect()->route('convocatorias.mostrar',$convocatoria->id);
    }
    public function mostrar($id)
    {
        $convocatoria = Convocatoria::getConvocatoria($id);
        $mergeData = ['id'=>$id,'convocatoria'=>$convocatoria,'parControl'=>$this->parControl];
        return view('convocatorias.mostrar',$mergeData);
    }

    public function modificar($id)
    {
        $convocatoria = Convocatoria::find($id);
        $cargo= new Cargo();
        $cargos = $cargo->obtenerCargosActivos();

        $mergeData = ['id'=>$id,'convocatoria'=>$convocatoria,'cargos'=>$cargos,'parControl'=>$this->parControl];
        return view('convocatorias.modificar',$mergeData);
    }

    public function actualizar(Request $request, convocatoria $convocatoria)
    {
        $request->validate([
            'nombre'=>'required|max:250',
            'cargo_id'=>'required',
            'codigo'=>'required',
            'activo'=>'required',
        ]);

        $convocatoria->nombre = $request->nombre;
        $convocatoria->cargo_id = $request->cargo_id;
        $convocatoria->codigo = $request->codigo;
        $convocatoria->activo = $request->activo?true:false;
        $convocatoria->save();

        return redirect()->route('convocatorias.mostrar',$convocatoria->id);
    }

    public function eliminar($id)
    {
        $convocatoria = Convocatoria::find($id);
        $convocatoria->eliminado=true;
        $convocatoria->save();
        return redirect()->route('convocatorias.index');
    }
}
