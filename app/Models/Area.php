<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'unidad_id'];

    // Relación con Poa
    public function poas()
    {
        return $this->hasMany(Poa::class);
    }
    // Relación con Unidad
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    // Relación con Usuario
    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
