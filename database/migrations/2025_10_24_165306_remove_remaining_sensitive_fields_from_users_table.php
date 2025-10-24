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
        Schema::table('users', function (Blueprint $table) {
            // Remover campos sensíveis restantes
            $table->dropColumn([
                'postal_code',      // CEP (sensível)
                'phone',           // Telefone (sensível)
                'district',        // Distrito (redundante)
                'county',          // Condado (redundante)
                'road',            // Rua (sensível)
                'house_number',    // Número (sensível)
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar campos removidos
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('district')->nullable();
            $table->string('county')->nullable();
            $table->string('road')->nullable();
            $table->string('house_number')->nullable();
        });
    }
};