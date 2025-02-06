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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('coordinacion_id')->constrained('coordinaciones')->onDelete('cascade');
            /* $table->unsignedBigInteger('coordinacion_id');
            $table->foreign('coordinacion_id')->references('id')->on('coordinaciones')->onDelete('cascade'); */
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
