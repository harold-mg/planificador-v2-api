<?php

namespace App\Http\Controllers\reportes;

use App\Http\Controllers\Controller;
use App\Models\ActividadSinVehiculo;
use App\Models\Unidad;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteSinVehiculo extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function generarReporteMensual(Request $request, $mes, $year)
    {
        $primerDiaDelMes = Carbon::createFromDate($year, $mes, 1)->startOfMonth();
        $ultimoDiaDelMes = Carbon::createFromDate($year, $mes, 1)->endOfMonth();
        $actividades = ActividadSinVehiculo::with(['poa.operaciones', 'usuario.area', 'usuario.unidad', 'municipio'])
            ->where('estado_aprobacion', 'aprobado')
            ->whereBetween('fecha_inicio', [$primerDiaDelMes, $ultimoDiaDelMes])
            ->get();        
        if ($actividades->isEmpty()) {
            return response()->json(['error' => 'No se encontraron actividades para el mes y año seleccionados.'], 404);
        }
        $unidades = Unidad::with(['areas'])
            ->orderBy('prioridad', 'asc')
            ->get();        
        $areasConUnidades = $unidades->map(function ($unidad) {
            $unidad->areas = $unidad->areas->sortBy('id');  
            return $unidad;
        });
        $data = [
            'mes' => $mes,
            'year' => $year,
            'actividades' => $actividades,
            'unidades' => $areasConUnidades
        ];
        // Generar el PDF con DomPDF
        $pdf = PDF::loadView('reports.reporte_actividad_sinv', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->stream('reporte_mensual_sin_vehiculo.pdf');
    }
}
