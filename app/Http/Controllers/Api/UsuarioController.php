<?php

namespace App\Http\Controllers\Api;

use App\Models\Clientes;
use App\Models\UserHasClientes;
use App\Models\usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Trabajadores;
use App\Models\UserHasTrabajadores;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Usuarios::with('userHasClientes','userHasTrabajadores.trabajador')
                        ->id($request->id)
                        ->name($request->name)
                        ->get();
        return $user;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password'   =>  'required|max:100|String',
            'name'       =>  'required|max:50|String',
            'email'      =>  'required|max:200|String'
        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "UsuarioController.php",
                    'method' => "crearUsuario"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $usuario = new Usuarios();
                $usuario->name = $request->name;
                $usuario->password = Hash::make($request->password);
                $usuario->email = $request->email;
                $usuario->save();
                $usuarioId = $usuario->id;

                if(isset($request->trabajador))
                {
                    $trabajor = new Trabajadores();
                    $trabajor->nombre = $request->name;
                    $trabajor->save();
                    $trabajadorId = $trabajor->id;

                    $userHastrabajador = new UserHasTrabajadores();
                    $userHastrabajador->trabajador_id = $trabajadorId;
                    $userHastrabajador->user_id = $usuarioId;
                    $userHastrabajador->save();
                }
                else if(isset($request->cliente))
                {
                    $cliente = new Clientes();
                    $cliente->nombre = $request->name;
                    $cliente->save();
                    $clienteId = $cliente->id;

                    $userHasClientes = new UserHasClientes();
                    $userHasClientes->cliente_id = $clienteId;
                    $userHasClientes->user_id = $usuarioId;
                    $userHasClientes->save();
                }
                $filtro = request::create('/usuario','GET',[
                    'id'     =>  $usuarioId,
                    'nombre' => null
                ]);
                DB::commit();
                return $this->index($filtro);
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }

        }
    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
