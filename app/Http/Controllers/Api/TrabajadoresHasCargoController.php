<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TrabajadoresHasCargo;
use Illuminate\Support\Facades\Validator;

class TrabajadoresHasCargoController extends Controller
{

    public function index(Request $request)
    {
        $trabajadoresHasCargo = TrabajadoresHasCargo::with('trabajador',
                                                           'cargo')
                                                     ->trabajadorId($request->trabajador_id)
                                                     ->cargoId($request->cargo_id)
                                                     ->get();
        return $trabajadoresHasCargo; 
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trabajador_id' =>  'required|int',
            'cargo_id'      =>  'required|int',
        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "TrabajadoresHsaCargoController.php",
                    'method' => "crearTrabajadorHasCargo"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $tabajadorHasCargo = new TrabajadoresHasCargo();
                $tabajadorHasCargo->trabajador_id = $request->trabajador_id;
                $tabajadorHasCargo->cargo_id = $request->cargo_id;
                $tabajadorHasCargo->save();
                $trabajadorId = $tabajadorHasCargo->id;
                $filtro = request::create('','GET',[
                    'trabajador_id'     =>  $trabajadorId
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Trabajador Has Cargo creado',
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
            'trabajador_id' =>  'required|int',
            'cargo_id'      =>  'required|int',
        ]);
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors()->all(),
                    'file' => "TrabajadoresHasCargoController.php",
                    'method' => "editarTrabajadorHasCargo"
                ],
                500
            )
                ->header('Content-Type', 'application/json');
        } else {
            try {
                DB::beginTransaction();
                $tabajadorHasCargo = TrabajadoresHasCargo::where('trabajador_id',$id)->first();
                $tabajadorHasCargo->trabajador_id = $request->trabajador_id;
                $tabajadorHasCargo->cargo_id = $request->cargo_id;
                $tabajadorHasCargo->save();
                $trabajadorId = $id;
                $filtro = request::create('','GET',[
                    'trabajador_id'     =>  $trabajadorId
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Trabajador Has Cargo modificado',
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

    }
}
