<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index()
    {
        // Retornar todos los municipios con sus respectivas coordinaciones
        return Municipio::with('coordinacion')->get();
    }

    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'coordinacion_id' => 'required|exists:coordinaciones,id', // Asegura que exista la coordinacion
        ]);

        // Crear un nuevo municipio
        $municipio = Municipio::create($request->all());

        return response()->json($municipio, 201);
    }

    public function show(Municipio $municipio)
    {
        // Mostrar un municipio específico con su relación de coordinación
        return $municipio->load('coordinacion');
    }

    public function update(Request $request, Municipio $municipio)
    {
        // Validar los datos para la actualización
        $request->validate([
            'nombre' => 'required|string|max:255',
            'coordinacion_id' => 'required|exists:coordinaciones,id',
        ]);

        // Actualizar el municipio
        $municipio->update($request->all());

        return response()->json($municipio, 200);
    }

    public function destroy(Municipio $municipio)
    {
        // Eliminar el municipio
        $municipio->delete();

        return response()->json(null, 204);
    }
}