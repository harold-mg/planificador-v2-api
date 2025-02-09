<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActividadSinVehiculo extends Model
{
    use HasFactory;
    use SoftDeletes;
    // Definir la tabla si no sigue el nombre plural de Laravel
    protected $table = 'actividades_sin_vehiculo';

    // Los atributos que son asignables masivamente
    protected $fillable = [
        'poa_id',
        'detalle_operacion',
        'resultados_esperados',
        'fecha_inicio',
        'fecha_fin',
        'municipio_id',
        'lugar',
        'tecnico_a_cargo',
        'detalles_adicionales',
        'estado_aprobacion',
        'observaciones',
        'nivel_aprobacion',
        //'realizado',
        'usuario_id',
    ];

    // Relaciones
    public function poa()
    {
        return $this->belongsTo(Poa::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
