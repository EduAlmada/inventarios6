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
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->text('descripcion');
            $table->integer('stock')->nullable();
            $table->integer('stock_minimo')->nullable();
            $table->integer('stock_maximo')->nullable();
            $table->string('foto')->nullable();
            $table->string('EAN13', 13)->nullable();
            $table->string('DUN14', 14)->nullable();
            $table->integer('unidades')->nullable()->default(1);
            $table->decimal('precio', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
