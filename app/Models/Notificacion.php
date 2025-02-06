<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;
    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'actividad_id',
        'tipo_actividad',
        'codigo_poa',
        'fecha_inicio',
        'estado_aprobacion',
        'observaciones',
        'leido'
    ];
}
