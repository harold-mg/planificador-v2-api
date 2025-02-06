<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActividadAuditorio;
use Illuminate\Http\Request;
use App\Models\POA; // Si tienes una tabla para los POAs
use App\Models\CentroSalud; // Si tienes una tabla para los centros de salud
use App\Models\Coordinacion;
use App\Models\Municipio;

class ActividadAuditorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');  // Asegúrate de que este middleware esté aplicado
    }

    // Crear una nueva actividad (sin vehículo)
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'poa_id' => 'required|exists:poas,id',  // Debe coincidir con el nombre de la tabla y el campo
            'detalle_operacion' => 'required|string',
            'tipo_actividad' => 'required|string',
            'resultados_esperados' => 'required|string',
            'fecha' => 'required|date',  // Alineado con la migración
            'hora_inicio' => 'required|date_format:H:i',  // Ajustado según la migración
            'hora_fin' => 'required|date_format:H:i',  // Ajustado según la migración
            'plataforma' => 'required|string',  // Según la migración
            'tecnico_a_cargo' => 'required|string',
            'participantes' => 'required|integer',
            'observaciones' => 'nullable|string',
            'usuario_id' => 'required|exists:usuarios,id',  // Asegúrate de que este campo coincida
        ]);

        // Determinar el nivel de aprobación en función del rol del usuario
        $usuario = auth()->user(); // Obtiene el usuario autenticado
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401); // Retorna error si no está autenticado
        }
        $nivel_aprobacion = 'unidad'; // Por defecto, actividades creadas por responsables de área necesitan revisión de unidad
        
        if ($usuario->rol == 'responsable_unidad') {
            // Si el usuario es Responsable de Unidad, su actividad pasa directamente al planificador
            $nivel_aprobacion = 'planificador';
        }

        // Crear la actividad
        $actividad = ActividadAuditorio::create([
            'poa_id' => $validated['poa_id'],
            'detalle_operacion' => $validated['detalle_operacion'],
            'tipo_actividad' => $validated['tipo_actividad'],
            'resultados_esperados' => $validated['resultados_esperados'],
            'fecha' => $validated['fecha'],  // Usando el nombre correcto de la migración
            'hora_inicio' => $validated['hora_inicio'],  // Usando el nombre correcto de la migración
            'hora_fin' => $validated['hora_fin'],  // Usando el nombre correcto de la migración
            'plataforma' => $validated['plataforma'],
            'tecnico_a_cargo' => $validated['tecnico_a_cargo'],
            'participantes' => $validated['participantes'],
            'estado_aprobacion' => 'pendiente', // Estado inicial de la actividad
            'observaciones' => $validated['observaciones'] ?? null,
            'nivel_aprobacion' => $nivel_aprobacion,
            'usuario_id' => $validated['usuario_id'],
        ]);

        return response()->json(['actividad' => $actividad], 201);
    }

    // Método para obtener todas las actividades
    public function index()
    {
        $actividades = ActividadAuditorio::all(); // Obtiene todas las actividades
        return response()->json($actividades); // Retorna las actividades como JSON
    }

    // Método para obtener las actividades junto con POA y relaciones
    public function getActividadesPoa()
    {
        $actividades = ActividadAuditorio::with(['poa.operaciones', 'usuario.area', 'usuario.unidad'])->get();
        return response()->json($actividades);
    }

    // Método para aprobar la actividad (sin vehículos)
    public function aprobarActividad(Request $request, $id)
    {
        $actividad = ActividadAuditorio::findOrFail($id);

        // Validar que el estado de la actividad aún esté pendiente
        if ($actividad->estado_aprobacion !== 'pendiente') {
            return response()->json(['error' => 'La actividad ya ha sido aprobada o rechazada.'], 400);
        }

        // Cambiar el estado de aprobación
        $actividad->estado_aprobacion = $request->input('estado_aprobacion'); // 'aprobado' o 'rechazado'

        $actividad->save();

        return response()->json(['success' => true, 'actividad' => $actividad]);
    }

    // Método para obtener una actividad por ID
    public function show($id)
    {
        $actividad = ActividadAuditorio::with(['poa', 'centroSalud'])->findOrFail($id);
        return response()->json($actividad);
    }

    // Método para actualizar la actividad
    public function update(Request $request, $id)
    {
        $actividad = ActividadAuditorio::findOrFail($id);

        // Verificar que la actividad esté en estado pendiente
        if ($actividad->estado_aprobacion !== 'pendiente') {
            return response()->json(['error' => 'La actividad ya no se puede editar.'], 400);
        }

        // Validar los nuevos datos
        $validated = $request->validate([
            'poa_id' => 'required|exists:poas,id',
            'detalle_operacion' => 'required|string',
            'resultados_esperados' => 'required|string',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'plataforma' => 'required|string',
            'tecnico_a_cargo' => 'required|string',
            'participantes' => 'required|integer',
        ]);

        // Actualizar la actividad
        $actividad->update($validated);

        return response()->json(['success' => true, 'actividad' => $actividad]);
    }

    // Método para eliminar una actividad
    public function destroy($id)
    {
        $actividad = ActividadAuditorio::findOrFail($id);

        // Verificar que la actividad esté en estado pendiente
        if ($actividad->estado_aprobacion !== 'pendiente') {
            return response()->json(['error' => 'Solo se pueden eliminar actividades pendientes.'], 400);
        }

        $actividad->delete();

        return response()->json(['success' => true]);
    }

    // Método para aprobar actividad por parte del responsable de unidad
    public function aprobarPorUnidad($id)
    {
        $actividad = ActividadAuditorio::findOrFail($id);

        // Verificar que el usuario tenga el rol correcto para aprobar
        if (auth()->user()->rol !== 'responsable_unidad') {
            return response()->json(['error' => 'No tienes permisos para aprobar esta actividad'], 403);
        }

        // Verificar que la actividad esté en el nivel correcto de aprobación
        if ($actividad->nivel_aprobacion === 'unidad') {
            $actividad->nivel_aprobacion = 'planificador'; // Cambiar al siguiente nivel de aprobación
            $actividad->estado_aprobacion = 'pendiente';
            $actividad->save();

            return response()->json(['message' => 'Actividad aprobada por la unidad y enviada al planificador']);
        }

        return response()->json(['message' => 'No se puede aprobar esta actividad'], 400);
    }

    // Método para aprobar actividad por parte del planificador
    public function aprobarPorPlanificador($id)
    {
        $actividad = ActividadAuditorio::findOrFail($id);

        // Verificar que el usuario tenga el rol correcto para aprobar
        if (auth()->user()->rol !== 'planificador') {
            return response()->json(['error' => 'No tienes permisos para aprobar esta actividad'], 403);
        }

        // Verificar que la actividad esté en el nivel correcto de aprobación
        if ($actividad->nivel_aprobacion === 'planificador') {
            $actividad->estado_aprobacion = 'aprobado'; // Cambiar el estado a aprobado
            $actividad->save();

            return response()->json(['message' => 'Actividad aprobada por el planificador']);
        }

        return response()->json(['message' => 'No se puede aprobar esta actividad'], 400);
    }

    // Método para rechazar actividad
    public function rechazar($id, Request $request)
    {
        $actividad = ActividadAuditorio::findOrFail($id);

        // Verificar que el usuario tenga el rol correcto para rechazar
        if (auth()->user()->rol !== 'responsable_unidad' && auth()->user()->rol !== 'planificador') {
            return response()->json(['error' => 'No tienes permisos para rechazar esta actividad'], 403);
        }

        // Si el usuario es el planificador, devolver la actividad a la unidad con estado pendiente
        if (auth()->user()->rol === 'planificador') {
            $actividad->estado_aprobacion = 'rechazado';  // Volver el estado a pendiente
            $actividad->observaciones = $request->observaciones;
            $actividad->save();

            return response()->json(['message' => 'Actividad rechazada por el planificador y devuelta a la unidad']);
        }

        // Si el usuario es el responsable de unidad, rechazar la actividad
        if (auth()->user()->rol === 'responsable_unidad') {
            $actividad->estado_aprobacion = 'rechazado';  // Cambiar el estado a rechazado
            $actividad->nivel_aprobacion = 'unidad';      // Mantener el nivel en unidad
            $actividad->observaciones = $request->observaciones;
            $actividad->save();

            return response()->json(['message' => 'Actividad rechazada por la unidad']);
        }

        return response()->json(['message' => 'No se puede rechazar esta actividad'], 400);
    }

    // Método para cambiar el estado de la actividad
    public function cambiarEstadoActividad(Request $request, $id)
    {
        // Validar que el parámetro estado_aprobacion esté presente y sea válido
        $request->validate([
            'estado_aprobacion' => 'required|string|in:pendiente,aprobado,rechazado'
        ]);
        
        // Buscar la actividad por ID
        $actividad = ActividadAuditorio::findOrFail($id);
        
        // Cambiar el estado de aprobación según el rol del usuario
        if (auth()->user()->rol === 'planificador') {
            // Si el rol es planificador, cambiamos estado_aprobacion a 'pendiente'
            $actividad->estado_aprobacion = 'pendiente';  
        } elseif (auth()->user()->rol === 'responsable_unidad') {
            // Si el rol es responsable_unidad, cambiamos nivel_aprobacion a 'unidad'
            $actividad->nivel_aprobacion = 'unidad';  
            $actividad->estado_aprobacion = 'pendiente';  

        } else {
            // Si no tiene el rol adecuado, devolver un error
            return response()->json(['error' => 'No tienes permisos para cambiar el estado de esta actividad'], 403);
        }
    
        // Si el estado es válido, se guarda el cambio realizado
        $actividad->save();
        
        // Responder con la actividad actualizada
        return response()->json(['success' => true, 'actividad' => $actividad]);
    }
}
