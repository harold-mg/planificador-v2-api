<?php

namespace App\Http\Controllers\reportes;

use App\Http\Controllers\Controller;
use App\Models\ActividadAuditorio;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReporteAuditorio extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:sanctum');
    }

    // Generar reporte mensual de actividades sin vehículo
    public function generarReporteMensual(Request $request, $mes, $year)
    {
        // Obtener el primer y último día del mes seleccionado
        $primerDiaDelMes = Carbon::createFromDate($year, $mes, 1)->startOfMonth();
        $ultimoDiaDelMes = Carbon::createFromDate($year, $mes, 1)->endOfMonth();

        // Filtrar actividades dentro del rango de fechas
        $actividades = ActividadAuditorio::with(['poa', 'usuario'])
            ->where('estado_aprobacion', 'aprobado')
            ->whereBetween('fecha', [$primerDiaDelMes, $ultimoDiaDelMes])  // Rango de fechas
            ->get();

        // Verificar si hay actividades
        if ($actividades->isEmpty()) {
            return response()->json(['error' => 'No se encontraron actividades para el mes y año seleccionados.'], 404);
        }

        // Datos para el reporte
        $data = [
            'mes' => $mes,
            'year' => $year,
            'actividades' => $actividades,
        ];

        // Generar el PDF con DomPDF
        $pdf = PDF::loadView('reports.reporte_actividad_audi', $data)
            ->setPaper('a4', 'landscape'); // Establecer tamaño A4 y orientación horizontal

        return $pdf->stream('reporte_mensual_auditorio.pdf');
    }
}
