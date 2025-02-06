<?php

namespace App\Http\Controllers\reportes;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\ActividadVehiculo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ReporteUsuarioConVehiculo extends Controller
{
    /**
     * Generar reporte PDF de actividades aprobadas para un usuario en un mes específico.
     *
     * @param Request $request
     * @param int $usuario_id
     * @param int $mes
     * @param int $year
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function reporteMensual($usuario_id, $mes, $year)
    {
        setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer el idioma a español
        // Obtener el usuario
        $usuario = Usuario::findOrFail($usuario_id);

        // Obtener las actividades aprobadas para el usuario en el mes y año dados
        $actividades = ActividadVehiculo::where('usuario_id', $usuario_id)
        ->where('estado_aprobacion', 'aprobado')
        ->whereYear('fecha_inicio', $year)
        ->whereMonth('fecha_inicio', $mes)
        ->orderBy('fecha_inicio', 'asc') // Ordenar por fecha de inicio, ascendente
        ->with(['poa', 'centroSalud.municipio'])
        ->get();
    

        // Si no hay actividades, devolver error en JSON
        if ($actividades->isEmpty()) {
            return response()->json(['error' => 'No se encontraron actividades para el usuario en el mes y año seleccionados.'], 404);
        }

        // Datos a enviar a la vista del PDF
        $data = [
            'usuario' => $usuario,
            'actividades' => $actividades,
            'anio' => $year,
            'mes' => $mes
        ];

        // Generar el PDF con la vista 'reports.reporte_mensual'
        $pdf = PDF::loadView('reports.reporte_mensual', $data)
            ->setPaper('a4', 'landscape'); // A4 en horizontal

        // Descargar el PDF en el navegador
        return $pdf->stream("reporte_mensual_{$usuario->id}.pdf");
    }
}