<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActividadAuditorio extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'actividades_auditorio';

    protected $fillable = [
        'poa_id',
        'detalle_operacion',
        'tipo_actividad',
        'resultados_esperados',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'lugar',
        'tecnico_a_cargo',
        'participantes',
        'estado_aprobacion',
        'observaciones',
        'nivel_aprobacion',
        'realizado',
        'usuario_id'
    ];


    public function poa()
    {
        return $this->belongsTo(Poa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }


    public function getFechaHoraInicioAttribute()
    {
        return $this->fecha . ' ' . $this->hora_inicio;
    }

    public function getFechaHoraFinAttribute()
    {
        return $this->fecha . ' ' . $this->hora_fin;
    }
}
