<?php

use App\Http\Controllers\Api\V1\ActividadVehiculoController;
use App\Http\Controllers\ApiExcel\ExcelPoaDataController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\reportes\ReporteAuditorio;
use App\Http\Controllers\reportes\ReporteConVehiculo;
use App\Http\Controllers\reportes\ReporteSinVehiculo;
use App\Http\Controllers\reportes\ReporteUsuarioConVehiculo;
use App\Http\Controllers\reportes\ReporteVirtual;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */
//Route::get('/reporte-mensual/{mes}', [ActividadVehiculoController::class, 'generarReporteMensual'])->name('reporte.mensual');
//Route::get('/reporte-mensual-con-vehiculo/{mes}', [ReporteConVehiculo::class, 'generarReporteMensual'])->name('reporte.mensual');
Route::get('/reporte-mensual-con-vehiculo/{mes}/{year}', [ReporteConVehiculo::class, 'generarReporteMensual']);
Route::get('/reporte-mensual-sin-vehiculo/{mes}/{year}', [ReporteSinVehiculo::class, 'generarReporteMensual']);
Route::get('/reporte-mensual-auditorio/{mes}/{year}', [ReporteAuditorio::class, 'generarReporteMensual']);
Route::get('/reporte-mensual-virtual/{mes}/{year}', [ReporteVirtual::class, 'generarReporteMensual']);

Route::get('/reporte-mensual/{usuario_id}/{mes}/{year}', [ReporteUsuarioConVehiculo::class, 'reporteMensual']);

/* Route::get('/import-poa', function () {
    return view('imports.import_poa');
}); */
Route::get('/import-poa', [ExcelPoaDataController::class, 'showImportForm'])->name('excel.import.form');
Route::post('/save-poa', [ExcelPoaDataController::class, 'storePoaData'])->name('excel.save');


Route::post('/import-poa', [ExcelPoaDataController::class, 'importExcel'])->name('excel.import');
Route::post('/excel/store', [ExcelPoaDataController::class, 'storePoaData'])->name('excel.store');
//Route::post('/poas/store', [ExcelPoaDataController::class, 'store'])->name('poas.store');
//Route::get('/poas', [ExcelPoaDataController::class, 'index'])->name('poas.index');