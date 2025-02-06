<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operacion extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'operaciones';
    
    protected $fillable = [
        'poa_id', 
        'accion_corto_plazo', 
        'descripcion'];

    // Relación con Poa
    public function poa()
    {
        return $this->belongsTo(Poa::class);
    }
}
