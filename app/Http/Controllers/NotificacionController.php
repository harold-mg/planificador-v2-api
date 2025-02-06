<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\ActividadVehiculo; // Importa los modelos necesarios para las actividades
use App\Models\ActividadSinVehiculo; // Importa los modelos necesarios para las actividades
use App\Models\ActividadAuditorio; // Importa los modelos necesarios para las actividades
use App\Models\ActividadVirtual; // Importa los modelos necesarios para las actividades

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    // Método para obtener notificaciones no leídas del usuario autenticado
    public function getUserNotifications()
    {
        $user = Auth::user();
        $notificaciones = Notificacion::where('usuario_id', $user->id)
                                        ->where('leido', false)
                                        ->get();

        return response()->json($notificaciones);
    }

    // Método para crear notificaciones cuando una actividad cambia de estado
    public function crearNotificacion($actividad, $estado, $observacion = null)
    {
        Notificacion::create([
            'usuario_id' => $actividad->usuario_id,
            'actividad_id' => $actividad->id,
            'tipo_actividad' => get_class($actividad),
            'codigo_poa' => $actividad->poa->codigo_poa,
            'fecha_inicio' => $actividad->fecha_inicio ?? $actividad->fecha,
            'estado_aprobacion' => $estado,
            'observaciones' => $observacion,
            'leido' => false
        ]);
    }

    // Método para marcar notificaciones como leídas
    public function marcarComoLeida($id)
    {
        $notificacion = Notificacion::find($id);
        $notificacion->leido = true;
        $notificacion->save();

        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    public function aprobarActividad(Request $request, $id)
    {
        $actividad = ActividadVehiculo::findOrFail($id);
        $actividad->estado_aprobacion = 'aprobado';
        $actividad->save();

        // Crear una notificación
        Notificacion::create([
            'usuario_id' => $actividad->usuario_id, // O el ID del usuario relevante
            'actividad_id' => $actividad->id,
            'tipo_actividad' => 'vehiculo', // Cambia esto según el tipo de actividad
            'codigo_poa' => $actividad->codigo_poa,
            'fecha_inicio' => $actividad->fecha_inicio,
            'estado_aprobacion' => 'aprobado',
            'observaciones' => 'La actividad ha sido aprobada',
            'leido' => false
        ]);
        $this->crearNotificacion($actividad, 'aprobado', 'La actividad ha sido aprobada');
        return response()->json(['message' => 'Actividad aprobada y notificación creada']);
    }
    public function getNotificacionesNoLeidas(Request $request)
    {
        $userId = $request->user()->id;
        $notificaciones = Notificacion::where('usuario_id', $userId)
                                        ->where('leido', false)
                                        ->get();
    
        return response()->json($notificaciones);
    }
    
}
