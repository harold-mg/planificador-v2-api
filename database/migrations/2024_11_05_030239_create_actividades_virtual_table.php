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
        Schema::create('actividades_virtual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poa_id')->constrained('poas')->onDelete('cascade');
            $table->text('detalle_operacion')->nullable();
            $table->string('tipo_actividad');
            $table->text('resultados_esperados');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('plataforma');
            $table->string('tecnico_a_cargo');
            $table->integer('participantes');
            $table->enum('estado_aprobacion', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->enum('nivel_aprobacion', ['unidad', 'planificador'])->default('unidad');
            //$table->boolean('realizado')->default(false);
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades_virtual');
    }
};
