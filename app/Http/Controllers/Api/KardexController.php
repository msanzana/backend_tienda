<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kardex;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $kardex = Kardex::with('modulo',
                              'producto',
                              'sucursal')
                        ->id($request->id)
                        ->fecha($request->fecha)
                        ->moduloId($request->modulo_id)
                        ->correlativo($request->correlativo)
                        ->productoId($request->producto_id)
                        ->sucursalId($request->sucursal_id)
                        ->gety();
         return $kardex;     
    }

}
