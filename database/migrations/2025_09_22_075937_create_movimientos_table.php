<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articulo_id')->constrained()->onDelete('cascade');
            $table->string('tipo'); // e.g., 'ingreso', 'salida', 'ajuste'
            $table->string('orden')->nullable();
            $table->string ('motivo')->nullable();
            $table->string('cliente')->nullable();
            $table->integer('cantidad');
            $table->foreignId('zona_id')->nullable()->constrained()->onDelete('set null'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');        
    }
};