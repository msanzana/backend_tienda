<?php

namespace App\Http\Controllers\Api;

use App\Models\Clientes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    public function index(request $object)
    {
        $clientes = Clientes::id($object->id)
                         ->nombre($object->nombre)
                         ->get();
        return $clientes;
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
