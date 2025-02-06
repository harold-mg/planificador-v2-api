<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    // Listar todos los vehículos
    public function index()
    {
        $vehiculos = Vehiculo::all();
        return response()->json($vehiculos);
    }

    // Registrar un nuevo vehículo
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'placa' => 'required|unique:vehiculos',
            'modelo' => 'required',
            'disponible' => 'boolean',
        ]);

        $vehiculo = Vehiculo::create($validatedData);
        return response()->json($vehiculo, 201);
    }

    // Mostrar un vehículo específico
    public function show($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        return response()->json($vehiculo);
    }

    // Actualizar un vehículo existente
    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        $validatedData = $request->validate([
            'placa' => 'required|unique:vehiculos,placa,' . $id,
            'modelo' => 'required',
            'disponible' => 'boolean',
        ]);

        $vehiculo->update($validatedData);
        return response()->json($vehiculo);
    }

    // Eliminar un vehículo
    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->delete();

        return response()->json(null, 204);
    }
    public function disponibles()
    {
        $vehiculos = Vehiculo::where('disponible', true)->get();
        return response()->json($vehiculos);
    }
}