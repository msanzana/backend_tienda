<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Cargos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CargosController extends Controller
{

    public function index(Request $request)
    {
        $cargo = Cargos::id($request->id)
                    ->nombre($request->nombre)
                    ->get();
        return $cargo;
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
                    'file' => "CargoController.php",
                    'method' => "crearCargo"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $cargo = new Cargos();
                $cargo->nombre = $request->nombre;
                $cargo->save();
                $cargoId = $cargo->id;
                $filtro = request::create('','GET',[
                    'id'     =>  $cargoId,
                    'nombre' => null,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Cargo creado',
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
                    'file' => "CargoController.php",
                    'method' => "moificarCargo"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $cargo = Cargos::where('id','=',$id)->first();
                $cargo->nombre = $request->nombre;
                $cargo->save();
                $cargoId = $cargo->id;
                $filtro = request::create('','GET',[
                    'id'     =>  $cargoId,
                    'nombre' => null,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Cargo modificado',
                    'data'      =>  $this->index($filtro)
                ],200);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }
}
