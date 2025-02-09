<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentroSalud extends Model
{
    use HasFactory;
    use SoftDeletes;
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
