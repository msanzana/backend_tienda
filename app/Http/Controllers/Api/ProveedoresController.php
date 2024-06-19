<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = Proveedores::id($request->id)
                                ->nombre($request->nombre)
                                ->activo($request->activo)
                                ->get();
        return $proveedores; 
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      =>  'required|max:50|String',
            'activo'      =>  'required|int',

        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "ProveedoresController.php",
                    'method' => "crearProveedor"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $proveedor = new Proveedores();
                $proveedor->nombre = $request->nombre;
                $proveedor->activo = 1;
                $proveedor->save();
                $proveedorId = $proveedor->id;
                $filtro = request::create('','GET',[
                    'id'     =>  $proveedorId,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Proveedor creado',
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

        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "ProveedoresController.php",
                    'method' => "crearProveedor"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $proveedor = Proveedores::where('id','=',$id)->first();
                $proveedor->nombre = $request->nombre;
                $proveedor->activo = $request->activo;
                $proveedor->save();
                $proveedorId = $id;
                $filtro = request::create('','GET',[
                    'id'     =>  $proveedorId,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Proveedor modificado',
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
            $proveedor = Proveedores::where('id','=',$id)->first();
            $proveedor->activo =0;
            $proveedor->save();
            DB::commit();
            return [
                'mensaje' =>'Proveedor desactivado'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
