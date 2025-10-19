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
        Schema::table('notas', function (Blueprint $table) {
            // Agregamos campos Orden de Compra del cliente y transporte de ultima milla.
            $table->string('orden_compra')->nullable()->after('nota');
            $table->string('transporte')->nullable()->after('domicilio');
            $table->string('domicilio_transporte')->nullable()->after('transporte');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            $table->dropColumn(['orden_compra', 'transporte', 'domicilio_transporte']);
        });
    }
};
