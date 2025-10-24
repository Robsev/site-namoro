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
            // Remover campos sensíveis de endereço
            $table->dropColumn([
                'address',           // Endereço completo (sensível)
                'road',             // Rua (sensível)
                'house_number',     // Número da casa (sensível)
                'district',         // Distrito (redundante)
                'county',           // Condado (redundante)
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
            $table->string('address')->nullable();
            $table->string('road')->nullable();
            $table->string('house_number')->nullable();
            $table->string('district')->nullable();
            $table->string('county')->nullable();
        });
    }
};