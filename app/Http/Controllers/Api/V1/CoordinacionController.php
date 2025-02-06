<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coordinacion;
use Illuminate\Http\Request;

class CoordinacionController extends Controller
{
    // Método para almacenar una nueva coordinación
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        //crear la nueva coordinacion
        $coordinacion = Coordinacion::create($request->all());

        return response()->json($coordinacion, 201);
    }
   /*  public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:unidades|max:255',
        ]);

        Coordinacion::create([
            'nombre' => $validated['nombre'],
        ]);

        return response()->json(['message' => 'Unidad creada correctamente']);
    } */

    // Método para listar las coordinaciones
    public function index()
    {
        $coordinaciones = Coordinacion::all();
        return response()->json($coordinaciones);
    }

    // Método para mostrar una coordinación específica
    public function show($id)
    {
        $coordinacion = Coordinacion::findOrFail($id);
        return response()->json($coordinacion);
    }

    // Método para actualizar una coordinación
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $coordinacion = Coordinacion::findOrFail($id);
        $coordinacion->update($request->all());

        return response()->json($coordinacion);
    }

    // Método para eliminar una coordinación
    public function destroy($id)
    {
        $coordinacion = Coordinacion::findOrFail($id);
        $coordinacion->delete();

        return response()->json(null, 204);
    }
}