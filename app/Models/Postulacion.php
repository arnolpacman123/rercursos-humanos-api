<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Postulacion extends Model
{
    use HasFactory;
    protected $table="postulaciones";

    public function existePostulacionEnConvocatoria($postulante_id, $convocatoria_id){
        $sql = "select * from postulaciones p where p.postulante_id =$postulante_id and p.convocatoria_id =$convocatoria_id and activo =1 and eliminado =0";
        $postulaciones= DB::select($sql);
        if(count($postulaciones)>0){
            return true;
        }else{
            return false;
        }
    }


    
    public function obtenerPostulaciones($buscar='', $pagina=1)
    {
        $limite=15;
        $pagina = $pagina ? $pagina : 1;//no modificar (inicia la pagina)
        $inicio = ($pagina -1)* $limite; // define el inicio

        $filtro="";
        if ($buscar!="") {
            $filtro=" and (f.id like '%$buscar%' ) ";
        }

        $sql = "select pos.*, po.id as postulante,pe.ci as ci,pe.celular as celular, c.nombre as convocatoria
        ,concat(pe.primer_apellido,' ',coalesce(pe.segundo_apellido,''),' ',pe.nombres) as persona 
        ,po.curriculum,ca.nombre as cargo
        from postulaciones pos
        inner join postulantes po on po.id =pos.postulante_id 
        inner join personas pe on pe.id=po.id
        inner join convocatorias c on c.id = pos.convocatoria_id 
        inner join cargos ca on ca.id = c.cargo_id
        where pos.eliminado =0 $filtro order by pos.id desc limit $inicio,$limite";
        $postulaciones = DB::select($sql);

        $sqlTotal = "select count(*) as total from postulaciones pos
        inner join postulantes po on po.id =pos.postulante_id 
        inner join convocatorias c on c.id =pos.convocatoria_id 
        where pos.eliminado =0 $filtro ";
        $result = DB::select($sqlTotal); //no tocar
        $total=$totPaginas=0;
        if (count($result)>0) {
            $total=$result[0]->total;
            $totPaginas = round($total/$limite, 2)>floor($total/$limite)?floor($total/$limite)+1:floor($total/$limite);
        } //hasta aqui
        return [
            'postulaciones'=>$postulaciones,
            'total'=>$total, //NT
            'parPaginacion'=>['pagActual'=>$pagina,'totPaginas'=>$totPaginas], //NT
        ];
    }

    public static function getPostulacion($id)
    {
        $sql = "select f.*, m.id as postulante from postulaciones f
                    inner join postulantes m on m.id =f.postulante_id 
                    inner join convocatorias c on c.id =f.convocatoria_id 
                    where f.eliminado =0 and f.id=$id";
        $postulaciones = DB::select($sql);
        if (count($postulaciones)>0) {
            return $postulaciones[0];
        } else {
            return null;
        }
    }

    public function obtenerPostulacionesActivas($modulo_id)
    {
        $sql = "select id,titulo from postulaciones f where f.activo =1 and f.eliminado =0";
        $postulaciones = DB::select($sql);
        return $postulaciones;
    }
    
    public function obtenerResultadoPostulacion($convocatoria_id)
    {
        $sql = "select pos.id, p.nombres ,p.primer_apellido ,p.segundo_apellido ,p.ci,p.celular
        ,COALESCE(res.puntaje,0) as puntaje,ep.id as entrevista_programada_id,po.curriculum
            from postulaciones pos
            inner join postulantes po on po.id=pos.postulante_id 
            inner join personas p on p.id=po.id
            left join (
                select e.postulacion_id, sum(r.valor) as puntaje 
                from evaluaciones e
                inner join evaluaciones_respuestas er on er.evaluacion_id =e.id
                inner join respuestas r on r.id=er.respuesta_id 
                inner join postulaciones pos on pos.id=e.postulacion_id 
                where pos.convocatoria_id =$convocatoria_id
                group by e.postulacion_id 
            ) res on res.postulacion_id=pos.id
            left join entrevistas_programadas ep on ep.eliminado =0 and ep.postulacion_id =pos.id 
            where pos.convocatoria_id =$convocatoria_id
            order by res.puntaje desc";
        $resultados = DB::select($sql);
        return $resultados;
    }

}
