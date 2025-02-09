<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;
    use SoftDeletes;
    protected $fillable = [
        'nombre',
        'apellido',
        'cedula_identidad',
        'nombre_usuario',
        'password',
        'telefono',
        'rol',
        'area_id',
        'unidad_id'
    ];
    protected $dates = ['deleted_at'];
    // Relación con Area
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    // Relación directa con Unidad
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
    // Método que devuelve la entidad (area o unidad) a la que pertenece el usuario
    public function entidad()
    {
        if ($this->area) {
            return $this->area;
        } elseif ($this->unidad) {
            return $this->unidad;
        }
        return null; // Si no tiene ni área ni unidad
    }

    // Relación con ActividadesConVehiculo
    public function actividadesConVehiculo()
    {
        return $this->hasMany(ActividadVehiculo::class);
    }
    public function actividadesSinVehiculo()
    {
        return $this->hasMany(ActividadVehiculo::class);
    }
    public function actividadesAuditorio()
    {
        return $this->hasMany(ActividadAuditorio::class);
    }
    /* // Acceso a Unidad a través de Area
    public function unidad()
    {
        return $this->area->unidad();
    } */
}

