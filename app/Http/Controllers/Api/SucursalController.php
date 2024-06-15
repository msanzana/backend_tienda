<?php

namespace App\Http\Controllers\Api;

use App\Models\Sucursales;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SucursalController extends Controller
{

    public function index(Request $request)
    {
        $sucursales = Sucursales::where('trabajadores')
                                ->id($request->id)
                                ->nombre($request->nombre)
                                ->region($request->region)
                                ->get();
        return $sucursales;
    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
