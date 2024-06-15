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
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $sucursal = Sucursales::where('id','=',$id);
            $sucursal->nombre = $request->nombre;
            $sucursal->region = $request->region;
            $sucursal->activo = $request->activo;
            $sucursal->save();
            DB::commit();
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
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
