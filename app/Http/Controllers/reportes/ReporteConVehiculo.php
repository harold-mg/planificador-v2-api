<?php

namespace App\Http\Controllers\reportes;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ActividadVehiculo;
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
    
        // Continuar con el procesamiento de los datos y generación del PDF
        $data = [
            'mes' => $mes,
            'year' => $year,
            'actividades' => $actividades
        ];
    
        // Generar el PDF con DomPDF
        $pdf = PDF::loadView('reports.reporte_actividad_conv', $data)
                   ->setPaper('a4', 'landscape'); // Establecer tamaño A4 y orientación horizontal
        return $pdf->stream('reporte_mensual.pdf');
    }

}
