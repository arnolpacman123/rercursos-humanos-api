<?php

namespace App\Http\Controllers;

use App\Models\Postulacion;
use App\Models\Postulante;
use App\Models\Convocatoria;
use App\Models\Empleado;
use App\Models\EntrevistaProgramada;
use App\Libs\Funciones;
use Illuminate\Http\Request;

class PostulacionController extends Controller
{
    public $parControl=[
        'modulo'=>'seleccion',
        'funcionalidad'=>'postulaciones',
        'titulo' =>'Postulaciones',
    ];

    public function index(Request $request)
    {
        $postulaciones = new Postulacion();
        $buscar=$request->buscar;
        $pagina=$request->pagina;
        $resultado = $postulaciones->obtenerpostulaciones($buscar,$pagina);
        $mergeData = [
            'postulaciones'=>$resultado['postulaciones'],
            'total'=>$resultado['total'],
            'buscar'=>$buscar,
            'parPaginacion'=>$resultado['parPaginacion'],
            'parControl'=>$this->parControl
        ];
        return view('postulaciones.index',$mergeData);
    }


    public function agregar()
    { 
        $postulante= new Postulante();
        $postulantes = $postulante->obtenerPostulantesActivos();

        $convocatoria= new Convocatoria();
        $convocatorias = $convocatoria->obtenerConvocatoriasActivas();

        $mergeData = ['parControl'=>$this->parControl,'postulantes'=>$postulantes,'convocatorias'=>$convocatorias];
        return view('postulaciones.agregar',$mergeData);  
    }

    public function insertar(Request $request)
    {
        $request->validate([
            'postulante_id'=>'required|max:50',
            'convocatoria_id'=>'required|max:20',
            'activo'=>'required',
        ]);

        $postulacion = new Postulacion();
        $postulacion->postulante_id = $request->postulante_id;
        $postulacion->convocatoria_id = $request->convocatoria_id;
        $postulacion->activo = $request->activo?true:false;
        $postulacion->save();

        return redirect()->route('postulaciones.mostrar',$postulacion->id);
    }
    

    public function eliminar($id)
    {
        $postulacion = Postulacion::find($id);
        $postulacion->eliminado=true;
        $postulacion->save();
        return redirect()->route('postulaciones.index');
    }

    public function procesar(Request $request)
    {
        $convocatoria_id = $request->convocatoria_id;
        $convocatoria= new Convocatoria();
        $convocatorias = $convocatoria->obtenerConvocatoriasActivas();
        $resultados=null;
        $empleados=null;
        if($convocatoria_id >0){
            $postulacion = new Postulacion();
            $resultados= $postulacion->obtenerResultadoPostulacion($convocatoria_id);

            $empleado=new Empleado();
            $empleados= $empleado->obtenerEmpleadosActivos();
        }

        $mergeData = ['parControl'=>$this->parControl,'convocatorias'=>$convocatorias,'convocatoria_id'=>$convocatoria_id,'resultados'=>$resultados,'empleados'=>$empleados];
        return view('postulaciones.procesar',$mergeData);  
    }

    public function guardarEntrevistas(Request $request)
    {
        $postulaciones_ids=$request->postulaciones_ids;

        for ($i=0; $i < count($postulaciones_ids); $i++) { 
            $postulacion_id = $postulaciones_ids[$i];
            $entrevistaP = new EntrevistaProgramada(); 
            $entrevistaP->fecha=$request->{"fecha_$postulacion_id"};
            $entrevistaP->hora=$request->{"hora_$postulacion_id"};
            $entrevistaP->postulacion_id=$postulacion_id;
            $entrevistaP->empleado_id=$request->{"empleado_id_$postulacion_id"};
            $entrevistaP->activo=true;
            $entrevistaP->eliminado=false;
            $entrevistaP->save();
        }

        
    }
}
