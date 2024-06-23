<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Kardex;
use App\Models\Ventas;
use Illuminate\Http\Request;
use App\Models\VentasDetalle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SucursalesHasProductos;
use Illuminate\Support\Facades\Validator;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $ventas = Ventas::with('cliente',
                               'sucursal',
                               'ventaDetalle')
                    ->id($request->id)
                    ->fecha($request->fecha)
                    ->clienteId($request->cliente_id)
                    ->sucursalId($request->scuursal_id)
                    ->total($request->total)
                    ->get();
        return $ventas;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha'            =>  'required|String',
            'cliente_id'       =>  'required|int',
            'sucursal_id'      =>  'required|int',
            'total'            =>  'required|int',
            'ventas_detalle'   =>  'required|array'

        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "VentaController.php",
                    'method' => "crearVenta"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $venta = new Ventas();
                $venta->fecha = $request->fecha;
                $venta->cliente_id = $request->cliente_id;
                $venta->sucursal_id = $request->sucursal_id;
                $venta->total = $request->total;
                $venta->save();
                $ventaId = $venta->id;
                if(isset($request->ventas_detalle))
                {
                    foreach($request->ventas_detalle AS $item)
                    {
                        $cantidadActual = 0;
                        $cantidadNueva = 0;
                        $stockProducto = null;
                        $ventaDetalle = new VentasDetalle();
                        $ventaDetalle->venta_id = $ventaId;
                        $ventaDetalle->producto_id = $item['producto_id'];
                        $ventaDetalle->cantidad = $item['cantidad'];
                        $ventaDetalle->total = $item['total'];
                        $ventaDetalle->save();

                        // obtener stock actual producto
                        $stockProducto = SucursalesHasProductos::where('sucursal_id','=',$request->sucursal_id)
                                                               ->where('producto_id','=',$item['producto_id'])
                                                               ->first();

                        if(is_null($stockProducto))
                        {
                            $cantidadActual = 0;
                            $cantidadNueva = $item['cantidad'];
                            $nuevoStock = new SucursalesHasProductos();
                            $nuevoStock->producto_id = $item['producto_id'];
                            $nuevoStock->sucursal_id = $request->sucursal_id;
                            $nuevoStock->stock =  $item['cantidad'];
                            $nuevoStock->save();
                        }
                        else
                        {
                            $stockProducto = SucursalesHasProductos::where('sucursal_id','=',$request->sucursal_id)
                                                                    ->where('producto_id','=',$item['producto_id'])
                                                                    ->first();
                            $cantidadActual = $stockProducto->stock;
                            $cantidadNueva = $stockProducto->stock - $item['cantidad'];
                            $stockProducto->stock -=  $item['cantidad'];
                            $stockProducto->save();
                        }
                        // Kardex Entrada
                        $kardex = new Kardex();
                        $kardex->fecha = $request->fecha;
                        $kardex->modulo_id = 2; // compra
                        $kardex->correlativo = $ventaId;
                        $kardex->producto_id = $item['producto_id'];
                        $kardex->sucursal_id = $request->sucursal_id;
                        $kardex->cantidad_actual = $cantidadActual;
                        $kardex->nueva_cantidad = $cantidadNueva;

                        $kardex->save();
                    }
                }
                $filtro = request::create('','GET',[
                    'id'     =>  $ventaId,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Venta Ingresada',
                    'data'      =>  $this->index($filtro)
                ],200);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

}
