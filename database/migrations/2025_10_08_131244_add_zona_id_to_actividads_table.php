<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
    {
        public function up(): void
        {
            Schema::table('actividads', function (Blueprint $table) {
                // Añadimos la clave foránea a la tabla 'zonas', permitiendo nulos.
                $table->foreignId('zona_id')->nullable()->constrained('zonas')->onDelete('set null')->after('pedido');
            });
        }

        public function down(): void
        {
            Schema::table('actividads', function (Blueprint $table) {
                $table->dropConstrainedForeignId('zona_id');
            });
        }
    };
