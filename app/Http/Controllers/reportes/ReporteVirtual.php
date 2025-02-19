<?php

namespace App\Http\Controllers\reportes;

use App\Http\Controllers\Controller;
use App\Models\ActividadVirtual;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReporteVirtual extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:sanctum');
    }

    public function generarReporteMensual(Request $request, $mes, $year)
    {
        $primerDiaDelMes = Carbon::createFromDate($year, $mes, 1)->startOfMonth();
        $ultimoDiaDelMes = Carbon::createFromDate($year, $mes, 1)->endOfMonth();

        $actividades = ActividadVirtual::with(['poa.operaciones', 'usuario.area', 'usuario.unidad'])
            ->where('estado_aprobacion', 'aprobado')
            ->whereBetween('fecha', [$primerDiaDelMes, $ultimoDiaDelMes])
            ->orderBy('fecha', 'asc')
            ->get();

        if ($actividades->isEmpty()) {
            return response()->json(['error' => 'No se encontraron actividades para el mes y año seleccionados.'], 404);
        }

        $unidades = Unidad::with(['areas'])->orderBy('prioridad', 'asc')->get();
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
        $pdf = PDF::loadView('reports.reporte_actividad_virtual', $data)
            ->setPaper('a4', 'landscape'); // Establecer tamaño A4 y orientación horizontal

        return $pdf->stream('reporte_mensual_virtual.pdf');
    }
}
