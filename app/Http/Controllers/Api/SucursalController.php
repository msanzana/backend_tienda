<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Sucursales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SucursalController extends Controller
{

    public function index(Request $request)
    {

        $sucursales = Sucursales::with('trabajadores')
                                ->id($request->id)
                                ->nombre($request->nombre)
                                ->region($request->region)
                                ->activo($request->activo)
                                ->get();
        return $sucursales;
    }

    public function store(Request $request)
    {
         try {
            DB::beginTransaction();
            $sucursal = new Sucursales();
            $sucursal->nombre = $request->nombre;
            $sucursal->region = $request->region;
            $sucursal->activo = 1;
            $sucursal->save();
            DB::commit();
            $filtro = request::create('','GET',[
                'id'     => $sucursal->id,
            ]);
            $data =  $this->index($filtro);
            return response([
                'mensaje'   => 'Sucursal creada',
                'data'      =>  $this->index($filtro)
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $sucursal = Sucursales::where('id','=',$id)->first();
            $sucursal->nombre = $request->nombre;
            $sucursal->region = $request->region;
            $sucursal->activo = $request->activo;
            $sucursal->save();
            $filtro = request::create('','GET',[
                'id'     =>  $id,
            ]);
            DB::commit();
            return response([
                'mensaje'   => 'Sucursal Modificada',
                'data'      =>  $this->index($filtro)
            ]);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $sucursales = Sucursales::where('id','=',$id)->first();
            $sucursales->activo=0;
            $sucursales->save();
            DB::commit();
            return ['mensaje'   =>  'Sucursal desactivada'];
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
