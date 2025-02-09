<?php

use App\Http\Controllers\Api\V1\ActividadAuditorioController;
use App\Http\Controllers\Api\V1\ActividadExternaController;
use App\Http\Controllers\Api\V1\ActividadSinVehiculoController;
use App\Http\Controllers\Api\V1\ActividadVehiculoController;
use App\Http\Controllers\Api\V1\ActividadVirtualController;
use App\Http\Controllers\Api\V1\AreaController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CentroSaludController;
use App\Http\Controllers\Api\V1\CoordinacionController;
use App\Http\Controllers\Api\V1\MunicipioController;
use App\Http\Controllers\Api\V1\OperacionController;
use App\Http\Controllers\Api\V1\PoaController;
use App\Http\Controllers\Api\V1\UnidadController;
use App\Http\Controllers\Api\V1\VehiculoController;
use App\Http\Controllers\ApiExcel\ExcelDataController;
use App\Http\Controllers\ApiExcel\ExcelPoaDataController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\NotificacionesController;
//use App\Http\Controllers\ExcelDataController as ControllersExcelDataController;
use App\Models\ActividadVehiculo;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', [AuthController::class, 'me']);

Route::get('/usuarios', [AuthController::class, 'getAllUsers']);
Route::post('/login', [AuthController::class, 'login']);
// Rutas protegidas por autenticación Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('actividad_vehiculos', ActividadVehiculoController::class);
    /* Route::post('/actividad_vehiculo', [ActividadVehiculoController::class, 'store']);
    Route::get('/actividad_vehiculo', [ActividadVehiculoController::class, 'index']);
    Route::get('/actividad_vehiculo/{id}', [ActividadVehiculoController::class, 'show']);
    Route::put('/actividad_vehiculo/{id}', [ActividadVehiculoController::class, 'update']);
    Route::delete('/actividad_vehiculo/{id}', [ActividadVehiculoController::class, 'destroy']); */

    // Ruta para aprobar actividad por el responsable de unidad
    Route::post('/actividad_vehiculos/{id}/aprobar-unidad', [ActividadVehiculoController::class, 'aprobarPorUnidad']);
    Route::put('actividad_vehiculos/{id}/estado', [ActividadVehiculoController::class, 'cambiarEstadoActividad']);

    // Ruta para aprobar actividad por el planificador
    Route::post('/actividad_vehiculos/{id}/aprobar-planificador', [ActividadVehiculoController::class, 'aprobarPorPlanificador']);

    // Ruta para rechazar actividad
    Route::put('/actividad_vehiculos/{id}/rechazar', [ActividadVehiculoController::class, 'rechazar']);
    // Solo los planificadores pueden acceder a estas rutas
    Route::middleware('check.planificador')->group(function () {
        // Asegúrate de tener esta ruta en routes/api.php
        Route::get('/usuarios/eliminados', [AuthController::class, 'getDeletedUsers']);
        Route::get('/usuarios/{id}', [AuthController::class, 'getUsuario']);
        Route::put('/usuarios/{id}', [AuthController::class, 'updateUsuario']);
        Route::delete('/usuarios/{id}', [AuthController::class, 'deleteUsuario']); // Eliminar usuario (soft delete)
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/usuarios', [AuthController::class, 'getAllUsers']);
        Route::put('/usuarios/{id}/restore', [AuthController::class, 'restoreUsuario']); // Restaurar usuario eliminado
        //Route::apiResource('/user', AuthController::class);

        //UNIDADES
        Route::apiResource('/unidades', UnidadController::class);
        Route::post('/unidades', [UnidadController::class, 'store']);
        Route::get('/unidades', [UnidadController::class, 'index']);
        Route::get('/unidades/{id}', [UnidadController::class, 'show']);
    
        //AREAS
        Route::apiResource('/areas', AreaController::class);
        Route::post('/areas', [AreaController::class, 'store']);
        Route::get('/areas', [AreaController::class, 'index']);
        Route::get('/areas/{id}', [AreaController::class, 'show']);
    
        Route::get('/unidades/{unidad}/areas', [AreaController::class, 'getAreasPorUnidad']);
    
        //POAS
        Route::apiResource('poas', PoaController::class);
        Route::get('unidades/{unidad_id}/areas', [UnidadController::class, 'getAreasByUnidad']);
        //Route::get('/poas/{codigo_poa}/operaciones', [PoaController::class, 'getOperacionesByCodigo']);    
        Route::put('/poas/{id}/recover', [PoaController::class, 'recover']);

        //OPERACIONES
        Route::apiResource('operaciones', OperacionController::class);
        Route::post('/operaciones', [OperacionController::class, 'store']);
        Route::put('/operaciones/{id}/recover', [OperacionController::class, 'recover']);

        //COORDINACIONES
        Route::apiResource('coordinaciones', CoordinacionController::class);
    
        //MUNICIPIOS
        Route::apiResource('municipios', MunicipioController::class);
        Route::get('coordinacion/{coordinacionId}', [MunicipioController::class, 'getMunicipiosByCoordinacion']);
    
        //CENTRO DE SALUD
        Route::apiResource('centros_salud', CentroSaludController::class);
        Route::get('municipios/coordinacion/{id}', [CentroSaludController::class, 'getMunicipiosByCoordinacion']);
        Route::get('/coordinaciones/{coordinacionId}/municipios', [MunicipioController::class, 'getMunicipiosByCoordinacion']);
        Route::get('/municipios/{municipioId}/centros_salud', [CentroSaludController::class, 'getCentrosSaludByMunicipio']);
        
        //VEHICULO
        Route::apiResource('vehiculos', VehiculoController::class);
        Route::get('vehiculos/disponibles', [VehiculoController::class, 'disponibles']);
        
        //ACTIVIDAD VEHICULO
        Route::post('/actividad_vehiculos', [ActividadVehiculoController::class, 'store']);
        Route::get('/actividad_vehiculos', [ActividadVehiculoController::class, 'index']);
        Route::get('/actividad_vehiculo/{id}', [ActividadVehiculoController::class, 'show']);
        Route::put('/actividad_vehiculo/{id}', [ActividadVehiculoController::class, 'update']);
        Route::delete('/actividad_vehiculo/{id}', [ActividadVehiculoController::class, 'destroy']);
        //ACTIVIDAD SIN VEHICULO
        Route::apiResource('actividad_sin_vehiculos', ActividadSinVehiculoController::class);
        Route::get('actividad_sin_vehiculos_poa', [ActividadSinVehiculoController::class, 'getActividadesPoa']);
        Route::post('/actividad_sin_vehiculos/{id}/aprobar-planificador', [ActividadSinVehiculoController::class, 'aprobarPorPlanificador']);
        Route::post('/actividad_sin_vehiculos/{id}/aprobar-unidad', [ActividadSinVehiculoController::class, 'aprobarPorUnidad']);
        Route::put('/actividad_sin_vehiculos/{id}/rechazar', [ActividadSinVehiculoController::class, 'rechazar']);
        Route::put('actividad_sin_vehiculos/{id}/estado', [ActividadSinVehiculoController::class, 'cambiarEstadoActividad']);
        //ACTIVIDAD AUDITORIO
        Route::apiResource('actividad_auditorios', ActividadAuditorioController::class);
        Route::get('/actividad_auditorios_poa', [ActividadAuditorioController::class, 'getActividadesPoa']);
        Route::post('/actividad_auditorios/{id}/aprobar-planificador', [ActividadAuditorioController::class, 'aprobarPorPlanificador']);
        Route::post('/actividad_auditorios/{id}/aprobar-unidad', [ActividadAuditorioController::class, 'aprobarPorUnidad']);
        Route::put('/actividad_auditorios/{id}/rechazar', [ActividadAuditorioController::class, 'rechazar']);
        Route::put('/actividad_auditorios/{id}/estado', [ActividadAuditorioController::class, 'cambiarEstadoActividad']);
        //ACTIVIDAD VIRTUAL
        Route::apiResource('actividad_virtuales', ActividadVirtualController::class);
        Route::post('/actividad_virtuales/{id}/aprobar-unidad', [ActividadVirtualController::class, 'aprobarPorUnidad']);
        Route::post('/actividad_virtuales/{id}/aprobar-planificador', [ActividadVirtualController::class, 'aprobarPorPlanificador']);
        Route::get('/actividad_virtuales_poa', [ActividadVirtualController::class, 'getActividadesPoa']);
        Route::put('/actividad_virtuales/{id}/rechazar', [ActividadVirtualController::class, 'rechazar']);
        Route::put('/actividad_virtuales/{id}/estado', [ActividadVirtualController::class, 'cambiarEstadoActividad']);
        //ACTIVIDAD EXTERNA
        Route::apiResource('actividad_externas', ActividadExternaController::class);
        Route::post('/actividad_externas/{id}/aprobar-unidad', [ActividadExternaController::class, 'aprobarPorUnidad']);
        Route::post('/actividad_externas/{id}/aprobar-planificador', [ActividadExternaController::class, 'aprobarPorPlanificador']);
        Route::get('/actividad_externas_poa', [ActividadExternaController::class, 'getActividadesPoa']);
        Route::put('/actividad_externas/{id}/rechazar', [ActividadExternaController::class, 'rechazar']);
        Route::put('/actividad_externas/{id}/estado', [ActividadExternaController::class, 'cambiarEstadoActividad']);

        // Ruta para aprobar actividad por el planificador
        Route::post('/actividad_vehiculos/{id}/aprobar-planificador', [ActividadVehiculoController::class, 'aprobarPorPlanificador']);
        Route::post('/actividad_vehiculos/{id}/rechazar', [ActividadVehiculoController::class, 'rechazar']);

        //NOTIFICACIONES
        Route::get('/notificaciones', [NotificacionController::class, 'getUserNotifications']);
        Route::post('/notificaciones/marcar-leida/{id}', [NotificacionController::class, 'marcarComoLeida']);
        Route::post('/notificaciones/no-leidas', [NotificacionController::class, 'getNotificacionesNoLeidas']);
        Route::get('actividad_sin_vehiculos/actividades-sin-vehiculo/usuario/{id}', [ActividadSinVehiculoController::class, 'getActividadesPorUsuario']);

    });
    
    //ACTIVIDAD VEHICULO
    // Actividades de vehículos - CRUD básico
    Route::apiResource('actividad_vehiculos', ActividadVehiculoController::class);
    //Route::post('/actividad_vehiculos/{id}/aprobar-planificador', [ActividadVehiculoController::class, 'aprobarPorPlanificador']);
    Route::post('/actividad_vehiculos/{id}/aprobar-unidad', [ActividadVehiculoController::class, 'aprobarPorUnidad']);
    Route::post('/actividad_vehiculos/{id}/rechazar', [ActividadVehiculoController::class, 'rechazar']);
    Route::post('/actividad_vehiculos', [ActividadVehiculoController::class, 'store']);
    Route::get('/actividad_vehiculos/{usuario_id}/poas', [ActividadVehiculoController::class, 'getActividadesPoa']);
    Route::get('actividad_vehiculos_poa', [ActividadVehiculoController::class, 'getActividadesPoa']);
    //ACTIVIDAD SIN VEHICULO
    Route::apiResource('actividad_sin_vehiculos', ActividadSinVehiculoController::class);
    Route::get('actividad_sin_vehiculos_poa', [ActividadSinVehiculoController::class, 'getActividadesPoa']);
    Route::post('/actividad_sin_vehiculos/{id}/aprobar-unidad', [ActividadSinVehiculoController::class, 'aprobarPorUnidad']);
    Route::put('/actividad_sin_vehiculos/{id}/rechazar', [ActividadSinVehiculoController::class, 'rechazar']);
    Route::put('actividad_sin_vehiculos/{id}/estado', [ActividadSinVehiculoController::class, 'cambiarEstadoActividad']);
    //ACTIVIDAD AUDITORIO
    Route::apiResource('actividad_auditorios', ActividadAuditorioController::class);
    Route::post('/actividad_auditorios/{id}/aprobar-unidad', [ActividadAuditorioController::class, 'aprobarPorUnidad']);
    Route::get('/actividad_auditorios_poa', [ActividadAuditorioController::class, 'getActividadesPoa']);
    Route::put('/actividad_auditorios/{id}/rechazar', [ActividadAuditorioController::class, 'rechazar']);
    Route::put('/actividad_auditorios/{id}/estado', [ActividadAuditorioController::class, 'cambiarEstadoActividad']);
    
    //ACTIVIDAD VIRTUAL
    Route::apiResource('actividad_virtuales', ActividadVirtualController::class);
    Route::post('/actividad_virtuales/{id}/aprobar-unidad', [ActividadVirtualController::class, 'aprobarPorUnidad']);
    Route::get('/actividad_virtuales_poa', [ActividadVirtualController::class, 'getActividadesPoa']);
    Route::put('/actividad_virtuales/{id}/rechazar', [ActividadVirtualController::class, 'rechazar']);
    Route::put('/actividad_virtuales/{id}/estado', [ActividadVirtualController::class, 'cambiarEstadoActividad']);
    //ACTIVIDAD EXTERNA
    Route::apiResource('actividad_externas', ActividadExternaController::class);
    Route::post('/actividad_externas/{id}/aprobar-unidad', [ActividadExternaController::class, 'aprobarPorUnidad']);
    Route::get('/actividad_externas_poa', [ActividadExternaController::class, 'getActividadesPoa']);
    Route::put('/actividad_externas/{id}/rechazar', [ActividadExternaController::class, 'rechazar']);
    Route::put('/actividad_externas/{id}/estado', [ActividadExternaController::class, 'cambiarEstadoActividad']);
    //NOTIFICACIONES
    Route::get('actividad_sin_vehiculos/actividades-sin-vehiculo/usuario/{id}', [ActividadSinVehiculoController::class, 'getActividadesPorUsuario']);
    Route::get('actividad_vehiculos/actividades-vehiculo/usuario/{id}', [ActividadVehiculoController::class, 'getActividadesPorUsuario']);
    Route::get('actividad_auditorios/actividades-vehiculo/usuario/{id}', [ActividadAuditorioController::class, 'getActividadesPorUsuario']);
    Route::get('actividad_virtuales/actividades-vehiculo/usuario/{id}', [ActividadVirtualController::class, 'getActividadesPorUsuario']);
    Route::get('actividad_externas/actividades-vehiculo/usuario/{id}', [ActividadExternaController::class, 'getActividadesPorUsuario']);


});
    //ACTIVIDAD VEHICULO
    //Route::middleware('auth:api')->post('/actividad_vehiculos', [ActividadVehiculoController::class, 'store']);
    // Actividades de vehículos - CRUD básico
    Route::apiResource('actividad_vehiculos', ActividadVehiculoController::class);
