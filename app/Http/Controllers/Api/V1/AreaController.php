<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:areas|max:255',
            'unidad_id' => 'required|exists:unidades,id',
        ]);

        $area = Area::create([
            'nombre' => $request->nombre,
            'unidad_id' => $request->unidad_id,
        ]);

        return response()->json(['message' => 'Área creada con éxito', 'area' => $area], 201);
    }

    // Método para obtener todas las areas
    public function index()
    {
        $areas = Area::all();
        return response()->json($areas);
    }
    // Método para obtener una area específica
    public function show($id)
    {
        return Area::findOrFail($id);
    }
    public function getAreasPorUnidad($unidadId)
    {
        $areas = Area::where('unidad_id', $unidadId)->get();
        return response()->json($areas);
    }
    // Método para actualizar un área
    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);

        // Validar los datos de entrada
        $validated = $request->validate([
            'nombre' => 'required|unique:areas,nombre,' . $area->id . '|max:255', // El nombre debe ser único excepto para esta área
        ]);

        // Actualizar el área con los datos validados
        $area->update([
            'nombre' => $validated['nombre'],
        ]);

        return response()->json(['message' => 'Área actualizada correctamente']);
    }

    // Método para eliminar un área
    public function destroy($id)
    {
        $area = Area::findOrFail($id);

        // Eliminar el área
        $area->delete();

        return response()->json(['message' => 'Área eliminada correctamente']);
    }
}
