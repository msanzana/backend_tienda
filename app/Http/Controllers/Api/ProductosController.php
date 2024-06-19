<?php

namespace App\Http\Controllers\Api;

use App\Models\ProveedoresHasProductos;
use Exception;
use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SucursalesHasProductos;
use Illuminate\Support\Facades\Validator;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        $productos = Productos::with('proveedoresHasProductos',
                                     'sucursalesHasProductos')
                              ->id($request->id)
                              ->nombre($request->nombre)
                              ->activo($request->activo)
                              ->sucursalId($request->sucursal_id)
                              ->proveedorId($request->proveedor_id)
                              ->get();
        return $productos;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      =>  'required|max:50|String',
            'activo'      =>  'required|int',
            'proveedores_has_productos' => 'required|array',
            'sucursales_has_productos'  => 'required|array'

        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "ProductosController.php",
                    'method' => "crearProducto"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $producto = new Productos();
                $producto->nombre = $request->nombre;
                $producto->precio_venta = $request->precio_venta;
                $producto->URL_imagen = $request->URL_imagen;
                $producto->activo = 1;
                $producto->save();
                $productoId = $producto->id;
                if(isset($request->proveedores_has_productos))
                {
                    foreach($request->proveedores_has_productos AS $item)
                    {
                        $proveedoresHasProductos = new ProveedoresHasProductos();
                        $proveedoresHasProductos->proveedor_id = $item['proveedor_id'];
                        $proveedoresHasProductos->producto_id = $productoId ;
                        $proveedoresHasProductos->save();
                    }
                }
                if(isset($request->sucursales_has_productos))
                {
                    foreach($request->sucursales_has_productos AS $item)
                    {
                        $sucursalesHasProductos = new SucursalesHasProductos();
                        $sucursalesHasProductos->sucursal_id = $item['sucursal_id'];
                        $sucursalesHasProductos->producto_id = $productoId ;
                        $sucursalesHasProductos->stock = 0;    
                        $sucursalesHasProductos->save();
                    }
                }
                $filtro = request::create('','GET',[
                    'id'     =>  $productoId,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Producto creado',
                    'data'      =>  $this->index($filtro)
                ],200);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      =>  'required|max:50|String',
            'activo'      =>  'required|int',
            'proveedores_has_productos' => 'required|array',
            'sucursales_has_productos'  => 'required|array'

        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "ProductosController.php",
                    'method' => "editarProducto"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $producto = Productos::where('id','=',$id)->first();
                $producto->nombre = $request->nombre;
                $producto->precio_venta = $request->precio_venta;
                $producto->URL_imagen = $request->URL_imagen;
                $producto->activo = $request->activo;
                $producto->save();
                $productoId = $id;
                if(isset($request->proveedores_has_productos))
                {
                    ProveedoresHasProductos::where('producto_id','=',$productoId)->delete();
                    foreach($request->proveedores_has_productos AS $item)
                    {
                        $proveedoresHasProductos = new ProveedoresHasProductos();
                        $proveedoresHasProductos->proveedor_id = $item['proveedor_id'];
                        $proveedoresHasProductos->producto_id = $productoId ;
                        $proveedoresHasProductos->save();
                    }
                }
                if(isset($request->sucursales_has_productos))
                {
                    SucursalesHasProductos::where('producto_id','=', $productoId)->delete();    
                    foreach($request->sucursales_has_productos AS $item)
                    {
                        $sucursalesHasProductos = new SucursalesHasProductos();
                        $sucursalesHasProductos->sucursal_id = $item['sucursal_id'];
                        $sucursalesHasProductos->producto_id = $productoId ;
                        $sucursalesHasProductos->stock = 0;    
                        $sucursalesHasProductos->save();
                    }
                }
                $filtro = request::create('','GET',[
                    'id'     =>  $productoId,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Producto modificado',
                    'data'      =>  $this->index($filtro)
                ],200);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $produco = Productos::where('id', '=', $id)->first();
            $produco->activo = 0;
            $produco->save();
            DB::commit();
            return [
                'mensaje' =>'Producto desactivado'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
