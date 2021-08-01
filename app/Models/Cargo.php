<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cargo extends Model
{
    use HasFactory;
    protected $table="cargos";

    public function obtenerCargos($buscar='', $pagina=1)
    {
        $limite=15;
        $pagina = $pagina ? $pagina : 1;//no modificar (inicia la pagina)
        $inicio = ($pagina -1)* $limite; // define el inicio

        $filtro="";
        if ($buscar!="") {
            $filtro=" and (c.nombre like '%$buscar%' ) ";
        }

        $sql = "select c.*, d.nombre as departamento from cargos c
        inner join departamentos d on d.id =c.departamento_id 
        where c.eliminado =0 $filtro order by c.id desc limit $inicio,$limite";
        $cargos = DB::select($sql);

        $sqlTotal = "select count(*) as total from cargos c
        inner join departamentos d on d.id =c.departamento_id 
        where c.eliminado =0 $filtro ";
        $result = DB::select($sqlTotal); //no tocar
        $total=$totPaginas=0;
        if (count($result)>0) {
            $total=$result[0]->total;
            $totPaginas = round($total/$limite, 2)>floor($total/$limite)?floor($total/$limite)+1:floor($total/$limite);
        } //hasta aqui
        return [
            'cargos'=>$cargos,
            'total'=>$total, //NT
            'parPaginacion'=>['pagActual'=>$pagina,'totPaginas'=>$totPaginas], //NT
        ];
    }
    public static function getCargo($id)
    {
        $sql = "select c.*, d.nombre as departamento from cargos c
                    inner join departamentos d on d.id =c.departamento_id 
                    where c.eliminado =0 and c.id=$id";
        $cargos = DB::select($sql);
        if (count($cargos)>0) {
            return $cargos[0];
        } else {
            return null;
        }
    }

    public function obtenerCargosActivos()
    {
        $sql = "select id,nombre 
        from cargos c 
        where c.eliminado =0 and activo=1 order by nombre asc";
        $cargos = DB::select($sql);
        return $cargos;
    }
 
}
