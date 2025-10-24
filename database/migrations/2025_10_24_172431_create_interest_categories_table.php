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
        Schema::create('interest_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da categoria (ex: "Música", "Esportes")
            $table->string('slug')->unique(); // Slug único (ex: "music", "sports")
            $table->text('description')->nullable(); // Descrição da categoria
            $table->json('options'); // Lista de opções disponíveis
            $table->integer('max_selections')->default(5); // Máximo de seleções permitidas
            $table->boolean('is_active')->default(true); // Se a categoria está ativa
            $table->integer('sort_order')->default(0); // Ordem de exibição
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_categories');
    }
};
