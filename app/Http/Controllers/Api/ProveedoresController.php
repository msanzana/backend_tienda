<?php

namespace App\Http\Controllers\Api;

use App\Models\Proveedores;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProveedoresController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = Proveedores::id($request->id)
                                ->nombre($request->nombre)
                                ->get();
        return $proveedores; 
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
