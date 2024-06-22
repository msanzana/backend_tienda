<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventarioController extends Controller
{
    public function index(Request $request)
    {

        if($request->tipo_filtro == 1)
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
                            ->whereIN('kard.sucursal_id',$request->sucursal)
                            ->groupBy('kard.fecha')
                            ->get();

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
