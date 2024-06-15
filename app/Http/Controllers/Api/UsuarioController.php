<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Clientes;
use App\Models\usuarios;
use App\Models\Trabajadores;
use Illuminate\Http\Request;
use App\Models\UserHasClientes;
use Illuminate\Support\Facades\DB;
use App\Models\UserHasTrabajadores;
use App\Http\Controllers\Controller;
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
                    $trabajor->admin =0;
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
                return response([
                    'mensaje'   =>  'Usuario creado con exito',
                    'data'      =>  $this->index($filtro)
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }

        }
    }

    public function update(Request $request, string $id)
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
                $usuario = Usuarios::where('id','=',$id) ;
                $usuario->name = $request->name;
                $usuario->password = Hash::make($request->password);
                $usuario->email = $request->email;
                $usuario->save();

                if(isset($request->trabajador))
                {
                    $trabajor = Trabajadores::where('user_id','=',$id)->first();
                    $trabajor->nombre = $request->name;
                    $trabajor->admin =0;
                    $trabajor->save();
                    $trabajadorId = $trabajor->id;

                    $userHastrabajador = UserHasTrabajadores::where('trabajador_id','=',$trabajadorId)->first();
                    $userHastrabajador->trabajador_id = $trabajadorId;
                    $userHastrabajador->user_id = $id;
                    $userHastrabajador->save();
                }
                else if(isset($request->cliente))
                {
                    $cliente = Clientes::where('user_id','=',$id)->first();
                    $cliente->nombre = $request->name;
                    $cliente->save();
                    $clienteId = $cliente->id;

                    $userHasClientes = UserHasClientes::where('cliente_id','=',$clienteId)->first();
                    $userHasClientes->cliente_id = $clienteId;
                    $userHasClientes->user_id = $id;
                    $userHasClientes->save();
                }
                $filtro = request::create('/usuario','GET',[
                    'id'     =>  $id,
                    'nombre' => null
                ]);
                DB::commit();
                return response([
                    'mensaje'   =>  'Usuario modificado con exito',
                    'data'      =>  $this->index($filtro)
                ]);
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

            $trabajadores=Trabajadores::where('user_id','=',$id)->first();
            if(!empty($trabajadores))
            {
                $trabajadorId = $trabajadores->id;
                UserHasTrabajadores::where('trabajador_id','=',$trabajadorId)->delete();
            }
            $trabajadores->delete();

            $cliente = Clientes::where('user_id','=',$id)->first();
            if(!empty($cliente))
            {
                $clienteId = $cliente->id;
                UserHasClientes::where('cliente_id','=',$clienteId)->first();
            }
            $cliente->delete();
            Usuarios::where('id','=',$id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
