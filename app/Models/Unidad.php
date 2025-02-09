<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidad extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['nombre'];

    // Especificar la tabla 'unidades'
    protected $table = 'unidades';

    // Relación con Poa
    public function poas()
    {
        return $this->hasMany(Poa::class);
    }
    // Relación con Unidad
    public function unidad()
    {
        return $this->hasMany(Unidad::class);
    }
    // Relación con Area
    public function areas()
    {
        return $this->hasMany(Area::class);
    }
    
}
