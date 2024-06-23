<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $consulta = null;

        if($request->tipo_filtro == 1)
        {
            $consulta = DB::table('kardex AS kard')
                            ->join('productos AS prod','prod.id','=','kard.producto_id')
                            ->join('sucursales AS sucu','sucu.id','=','kard.sucursal_id')
                            ->select('kard.id AS id',
                                     DB::raw('DAY(kard.fecha) AS dia'),
                                     DB::raw('MONTH(kard.fecha) AS mes'),
                                     DB::raw('YEAR(kard.fecha) AS anyo'),
                                     'kard.producto_id AS producto_id',
                                     'prod.nombre AS producto',
                                     DB::raw('IFNULL((SELECT SUM(kar.nueva_cantidad) FROM kardex AS kar WHERE DATE(kar.fecha) = "'.$request->fecha.'" AND kar.producto_id = kard.producto_id AND kar.modulo_id = 1 GROUP BY kar.fecha),0) AS entradas'),
                                     DB::raw('IFNULL((SELECT SUM(kar.nueva_cantidad) FROM kardex AS kar WHERE DATE(kar.fecha) = "'.$request->fecha.'" AND kar.producto_id = kard.producto_id AND kar.modulo_id = 2 GROUP BY kar.fecha),0) AS salidas'),
                                     DB::raw('(IFNULL((SELECT SUM(kar.nueva_cantidad) FROM kardex AS kar WHERE DATE(kar.fecha) = "'.$request->fecha.'" AND kar.producto_id = kard.producto_id AND kar.modulo_id = 1 GROUP BY kar.fecha),0) -
                                               IFNULL((SELECT SUM(kar.nueva_cantidad) FROM kardex AS kar WHERE DATE(kar.fecha) = "'.$request->fecha.'" AND kar.producto_id = kard.producto_id AND kar.modulo_id = 2 GROUP BY kar.fecha),0)) AS saldo'))
                            ->whereIn('kard.sucursal_id',$request->sucursal)
                            ->whereRaw('DATE(kard.fecha) = "'.$request->fecha.'"')
                            ->groupBy('kard.fecha','kard.producto_id')
                            ->get();
           error_log(json_encode($consulta));
        }
        else if($request->tipo_filtro == 2)
        {
            $consulta = DB::table('kardex AS kard')
                            ->join('productos AS prod','prod','prod.id','=','kard.producto_id')
                            ->join('sucursales AS sucu','sucu.id','=','kard.sucursal_id')
                            ->select('kard.id AS id',
                                    'kard.fecha AS fecha',
                                    'kardex.producto_id AS producto_id',
                                    'pro.nombre AS producto',
                                    DB::raw('(SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 1) AS entradas'),
                                    DB::raw('(SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 2) AS salidas'),
                                    DB::raw('(
                                                (SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 1) -
                                                (SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 2)
                                            ) AS saldo'))
                            ->whereIn('kard.sucursal_id',$request->sucursal)
                            ->whereRaw('YEAR(kard.fecha) = ?',$request->anyo)
                            ->whereRaw('MONTH(kard.fecha) = ?', $request->mes)
                            ->groupBy(DB::rae('MONTH(kard.fecha)'))
                            ->get();
        }
        else if($request->tipo_filtro == 3)
        {
            $consulta = DB::table('kardex AS kard')
                            ->join('productos AS prod','prod','prod.id','=','kard.producto_id')
                            ->join('sucursales AS sucu','sucu.id','=','kard.sucursal_id')
                            ->select('kard.id AS id',
                                    'kard.fecha AS fecha',
                                    'kardex.producto_id AS producto_id',
                                    'pro.nombre AS producto',
                                    DB::raw('(SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 1) AS entradas'),
                                    DB::raw('(SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 2) AS salidas'),
                                    DB::raw('(
                                                (SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 1) -
                                                (SELECT IFNULL(SUM(kar.nueva_cantidad),0) FROM kardex AS kar WHERE kar.fecha = '.$request->fecha.' AND kar.producto_id = kard.id AND kar.modulo_id = 2)
                                            ) AS saldo'))
                            ->whereIn('kard.sucursal_id',$request->sucursal)
                            ->whereRaw('YEAR(kard.fecha) = ?',$request->anyo)
                            ->groupBy(DB::rae('YEAR(kard.fecha)'))
                            ->get();
        }
        return $consulta;
    }
}
