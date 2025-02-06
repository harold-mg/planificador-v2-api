<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActividadVehiculo;
use Illuminate\Http\Request;
//use PDF; // Importar el alias de DomPDF
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Vehiculo;
use App\Models\POA; // Si tienes una tabla para los POAs
use App\Models\CentroSalud; // Si tienes una tabla para los centros de salud
use App\Models\Coordinacion;
use App\Models\Municipio;


class ActividadVehiculoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');  // Asegúrate de que este middleware esté aplicado
    }
    public function store(Request $request)
    {

        // Validar los datos recibidos
        $validated = $request->validate([
            'poa_id' => 'required|exists:poas,id',
            'detalle_operacion' => 'required|string',
            'resultados_esperados' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'centro_salud_id' => 'required|exists:centros_salud,id',
            'tecnico_a_cargo' => 'required|string',
            'detalles_adicionales' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'realizado' => 'nullable|boolean',
            //'estado_aprobacion' => 'required|string',
            'usuario_id' => 'required|exists:usuarios,id', // Esta línea se puede omitir
        ]);
        // Determinar el nivel de aprobación en función del rol del usuario
        // Supongamos que el request trae el rol del usuario autenticado
        $usuario = auth()->user(); // Obtiene el usuario autenticado
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401); // Retorna error si no está autenticado
        }
        $nivel_aprobacion = 'unidad'; // Por defecto, actividades creadas por responsables de área necesitan revisión de unidad
        
        if ($usuario->rol == 'responsable_unidad') {
            // Si el usuario es Responsable de Unidad, su actividad pasa directamente al planificador
            $nivel_aprobacion = 'planificador';
        }
        // Crear la actividad con vehículo
        $actividad = ActividadVehiculo::create([
            'poa_id' => $validated['poa_id'],
            'detalle_operacion' => $validated['detalle_operacion'],
            'resultados_esperados' => $validated['resultados_esperados'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'centro_salud_id' => $validated['centro_salud_id'],
            'tecnico_a_cargo' => $validated['tecnico_a_cargo'],
            'detalles_adicionales' => $validated['detalles_adicionales'],
            //'usuario_id' => auth()->id(), // ID del usuario autenticado
            'estado_aprobacion' => 'pendiente', // Estado inicial de la actividad
            //'observaciones' => $validated['observaciones'],
            'observaciones' => $validated['observaciones'] ?? null,
            'nivel_aprobacion' => $nivel_aprobacion,
            'realizado' => $validated['realizado'] ?? false,
            //'nivel_aprobacion' => 'unidad',
            //'usuario_id' => $request->usuario_id,
            'usuario_id' => $validated['usuario_id'], // Se obtiene del request validado
        ]);
    
        // Retornar la actividad creada o una respuesta adecuada
        return response()->json(['actividad' => $actividad], 201);
    }

    // Método para obtener todas las actividades de vehículo
    public function index()
    {
        $actividades = ActividadVehiculo::all(); // Obtiene todas las actividades
        return response()->json($actividades); // Retorna las actividades como JSON
    }
    public function getActividadesPoa()
    {
        // Cargar las actividades junto con las relaciones de POA y Operacion
        $actividades = ActividadVehiculo::with(['poa.operaciones', 'usuario.area', 'usuario.unidad', 'centroSalud.municipio'])->get();
        
        return response()->json($actividades);
    }
    public function aprobarActividad(Request $request, $id)
    {
        $actividad = ActividadVehiculo::findOrFail($id);
    
        // Validar que el estado de la actividad aún esté pendiente
        if ($actividad->estado_aprobacion !== 'pendiente') {
            return response()->json(['error' => 'La actividad ya ha sido aprobada o rechazada.'], 400);
        }
    
        // Cambiar el estado de aprobación
        $actividad->estado_aprobacion = $request->input('estado_aprobacion'); // 'aprobado' o 'rechazado'
    
        if ($request->input('estado_aprobacion') === 'aprobado') {
            // Si es aprobado, buscar un vehículo disponible
            $vehiculo = Vehiculo::where('disponible', true)
                ->whereDoesntHave('actividadesVehiculo', function ($query) use ($actividad) {
                    $query->whereBetween('fecha_inicio', [$actividad->fecha_inicio, $actividad->fecha_fin]);
                })->first();
    
            if ($vehiculo) {
                $actividad->vehiculo_id = $vehiculo->id;
                $vehiculo->disponible = false; // Marcar el vehículo como no disponible
                $vehiculo->save();
            } else {
                return response()->json(['error' => 'No hay vehículos disponibles para las fechas seleccionadas.'], 400);
            }
        }
    
        $actividad->save();
    
        return response()->json(['success' => true, 'actividad' => $actividad]);
    }
    
    public function show($id)
    {
        $actividad = ActividadVehiculo::with(['poa', 'centroSalud', 'vehiculo'])->findOrFail($id);
    
        return response()->json($actividad);
    }
  
    public function update(Request $request, $id)
    {
        $actividad = ActividadVehiculo::findOrFail($id);
    
        // Verificar que la actividad esté en estado pendiente
        if ($actividad->estado_aprobacion !== 'pendiente') {
            return response()->json(['error' => 'La actividad ya no se puede editar.'], 400);
        }
    
        // Validar los nuevos datos
        $validated = $request->validate([
            'poa_id' => 'required|exists:poas,id',
            'detalle_operacion' => 'required|string',
            'resultados_esperados' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'centro_salud_id' => 'required|exists:centros_salud,id',
            'tecnico_a_cargo' => 'required|string',
            'detalles_adicionales' => 'nullable|string',
            'estado_aprobacion' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'realizado' => 'nullable|boolean',
        ]);
    
        // Actualizar la actividad
        $actividad->update($validated);
    
        return response()->json(['success' => true, 'actividad' => $actividad]);
    }

    public function destroy($id)
    {
        $actividad = ActividadVehiculo::findOrFail($id);
    
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
        $actividad = ActividadVehiculo::findOrFail($id);
        
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
        $actividad = ActividadVehiculo::findOrFail($id);
        
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
        /* $request->validate([
            'observaciones' => 'required|string|max:255',
        ]); */
        $actividad = ActividadVehiculo::findOrFail($id);
        
        // Verificar que el usuario tenga el rol correcto para rechazar
        if (auth()->user()->rol !== 'responsable_unidad' && auth()->user()->rol !== 'planificador') {
            return response()->json(['error' => 'No tienes permisos para rechazar esta actividad'], 403);
        }
        /* $actividad->estado_aprobacion = 'rechazado';  
        $actividad->observaciones = $request->observaciones; */
        // Si el usuario es el planificador, devolver la actividad a la unidad con estado pendiente
         if (auth()->user()->rol === 'planificador') {
            $actividad->estado_aprobacion = 'rechazado';  // Volver el estado a pendiente
            $actividad->observaciones = $request->observaciones;
            //$actividad->nivel_aprobacion = 'unidad';      // Devolver el nivel a unidad
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
        //$actividad->save();
    
        return response()->json(['message' => 'No se puede rechazar esta actividad'], 400);
    }
    public function cambiarEstadoActividad(Request $request, $id)
    {
        // Validar que el parámetro estado_aprobacion esté presente y sea válido
        $request->validate([
            'estado_aprobacion' => 'required|string|in:pendiente,aprobado,rechazado'
        ]);
        
        // Buscar la actividad por ID
        $actividad = ActividadVehiculo::findOrFail($id);
        
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
    public function getActividadesPorUsuario($id)
    {
        // Cargar la relación 'poa' junto con sus 'operaciones' en la consulta
        $actividades = ActividadVehiculo::with(['poa', 'poa.operaciones'])  // Carga la relación 'poa' y las 'operaciones' de 'poa'
                                    ->where('usuario_id', $id)
                                    ->where('estado_aprobacion', '!=', 'pendiente')  // Filtra solo las actividades aprobadas o rechazadas
                                    ->get();
    
        // Responder con las actividades en formato JSON
        return response()->json($actividades);
    }
  

    
}
