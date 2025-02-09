<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'vehiculos';
    
    protected $fillable = [
        'placa',
        'modelo',
        'disponible',
    ];

    // Relación uno a muchos con actividades_vehiculo
    public function actividadVehiculo()
    {
        return $this->hasMany(ActividadVehiculo::class);
    }
    // Scope para obtener solo los vehículos disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }
}
