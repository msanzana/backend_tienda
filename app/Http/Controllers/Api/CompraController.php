<?php

namespace App\Http\Controllers\Api;

use App\Models\Compras;
use App\Models\SucursalesHasProductos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ComprasDetalle;
use App\Models\Kardex;
use Illuminate\Support\Facades\Validator;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $compra = Compras::with('trabajador',
                                'sucursal',
                                'proveedor',
                                'detalle.producto')
                         ->id($request->id)
                         ->fecha($request->fecha)
                         ->trabajadorId($request->trabajador_id)
                         ->sucursalId($request->sucursal_id)
                         ->total($request->total)
                         ->get();
        return $compra;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha'             =>  'required|String',
            'trabajador_id'     =>  'required|int',
            'proveedor_id'      =>  'required|int',
            'sucursal_id'       =>  'required|int',
            'compras_detalle'   =>  'required|array'

        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "CompraController.php",
                    'method' => "crearCompra"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $compra = new Compras();
                $compra->fecha = $request->fecha;
                $compra->trabajador_id = $request->trabajador_id;
                $compra->proveedor_id = $request->proveedor_id;
                $compra->sucursal_id = $request->sucursal_id;
                $compra->total = $request->total;
                $compra->save();
                $compraId = $compra->id;
                if(isset($request->compras_detalle))
                {
                    foreach($request->compras_detalle AS $item)
                    {
                        $cantidadActual = 0;
                        $cantidadNueva = 0;
                        $compraDetalle = new ComprasDetalle();
                        $compraDetalle->compra_id = $compraId;
                        $compraDetalle->producto_id = $item['producto_id'];
                        $compraDetalle->cantidad = $item['cantidad'];
                        $compraDetalle->total = $item['total'];
                        $compraDetalle->save();

                        // obtener stock actual producto
                        $stockProducto = SucursalesHasProductos::where('sucursal_id','=',$request->sucursal_id)
                                                               ->where('producto_id','=',$item['producto_id'])
                                                               ->first();

                        if(is_null($stockProducto))
                        {
                            $cantidadActual =0;
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
                            $stockProducto->stock +=  $item['cantidad'];
                            $stockProducto->save();
                            $cantidadActual = $stockProducto->stock;
                            $cantidadNueva = $stockProducto->stock + $item['cantidad'];
                        }
                        // Kardex Entrada
                        $kardex = new Kardex();
                        $kardex->fecha = $request->fecha;
                        $kardex->modulo_id = 1; // compra
                        $kardex->correlativo = $compraId;
                        $kardex->producto_id = $item['producto_id'];
                        $kardex->sucursal_id = $request->sucursal_id;
                        $kardex->cantidad_actual = $cantidadActual;
                        $kardex->nueva_cantidad = $cantidadNueva;

                        $kardex->save();
                    }
                }
                $filtro = request::create('','GET',[
                    'id'     =>  $compraId,
                    'nombre' => null,
                    'admin'  => null
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Compra Ingresada',
                    'data'      =>  $this->index($filtro)
                ],200);
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }

        }
    }
    public function destroy(string $id)
    {

    }
}