// Ruta para que el planificador apruebe/rechace una actividad
Route::post('/actividad_vehiculos/{id}/aprobar', [ActividadVehiculoController::class, 'aprobarActividad']);
Route::get('municipios/coordinacion/{id}', [CentroSaludController::class, 'getMunicipiosByCoordinacion']);
Route::get('/coordinaciones/{coordinacionId}/municipios', [MunicipioController::class, 'getMunicipiosByCoordinacion']);
Route::get('/municipios/{municipioId}/centros_salud', [CentroSaludController::class, 'getCentrosSaludByMunicipio']);
Route::get('vehiculos/disponibles', [VehiculoController::class, 'getVehiculosDisponibles']);
Route::get('vehiculos/disponibles', [VehiculoController::class, 'disponibles']);

//CENTRO DE SALUD
Route::apiResource('centros_salud', CentroSaludController::class);
Route::get('municipios/coordinacion/{id}', [CentroSaludController::class, 'getMunicipiosByCoordinacion']);
Route::get('/coordinaciones/{coordinacionId}/municipios', [MunicipioController::class, 'getMunicipiosByCoordinacion']);
Route::get('municipio/{municipioId}', [CentroSaludController::class, 'getCentrosSaludByMunicipio']);

//COORDINACIONES
//Route::apiResource('coordinaciones', CoordinacionController::class);
Route::get('/coordinaciones', [CoordinacionController::class, 'index']);
//MUNICIPIOS
Route::apiResource('municipios', MunicipioController::class);
Route::get('coordinacion/{coordinacionId}', [MunicipioController::class, 'getMunicipiosByCoordinacion']);

