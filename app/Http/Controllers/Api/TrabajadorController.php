<?php

namespace App\Http\Controllers\Api;

use stdClass;
use App\Models\Trabajadores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TrabajadoresHasSucursales;
use Illuminate\Support\Facades\Validator;

class TrabajadorController extends Controller
{
    public function index(Request $request)
    {
        $trabajador = Trabajadores::with('sucursalesHasTrabajadores')
                                    ->id($request->id)
                                    ->nombre($request->nombre)
                                    ->get();
        return $trabajador;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      =>  'required|max:50|String',
            'sucursal'    =>  'required|array'
        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "TrabajadorController.php",
                    'method' => "crearUsuario"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $trabajador = new Trabajadores();
                $trabajador->nombre = $request->nombre;
                $trabajador->save();
                $trabajadorId = $trabajador->id;
                if(isset($request->sucursal))
                {
                    foreach($request->sucursal AS $item)
                    {   error_log(json_encode($item));
                        $sucursalesTrabajadores = new TrabajadoresHasSucursales();
                        $sucursalesTrabajadores->sucursal_id = $item['sucursal_id'];
                        $sucursalesTrabajadores->trabajador_id = $trabajadorId;
                        $sucursalesTrabajadores->save();
                    }
                }
                $filtro = request::create('/trabajador','GET',[
                    'id'     =>  $trabajadorId,
                    'nombre' => null
                ]);
                DB::commit();
                return $this->index((object)$filtro);
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }    

        }
    }


    public function update(Request $request, string $id)
    {
        //
    }
    public function destroy(string $id)
    {
        //
    }
}
