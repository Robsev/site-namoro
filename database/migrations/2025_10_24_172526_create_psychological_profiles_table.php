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
        Schema::create('psychological_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Big Five (OCEAN) - Escala de 1 a 5
            $table->decimal('openness', 3, 2)->default(3.00); // Abertura a experiências
            $table->decimal('conscientiousness', 3, 2)->default(3.00); // Conscienciosidade
            $table->decimal('extraversion', 3, 2)->default(3.00); // Extroversão
            $table->decimal('agreeableness', 3, 2)->default(3.00); // Amabilidade
            $table->decimal('neuroticism', 3, 2)->default(3.00); // Neuroticismo
            
            // Estilo de relacionamento (1-5)
            $table->decimal('attachment_style', 3, 2)->default(3.00); // Estilo de apego
            $table->decimal('communication_style', 3, 2)->default(3.00); // Estilo de comunicação
            $table->decimal('conflict_resolution', 3, 2)->default(3.00); // Resolução de conflitos
            
            // Valores pessoais (1-5)
            $table->decimal('family_importance', 3, 2)->default(3.00);
            $table->decimal('career_importance', 3, 2)->default(3.00);
            $table->decimal('adventure_seeking', 3, 2)->default(3.00);
            $table->decimal('stability_preference', 3, 2)->default(3.00);
            $table->decimal('social_connection', 3, 2)->default(3.00);
            
            // Preferências de lazer (1-5)
            $table->decimal('social_activities', 3, 2)->default(3.00);
            $table->decimal('introspective_activities', 3, 2)->default(3.00);
            $table->decimal('active_lifestyle', 3, 2)->default(3.00);
            $table->decimal('creative_activities', 3, 2)->default(3.00);
            
            // Metadados
            $table->json('questionnaire_responses')->nullable(); // Respostas originais
            $table->timestamp('completed_at')->nullable(); // Quando foi completado
            $table->boolean('is_public')->default(false); // Se é visível para outros
            $table->timestamps();
            
            // Índices para matching (nomes curtos para MySQL)
            $table->index(['openness', 'conscientiousness', 'extraversion'], 'psy_oce_index');
            $table->index(['agreeableness', 'neuroticism'], 'psy_an_index');
            $table->index(['attachment_style', 'communication_style'], 'psy_acs_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychological_profiles');
    }
};
