<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula_identidad')->unique();
            $table->string('nombre_usuario')->unique();
            $table->string('password'); // La contraseña se almacenará con hash
            $table->string('telefono')->nullable();
            $table->enum('rol', ['responsable_area', 'responsable_unidad', 'planificador']);
            $table->foreignId('area_id')->nullable()->constrained('areas');
            $table->foreignId('unidad_id')->nullable()->constrained('unidades');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
