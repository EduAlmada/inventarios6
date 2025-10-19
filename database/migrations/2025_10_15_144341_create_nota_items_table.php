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
        Schema::create('nota_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_id')->constrained()->onDelete('cascade'); // Clave Foránea a la Cabecera
            $table->foreignId('articulo_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('cantidad_solicitada')->nullable();
            $table->integer('cantidad_preparada')->default(0);
            $table->string('caja')->nullable();
            $table->string('pallet')->nullable();
            $table->integer('peso')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_items');
    }
};
