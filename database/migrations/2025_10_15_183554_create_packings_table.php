<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_id')->constrained('notas')->onDelete('cascade'); // Clave a la cabecera del pedido
            // --- Trazabilidad y Responsabilidad ---
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Packer (Operador)
            $table->foreignId('articulo_id')->constrained('articulos')->onDelete('cascade');
            $table->foreignId('zona_id')->nullable()->constrained('zonas')->onDelete('set null'); // Ubicación (Opcional)
            // --- Hitos de Tiempo ---
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            // --- Contenido y Embalaje ---
            $table->integer('cantidad')->nullable();
            $table->string('caja')->nullable();
            $table->string('pallet')->nullable();
            $table->integer('peso')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packings');
    }
};
