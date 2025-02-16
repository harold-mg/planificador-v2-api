<?php

namespace App\Http\Controllers\reportes;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ActividadVehiculo;
use App\Models\Unidad;  // Asegúrate de importar el modelo Unidad
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteConVehiculo extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');  // Asegúrate de que este middleware esté aplicado
    }

    public function generarReporteMensual(Request $request, $mes, $year)
    {
        // Obtener el primer y último día del mes seleccionado
        $primerDiaDelMes = Carbon::createFromDate($year, $mes, 1)->startOfMonth();
        $ultimoDiaDelMes = Carbon::createFromDate($year, $mes, 1)->endOfMonth();
    
        // Filtrar actividades dentro del rango de fechas
        $actividades = ActividadVehiculo::with(['poa.operaciones', 'usuario.area', 'usuario.unidad', 'municipio'])
            ->where('estado_aprobacion', 'aprobado')
            ->whereBetween('fecha_inicio', [$primerDiaDelMes, $ultimoDiaDelMes])  // Rango de fechas
            ->get();        
        // Verificar si hay actividades
        if ($actividades->isEmpty()) {
            return response()->json(['error' => 'No se encontraron actividades para el mes y año seleccionados.'], 404);
        }
        // Obtener las unidades con sus áreas ordenadas por prioridad y luego por el id de las áreas
        $unidades = Unidad::with(['areas'])  // Cargar áreas relacionadas
            ->orderBy('prioridad', 'asc')  // Ordenar las unidades por prioridad
            ->get();        
        // Agrupar áreas dentro de las unidades y ordenar las áreas por su id
        $areasConUnidades = $unidades->map(function ($unidad) {
            // Ordenar las áreas dentro de la unidad por el ID (o el criterio que prefieras)
            $unidad->areas = $unidad->areas->sortBy('id');  
            return $unidad;
        });
        // Preparar los datos para el reporte
        $data = [
            'mes' => $mes,
            'year' => $year,
            'actividades' => $actividades,
            'unidades' => $areasConUnidades  // Pasar las unidades ordenadas con sus áreas
        ];
        // Generar el PDF con DomPDF
        $pdf = PDF::loadView('reports.reporte_actividad_conv', $data)
                   ->setPaper('a4', 'landscape');  // Establecer tamaño A4 y orientación horizontal
        
        return $pdf->stream('reporte_mensual.pdf');
    }
}
