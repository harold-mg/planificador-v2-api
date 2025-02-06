<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroSalud extends Model
{
    use HasFactory;
    protected $table = 'centros_salud';
    protected $fillable = [
        'nombre',
        'tipo',
        'municipio_id',
    ];

    // Relación con Municipio
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
