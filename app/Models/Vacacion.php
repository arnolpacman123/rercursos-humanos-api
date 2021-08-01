<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vacacion extends Model
{
    use HasFactory;
    protected $table="vacaciones";

    public function obtenerVacaciones($buscar='', $pagina=1)
    {
        $limite=15;
        $pagina = $pagina ? $pagina : 1;//no modificar (inicia la pagina)
        $inicio = ($pagina -1)* $limite; // define el inicio

        $filtro="";
        if ($buscar!="") {
            $filtro=" and (f.fecha_ini or f.fecha_fin like '%$buscar%' ) ";
        }

        $sql = "select f.*, concat(p.primer_apellido,' ',p.segundo_apellido,' ',p.nombres) as empleado 
        from vacaciones f
        inner join empleados m on m.id =f.empleado_id
        inner join personas p on p.id= m.id
        where f.eliminado =0 $filtro order by f.id desc limit $inicio,$limite";
        $vacaciones = DB::select($sql);

        $sqlTotal = "select count(*) as total from vacaciones f
        inner join empleados m on m.id =f.empleado_id 
        where f.eliminado =0 $filtro ";
        $result = DB::select($sqlTotal); //no tocar
        $total=$totPaginas=0;
        if (count($result)>0) {
            $total=$result[0]->total;
            $totPaginas = round($total/$limite, 2)>floor($total/$limite)?floor($total/$limite)+1:floor($total/$limite);
        } //hasta aqui
        return [
            'vacaciones'=>$vacaciones,
            'total'=>$total, //NT
            'parPaginacion'=>['pagActual'=>$pagina,'totPaginas'=>$totPaginas], //NT
        ];
    }
    public static function getVacacion($id)
    {
        $sql = "select f.*, concat(p.primer_apellido,' ',p.segundo_apellido,' ',p.nombres) as empleado 
                    from vacaciones f
                    inner join empleados m on m.id =f.empleado_id
                    inner join personas p on p.id= m.id
                    where f.eliminado =0 and f.id=$id";
        $vacaciones = DB::select($sql);
        if (count($vacaciones)>0) {
            return $vacaciones[0];
        } else {
            return null;
        }
    }

    public function obtenerVacacionesActivas($empleado_id)
    {
        $sql = "select id from vacaciones f where f.activo =1 and f.eliminado =0 and empleado_id=$empleado_id order by f.orden asc";
        $vacaciones = DB::select($sql);
        return $vacaciones;
    }
    /*
    public function obtenerVacacionesActivasEnPerfil($modulo_id,$perfil_id)
    {
        $sql = "select f.id,f.titulo, p.perfil_id 
                from vacaciones f 
                left join permisos p on p.funcionalidad_id = f.id and p.perfil_id =$perfil_id
                where f.activo =1 and f.eliminado =0 and f.modulo_id =$modulo_id order by f.orden asc";
        $vacaciones = DB::select($sql);
        
        return $vacaciones;
    }*/

}
