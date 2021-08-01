<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Convocatoria extends Model
{
    use HasFactory;
    protected $table="convocatorias";

    public function obtenerConvocatoriaPorCodigo($codigoConvocatoria)
    {
        $sql = "select * from convocatorias c where c.codigo ='$codigoConvocatoria' and activo=1 and eliminado=0 limit 1";
        $convocatorias = DB::select($sql);
        if(count($convocatorias)>0){
            return $convocatorias[0];
        }
    }


    public function obtenerConvocatorias($buscar='', $pagina=1)
    {
        $limite=15;
        $pagina = $pagina ? $pagina : 1;//no modificar (inicia la pagina)
        $inicio = ($pagina -1)* $limite; // define el inicio

        $filtro="";
        if ($buscar!="") {
            $filtro=" and (f.nombre like '%$buscar%' ) ";
        }

        $sql = "select f.*, m.nombre as cargo from convocatorias f
        inner join cargos m on m.id =f.cargo_id 
        where f.eliminado =0 $filtro order by f.id desc limit $inicio,$limite";
        $convocatorias = DB::select($sql);

        $sqlTotal = "select count(*) as total from convocatorias f
        inner join cargos m on m.id =f.cargo_id 
        where f.eliminado =0 $filtro ";
        $result = DB::select($sqlTotal); //no tocar
        $total=$totPaginas=0;
        if (count($result)>0) {
            $total=$result[0]->total;
            $totPaginas = round($total/$limite, 2)>floor($total/$limite)?floor($total/$limite)+1:floor($total/$limite);
        } //hasta aqui
        return [
            'convocatorias'=>$convocatorias,
            'total'=>$total, //NT
            'parPaginacion'=>['pagActual'=>$pagina,'totPaginas'=>$totPaginas], //NT
        ];
    }

    public static function getConvocatoria($id)
    {
        $sql = "select f.*, m.nombre as cargo from convocatorias f
                    inner join cargos m on m.id =f.cargo_id 
                    where f.eliminado =0 and f.id=$id";
        $convocatorias = DB::select($sql);
        if (count($convocatorias)>0) {
            return $convocatorias[0];
        } else {
            return null;
        }
    }

    public function obtenerConvocatoriasActivas(){
        $sql = "select id,nombre from convocatorias c where activo =1 and eliminado =0";
        $convocatorias = DB::select($sql);
        return $convocatorias;
    }
}
