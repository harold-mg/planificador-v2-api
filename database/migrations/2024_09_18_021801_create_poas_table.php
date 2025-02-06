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
        Schema::create('poas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_poa');
            $table->year('anio');
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('cascade');
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); // Campo para borrado lógico
            $table->unique(['codigo_poa', 'anio'], 'codigo_poa_anio_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poas');
    }
};
