<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bono extends Model
{
    use HasFactory;
    protected $table="bonos";

    public function obtenerBonos($buscar='', $pagina=1)
    {
        $limite=15;
        $filtro="";
        if ($buscar!="") {
            $filtro=" and (m.fecha like '%$buscar%' or m.motivo like '%$buscar%')";
        }
        $pagina = $pagina ? $pagina : 1;
        $inicio =  ($pagina -1)* $limite;
        $sql = "select * from bonos m
        

        where m.eliminado=0 $filtro order by id asc limit $inicio,$limite ";
        $bonos = DB::select($sql);

        $sqlTotal = "select count(*) as total from bonos m where m.eliminado=0 $filtro";
        $result = DB::select($sqlTotal);
        $total=$totPaginas=0;
        if (count($result)>0) {
            $total=$result[0]->total;
            $totPaginas = round($total/$limite, 2)>floor($total/$limite)?floor($total/$limite)+1:floor($total/$limite);
        }
        return [
            'bonos'=>$bonos,
            'total'=>$total,
            'parPaginacion'=>['pagActual'=>$pagina,'totPaginas'=>$totPaginas]
        ];
    }
    public function obtenerbonosActivos()
    {
        $sql = "select id,monto from bonos a where a.eliminado =0 and activo=1 order by orden asc";
        $bonos = DB::select($sql);
        return $bonos;
    }
}
