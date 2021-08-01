<?php
//eddyamnia

namespace App\Http\Controllers;

use App\Models\EntrevistaProgramada;
use App\Models\Postulacion;
use App\Libs\Funciones;
use Illuminate\Http\Request;
//hecho por will
class EntrevistaProgramadaController extends Controller
{
    public $parControl=[
        'modulo'=>'seleccion',
        'funcionalidad'=>'entrevistas_programadas',
        'titulo' =>'Entrevistas Programadas',
    ];

    public function index(Request $request)
    {
        $entrevistaProgramada = new EntrevistaProgramada();
        $buscar=$request->buscar;
        $pagina=$request->pagina;
        $resultado = $entrevistaProgramada->obtenerEntrevistasProgramadas($buscar,$pagina);
        $mergeData = [
        'entrevistas_programadas'=>$resultado['entrevistas_programadas'],
        'total'=>$resultado['total'],
        'buscar'=>$buscar,
        'parPaginacion'=>$resultado['parPaginacion'],
        'parControl'=>$this->parControl];
        return view('entrevistas_programadas.index',$mergeData);
    }
    public function mostrar($id)
    {
        $entrevistaProgramada = EntrevistaProgramada::find($id);
        $mergeData = ['id'=>$id,'tipo_empleado'=>$entrevistaProgramada,'parControl'=>$this->parControl];
        return view('entrevistas_programadas.mostrar',$mergeData);
    }

    public function agregar()
    {   
        $postulacion= new Postulacion();
        $postulaciones = $postulacion->obtenerPostulacionesActivas();

        $mergeData = ['parControl'=>$this->parControl,'postulaciones'=>$postulaciones];
        return view('entrevistas_programadas.agregar',$mergeData);
    }

    public function insertar(Request $request)
    {
        $request->validate([
            'fecha'=>'required|max:30',
            'hora'=>'required',
            'postulacion_id'=>'required',
        ]);
        
        $entrevistaProgramada = new EntrevistaProgramada();
        $entrevistaProgramada->fecha = $request->fecha;
        $entrevistaProgramada->hora = $request->hora;
        $entrevistaProgramada->postulacion_id = $request->postulacion_id;
        $entrevistaProgramada->activo = $request->activo?true:false;
        $entrevistaProgramada->save();

        return redirect()->route('entrevistas_programadas.mostrar',$entrevistaProgramada->id);
    }

    public function modificar($id)
    {
        $entrevistaProgramada = EntrevistaProgramada::find($id);

        $postulacion= new Postulacion();
        $postulaciones = $postulacion->obtenerPostulacionesActivas();

        $mergeData = ['id'=>$id,'tipo_empleado'=>$entrevistaProgramada,'postulaciones'=>$postulaciones,'parControl'=>$this->parControl];
        return view('entrevistas_programadas.modificar',$mergeData);
    }

    public function actualizar(Request $request, EntrevistaProgramada $entrevistaProgramada)
    {
        $request->validate([
            'fecha'=>'required|max:30',
            'hora'=>'required',
            'postulacion_id'=>'required',
        ]);
        $entrevistaProgramada->fecha = $request->fecha;
        $entrevistaProgramada->hora = $request->hora;
        $entrevistaProgramada->postulacion_id = $request->postulacion_id;
        $entrevistaProgramada->activo = $request->activo?true:false;
        $entrevistaProgramada->save();

        return redirect()->route('entrevistas_programadas.mostrar',$entrevistaProgramada->id);
    }

    public function eliminar($id)
    {
        $entrevistaProgramada = EntrevistaProgramada::find($id);
        $entrevistaProgramada->eliminado=true;
        $entrevistaProgramada->save();
        return redirect()->route('entrevistas_programadas.index');
    }
}
