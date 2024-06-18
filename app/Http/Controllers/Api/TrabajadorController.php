<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Exception;
use App\Models\Trabajadores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TrabajadoresHasCargo;
use App\Models\TrabajadoresHasSucursales;
use Illuminate\Support\Facades\Validator;

class TrabajadorController extends Controller
{
    public function index(Request $request)
    {
        $trabajador = Trabajadores::with('sucursalesHasTrabajadores',
                                         'cargosHasTrabajadores')
                                    ->id($request->id)
                                    ->nombre($request->nombre)
                                    ->admin($request->admin)
                                    ->activo($request->activo)
                                    ->get();
        return $trabajador;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      =>  'required|max:50|String',
            'activo'      =>  'required|int',
            'sucursal'    =>  'required|array',
            'trabajadores_has_cargo'    => 'required|array'
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
                $trabajador->activo = 1;
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
                if(isset($request->trabajadores_has_cargo))
                {
                    TrabajadoresHasCargo::where('trabajador_id','=',$trabajadorId)->delete();

                    foreach($request->trabajadores_has_cargo AS $item)
                    {   error_log(json_encode($item));
                        $sucursalesTrabajadores = new TrabajadoresHasCargo();
                        $sucursalesTrabajadores->cargo_id = $item['cargo_id'];
                        $sucursalesTrabajadores->trabajador_id = $trabajadorId;
                        $sucursalesTrabajadores->save();
                    }                    
                }
                $filtro = request::create('/','GET',[
                    'id'     =>  $trabajadorId,
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'Trabajador creado',
                    'data'      =>  $this->index($filtro)
                ],200);
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        }
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      =>  'required|max:50|String',
            'activo'      =>  'required|int,',
            'sucursal'    =>  'required|array',
            'trabajadores_has_cargo'    => 'required|array'

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
                $trabajador = Trabajadores::where('id','=',$id)->first();
                $trabajador->nombre = $request->nombre;
                $trabajador->admin = $request->admin;
                $trabajador->activo = $request->activo;
                $trabajador->save();
                if(isset($request->sucursal))
                {
                    TrabajadoresHasSucursales::where('trabajador_id','=',$id)->delete();

                    foreach($request->sucursal AS $item)
                    {   error_log(json_encode($item));
                        $sucursalesTrabajadores = new TrabajadoresHasSucursales();
                        $sucursalesTrabajadores->sucursal_id = $item['sucursal_id'];
                        $sucursalesTrabajadores->trabajador_id = $id;
                        $sucursalesTrabajadores->save();
                    }
                }
                if(isset($request->trabajadores_has_cargo))
                {
                    TrabajadoresHasCargo::where('trabajador_id','=',$id)->delete();

                    foreach($request->trabajadores_has_cargo AS $item)
                    {   error_log(json_encode($item));
                        $sucursalesTrabajadores = new TrabajadoresHasCargo();
                        $sucursalesTrabajadores->cargo_id = $item['cargo_id'];
                        $sucursalesTrabajadores->trabajador_id = $id;
                        $sucursalesTrabajadores->save();
                    }                    
                }
                $filtro = request::create('/trabajador','GET',[
                    'id'     =>  $id,
                    'nombre' => null,
                    'admin'  => null        
                ]);
                DB::commit();
                return response([
                    'mensaje'   => 'trabaador modificado',
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
        try {
            DB::beginTransaction();
            $trabajador=Trabajadores::where('id','=',$id)->first();
            $trabajador->activo = 0;
            $trabajador->save();
            DB::commit();
            return [
                'mensaje' =>'trabajador desactivado'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