//POAS
//Route::apiResource('poas', PoaController::class);
Route::apiResource('poas', PoaController::class);
Route::get('unidades/{unidad_id}/areas', [UnidadController::class, 'getAreasByUnidad']);
Route::get('/poas/{codigo_poa}/operaciones', [PoaController::class, 'getOperacionesByCodigo']);    
Route::get('/poas/{codigo_poa}/operaciones', [PoaController::class, 'getOperaciones']);


//OPERACIONES
Route::apiResource('operaciones', OperacionController::class);
Route::post('/operaciones', [OperacionController::class, 'store']);
Route::get('/unidades', [UnidadController::class, 'index']);
Route::get('/areas', [AreaController::class, 'index']);
Route::middleware('auth:sanctum')->group(function() {
    // Ruta para obtener los POAs filtrados por el área o unidad del usuario autenticado
    Route::get('/poas', [PoaController::class, 'getPoas']);
    Route::get('poas/unidad/{unidad_id}', [PoaController::class, 'getPoasByUnidad']);

});
Route::apiResource('actividad_vehiculos', ActividadVehiculoController::class);
Route::get('actividad_vehiculos_poa', [ActividadVehiculoController::class, 'getActividadesPoa']);
//API MAPA ENFERMEDADES
Route::get('/datos-excel', [ExcelDataController::class, 'obtenerDatosDesdeExcel']);
//POA EXCEL
Route::get('/datos-poa-excel', [ExcelPoaDataController::class, 'obtenerDatosDesdeExcel']);
Route::post('/upload-excel', [ExcelPoaDataController::class, 'uploadExcel']);

Route::apiResource('centros_salud', CentroSaludController::class);
//NOTIFICACIONES
Route::get('actividad_sin_vehiculos/actividades-sin-vehiculo/usuario/{id}', [ActividadSinVehiculoController::class, 'getActividadesPorUsuario']);
Route::get('actividad_vehiculos/actividades-vehiculo/usuario/{id}', [ActividadVehiculoController::class, 'getActividadesPorUsuario']);

// Ruta para procesar el archivo y generar el JSON
Route::post('/import-poa', [ExcelPoaDataController::class, 'importExcel']);

// Ruta para obtener los datos en formato JSON
Route::get('/obtenerapi-poa', [ExcelPoaDataController::class, 'apiImportExcel']);