<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipio extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'nombre', 
        'coordinacion_id',
    ];

    // Relación con Coordinación
    public function coordinacion()
    {
        return $this->belongsTo(Coordinacion::class);
    }

    // Relación con Centros de Salud
    public function centrosSalud()
    {
        return $this->hasMany(CentroSalud::class);
    }
}
