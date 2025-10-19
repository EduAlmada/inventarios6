<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->string('nota')->unique(); // Campo para el número de orden (CLAVE)
            $table->string('cliente')->nullable();
            $table->string('domicilio')->nullable();            
            // --- Fechas de Hitos del Documento ---
            $table->date('fecha_fill_rate')->nullable();          
            $table->dateTime('fecha_facturado')->nullable();
            $table->dateTime('fecha_despachado')->nullable();
            $table->dateTime('fecha_entregado')->nullable();  
            // --- Estado ---
            $table->string('estado')->default('Pendiente'); // Ej: Pendiente, En Picking, En Packing, Cerrado
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Usuario que gestionó/importó
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
