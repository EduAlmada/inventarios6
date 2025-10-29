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
        Schema::create('tareas_activas', function (Blueprint $table) {
            $table->id();
            
            // 1. Llaves foráneas
            // 'users' es la tabla predeterminada para autenticación
            $table->foreignId('user_id')->constrained('users')->comment('Operador activo'); 
            // 'notas' es tu tabla de pedidos
            $table->foreignId('nota_id')->constrained('notas')->comment('Nota que se está trabajando'); 

            // 2. Campos de estado y prioridad
            $table->enum('tipo_actividad', ['para_picking', 'picking', 'para_packing', 'packing', 'para_despacho', 'despacho'])->comment('Fase actual del trabajo');
            $table->integer('orden_prioridad')->default(0)->comment('Prioridad asignada por el administrador (1 = Máxima)');
            
            // 3. Tiempos
            $table->timestamp('iniciada_en')->nullable()->comment('Marca de tiempo de inicio de la tarea');
            
            $table->timestamps();

            // 4. Restricción única
            // Asegura que una nota solo pueda ser trabajada por un usuario a la vez.
            $table->unique(['nota_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas_activas');
    }
};