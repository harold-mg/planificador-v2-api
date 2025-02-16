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
            'prioridad' => 0,
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
            'prioridad' => 'integer|min:0',
        ]);

        // Actualizar la unidad con los datos validados
        $unidad->update([
            'nombre' => $validated['nombre'],
            'prioridad' => $request->has('prioridad') ? $validated['prioridad'] : $unidad->prioridad,

        ]);

        return response()->json(['message' => 'Unidad actualizada correctamente']);
    }
    public function getUnidadesAreas()
    {
        $areas = Unidad::with(['areas'])->get();
        return response()->json($areas);

    }
    public function updatePrioridad(Request $request, $id)
    {
        $unidad = Unidad::findOrFail($id);
    
        // Validar la prioridad
        $request->validate([
            'prioridad' => 'required|integer|min:0',
        ]);
    
        // Actualizar solo la prioridad
        $unidad->prioridad = $request->prioridad;
        $unidad->save();
    
        return response()->json(['message' => 'Prioridad actualizada correctamente', 'unidad' => $unidad], 200);
    }
    
    // Método para obtener todas las unidades (incluyendo o excluyendo eliminadas)
    public function getAllUnidades(Request $request)
    {
        $includeDeleted = $request->query('deleted', false);

        $query = Unidad::query();

        if ($includeDeleted) {
            $query->withTrashed(); // Incluir eliminadas
        }

        return response()->json($query->get());
    }
    // Método para eliminar (soft delete) una unidad
    public function deleteUnidad($id)
    {
        $unidad = Unidad::findOrFail($id);
        $unidad->delete(); // Soft delete

        return response()->json(['message' => 'Unidad eliminada correctamente']);
    }

    // Método para restaurar una unidad eliminada
    public function restoreUnidad($id)
    {
        $unidad = Unidad::withTrashed()->find($id);

        if (!$unidad) {
            return response()->json(['message' => 'Unidad no encontrada'], 404);
        }

        $unidad->restore(); // Restaurar

        return response()->json(['message' => 'Unidad restaurada correctamente']);
    }

    // Método para obtener solo las unidades eliminadas
    public function getDeletedUnidades()
    {
        return response()->json(Unidad::onlyTrashed()->get());
    }
    public function forceDeleteUnidad($id)
    {
        $unidad = Unidad::onlyTrashed()->find($id);
    
        if (!$unidad) {
            return response()->json(['message' => 'Unidad no encontrada o no está eliminada'], 404);
        }
    
        $unidad->forceDelete(); // Eliminación definitiva
    
        return response()->json(['message' => 'Unidad eliminada permanentemente']);
    }    
}