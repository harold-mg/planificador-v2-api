<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CentroSalud;
use App\Models\Municipio;
use Illuminate\Http\Request;

class CentroSaludController extends Controller
{
    public function index()
    {
        // Retornar todos los centros de salud con sus respectivos municipios
        return CentroSalud::with('municipio')->get();
    }

    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'municipio_id' => 'required|exists:municipios,id', // Asegura que exista el municipio
        ]);

        // Crear un nuevo centro de salud
        $centroSalud = CentroSalud::create($request->all());

        return response()->json($centroSalud, 201);
    }

    public function show($id)
    {
        // Forzar la carga explícita de la relación
        $centroSalud = CentroSalud::with('municipio')->findOrFail($id);
        return response()->json($centroSalud);
    }

    public function update(Request $request, $id)
    {
        // Buscar el centro de salud por id
        $centroSalud = CentroSalud::findOrFail($id);
    
        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'municipio_id' => 'required|exists:municipios,id',
        ]);
    
        // Actualizar el centro de salud
        $centroSalud->update($request->all());
    
        // Retornar la respuesta
        return response()->json($centroSalud, 200);
    }
    
    
    

    public function destroy(CentroSalud $centroSalud)
    {
        // Eliminar el centro de salud
        $centroSalud->delete();

        return response()->json(null, 204);
    }

    // Obtener municipios filtrados por coordinaciones
    public function getMunicipiosByCoordinacion($coordinacionId)
    {
        // Obtener municipios que pertenecen a la coordinación específica
        $municipios = Municipio::where('coordinacion_id', $coordinacionId)->get();
        return response()->json($municipios);
    }
    // Método para obtener los centros de salud por municipio
    public function getCentrosSaludByMunicipio($municipioId)
    {
        $centrosSalud = CentroSalud::where('municipio_id', $municipioId)->get(); // Filtra los centros de salud por municipio
        return response()->json($centrosSalud);
    }
}
