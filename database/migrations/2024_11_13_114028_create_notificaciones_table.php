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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->unsignedBigInteger('actividad_id'); // ID de la actividad, sin restricción de foreign key
            $table->string('tipo_actividad'); // Indica la tabla de actividad (vehiculo, sin_vehiculo, etc.)
            $table->string('codigo_poa');
            $table->date('fecha_inicio');
            $table->enum('estado_aprobacion', ['aprobado', 'rechazado']);
            $table->text('observaciones')->nullable();
            $table->boolean('leido')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
