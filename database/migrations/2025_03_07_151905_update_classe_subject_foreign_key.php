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
        Schema::table('classe_subject', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère existante
            $table->dropForeign(['subject_id']);
            
            // Ajouter une nouvelle contrainte avec onDelete('restrict')
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classe_subject', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère modifiée
            $table->dropForeign(['subject_id']);
            
            // Restaurer la contrainte d'origine avec onDelete('cascade')
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('cascade');
        });
    }
};
