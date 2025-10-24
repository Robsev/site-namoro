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
        Schema::create('user_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('interest_category_id')->constrained()->onDelete('cascade');
            $table->string('interest_value'); // Valor selecionado (ex: "Rock", "Futebol")
            $table->integer('preference_level')->default(1); // Nível de preferência (1-5)
            $table->boolean('is_public')->default(true); // Se é visível para outros usuários
            $table->timestamps();
            
            // Índices para performance
            $table->index(['user_id', 'interest_category_id']);
            $table->index(['interest_category_id', 'interest_value']);
            
            // Evitar duplicatas (nome curto para MySQL)
            $table->unique(['user_id', 'interest_category_id', 'interest_value'], 'ui_user_category_value_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
    }
};
