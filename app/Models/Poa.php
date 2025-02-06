<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poa extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'codigo_poa',
        'anio',
        'area_id',
        'unidad_id',
    ];
    // Relación con operaciones
    public function operaciones()
    {
        return $this->hasMany(Operacion::class);
    }
    
    // Relación con Area
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
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
}
