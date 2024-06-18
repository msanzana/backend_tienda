<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index(request $object)
    {
        $clientes = Clientes::id($object->id)
                         ->nombre($object->nombre)
                         ->activo($object->activo)
                         ->get();
        return $clientes;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      =>  'required|max:50|String',
        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "ClientesController.php",
                    'method' => "crearCliente"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $cliente = new Clientes();
                $cliente->nombre = $request->nombre;
                $cliente->actico = 1;
                $cliente->save();
                $clienteId = $cliente->id;
                $filtro = request::create('','GET',[
                    'id'     =>  $clienteId,
                    'nombre' => null,

                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Cliente creado',
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
        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "ClientesController.php",
                    'method' => "ModificarCliente"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $cliente = Clientes::where('id','=',$id)->first();
                $cliente->nombre = $request->nombre;
                $cliente->actico = $request->activo;
                $cliente->save();
                $filtro = request::create('','GET',[
                    'id'     =>  $id,
                    'nombre' => null,

                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Cliente modificado',
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
            $cliente = Clientes::where('id','=',$id)->first();
            $cliente->activo =0;
            $cliente->save();
            DB::commit();
            return [
                'mensaje' =>'Cliente desactivado'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
