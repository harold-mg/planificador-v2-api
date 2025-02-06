<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadSinVehiculo extends Model
{
    use HasFactory;

    // Definir la tabla si no sigue el nombre plural de Laravel
    protected $table = 'actividades_sin_vehiculo';

    // Los atributos que son asignables masivamente
    protected $fillable = [
        'poa_id',
        'detalle_operacion',
        'resultados_esperados',
        'fecha_inicio',
        'fecha_fin',
        'centro_salud_id',
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

    public function centroSalud()
    {
        return $this->belongsTo(CentroSalud::class, 'centro_salud_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
