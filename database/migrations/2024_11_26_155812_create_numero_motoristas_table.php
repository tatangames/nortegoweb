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
        Schema::create('numero_motoristas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();

            // para que motorista en su app pueda aparecer el boton de editar datos
            // cuando sea autorizado por administrador
            $table->boolean('cambios');

            // fecha cuando registro motorista
            // una vez registrado ya no permitira registrar en firebase con el mismo numero
            $table->date('fecha_registro')->nullable();

            // bool para ver si esta registrado
            $table->boolean('registrado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numero_motoristas');
    }
};
