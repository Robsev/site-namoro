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
        if (!Schema::hasTable('user_interests')) {
            // Se a tabela não existe, cria completa
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
        } else {
            // Se a tabela já existe, adiciona as colunas faltantes
            Schema::table('user_interests', function (Blueprint $table) {
                if (!Schema::hasColumn('user_interests', 'user_id')) {
                    $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
                }
                if (!Schema::hasColumn('user_interests', 'interest_category_id')) {
                    $table->foreignId('interest_category_id')->constrained()->onDelete('cascade')->after('user_id');
                }
                if (!Schema::hasColumn('user_interests', 'interest_value')) {
                    $table->string('interest_value')->after('interest_category_id');
                }
                if (!Schema::hasColumn('user_interests', 'preference_level')) {
                    $table->integer('preference_level')->default(1)->after('interest_value');
                }
                if (!Schema::hasColumn('user_interests', 'is_public')) {
                    $table->boolean('is_public')->default(true)->after('preference_level');
                }
            });
            
            // Adiciona índices se não existirem (usando try-catch para evitar erros)
            try {
                Schema::table('user_interests', function (Blueprint $table) {
                    $table->index(['user_id', 'interest_category_id'], 'user_interests_user_id_interest_category_id_index');
                });
            } catch (\Exception $e) {
                // Índice já existe, ignora
            }
            
            try {
                Schema::table('user_interests', function (Blueprint $table) {
                    $table->index(['interest_category_id', 'interest_value'], 'user_interests_interest_category_id_interest_value_index');
                });
            } catch (\Exception $e) {
                // Índice já existe, ignora
            }
            
            try {
                Schema::table('user_interests', function (Blueprint $table) {
                    $table->unique(['user_id', 'interest_category_id', 'interest_value'], 'ui_user_category_value_unique');
                });
            } catch (\Exception $e) {
                // Índice único já existe, ignora
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
    }
};
