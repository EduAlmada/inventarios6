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
        Schema::table('tipo_actividads', function (Blueprint $table) {
            $table->tinyInteger('signo_stock')->default(0)->after('modifica_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_actividads', function (Blueprint $table) {
            $table->dropColumn('signo_stock');
        });
    }
};
