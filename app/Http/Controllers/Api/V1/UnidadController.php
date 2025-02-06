<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    // Método para crear una nueva unidad
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:unidades|max:255',
        ]);

        Unidad::create([
            'nombre' => $validated['nombre'],
        ]);

        return response()->json(['message' => 'Unidad creada correctamente']);
    }

    // Método para obtener todas las unidades
    public function index()
    {
        return Unidad::all();
    }

    // Método para obtener una unidad específica
    public function show($id)
    {
        return Unidad::findOrFail($id);
    }
    // Método para actualizar una unidad
    public function update(Request $request, $id)
    {
        $unidad = Unidad::findOrFail($id);

        // Validar los datos de entrada
        $validated = $request->validate([
            'nombre' => 'required|unique:unidades,nombre,' . $unidad->id . '|max:255', // Asegurar que el nombre es único excepto para esta unidad
        ]);

        // Actualizar la unidad con los datos validados
        $unidad->update([
            'nombre' => $validated['nombre'],
        ]);

        return response()->json(['message' => 'Unidad actualizada correctamente']);
    }

    // Método para eliminar una unidad
    public function destroy($id)
    {
        $unidad = Unidad::findOrFail($id);

        // Eliminar la unidad
        $unidad->delete();

        return response()->json(['message' => 'Unidad eliminada correctamente']);
    }
}