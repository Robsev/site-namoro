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
            // Remover campos sensíveis restantes (apenas os que existem)
            $table->dropColumn([
                'postal_code',      // CEP (sensível)
                'phone',           // Telefone (sensível)
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
        });
    }
};