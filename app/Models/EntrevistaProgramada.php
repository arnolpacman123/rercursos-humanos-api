<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EntrevistaProgramada extends Model
{
    use HasFactory;
    protected $table="entrevistas_programadas";

    public function obtenerEntrevistasProgramadas($buscar='', $pagina=1)
    {
        $limite=15;
        $pagina = $pagina ? $pagina : 1;//no modificar (inicia la pagina)
        $inicio = ($pagina -1)* $limite; // define el inicio

        $filtro="";
        if ($buscar!="") {
            $filtro=" and (f.id like '%$buscar%' ) ";
        }
        
        $sql = "select eprog.*
        ,po.id as postulante,pe.ci as ci,pe.celular as celular, c.nombre as convocatoria
        ,concat(pe.primer_apellido,' ',coalesce(pe.segundo_apellido,''),' ',pe.nombres) as persona 
        ,po.curriculum,ca.nombre as cargo
        ,concat(pem.primer_apellido,' ',coalesce(pem.segundo_apellido,''),' ',pem.nombres) as entrevistador
        from entrevistas_programadas eprog
        inner join postulaciones pos on pos.id =eprog.postulacion_id 
        inner join empleados em on em.id=eprog.empleado_id
        inner join personas pem on pem.id=em.id
        inner join postulantes po on po.id =pos.postulante_id 
        inner join personas pe on pe.id=po.id
        inner join convocatorias c on c.id = pos.convocatoria_id 
        inner join cargos ca on ca.id = c.cargo_id
        where eprog.eliminado =0 $filtro order by eprog.id desc limit $inicio,$limite";
        $entrevistas_programadas = DB::select($sql);

        $sqlTotal = "select count(*) as total from entrevistas_programadas eprog
        inner join postulaciones m on m.id =eprog.postulacion_id 
        where eprog.eliminado =0 $filtro ";
        $result = DB::select($sqlTotal); //no tocar
        $total=$totPaginas=0;
        if (count($result)>0) {
            $total=$result[0]->total;
            $totPaginas = round($total/$limite, 2)>floor($total/$limite)?floor($total/$limite)+1:floor($total/$limite);
        } //hasta aqui
        return [
            'entrevistas_programadas'=>$entrevistas_programadas,
            'total'=>$total, //NT
            'parPaginacion'=>['pagActual'=>$pagina,'totPaginas'=>$totPaginas], //NT
        ];
    }
    public static function getEntrevistaProgramada($id)
    {
        $sql = "select f.*, m.id as postulacion from entrevistas_programadas f
                    inner join postulaciones m on m.id =f.postulacion_id 
                    where f.eliminado =0 and f.id=$id";
        $entrevistas_programadas = DB::select($sql);
        if (count($entrevistas_programadas)>0) {
            return $entrevistas_programadas[0];
        } else {
            return null;
        }
    }

    public function obtenerEntrevistasPorgramadasActivas()
    {
        $sql = "select id,id from entrevistas_programadas f where f.activo =1 and f.eliminado =0";
        $entrevistas_programadas = DB::select($sql);
        return $entrevistas_programadas;
    }


}
