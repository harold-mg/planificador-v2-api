<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinacion extends Model
{
    use HasFactory;
    protected $table = 'coordinaciones';
    protected $fillable = ['nombre'];

    // Relación con Municipios
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }
    /* // Relación con ActividadesConVehiculo
    public function actividadVehiculo()
    {
        return $this->hasMany(ActividadVehiculo::class);
    } */
}
