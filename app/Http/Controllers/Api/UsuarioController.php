<?php

namespace App\Http\Controllers\Api;

use App\Models\usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'user'       =>  'required|max:15|String',
            'password'   =>  'required|max:100|String',
            'name'       =>  'required|max:50|String',
            'email'      =>  'required|max:200|String',
            'sucursales' =>  'present|array'
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
                $usuario->user = $request->user;
                $usuario->password = Hash::make($request->password);
                $usuario->email = $request->email;
                $usuario->save();
                DB::commit();
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
