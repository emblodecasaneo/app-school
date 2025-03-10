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
        Schema::table('grades', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère existante si elle existe
            if (Schema::hasColumn('grades', 'subject_id')) {
                // Vérifier si la contrainte existe avant de la supprimer
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys('grades');
                
                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('subject_id', $foreignKey->getLocalColumns())) {
                        $table->dropForeign($foreignKey->getName());
                        break;
                    }
                }
                
                // Ajouter la nouvelle contrainte avec onDelete('restrict')
                $table->foreign('subject_id')
                    ->references('id')
                    ->on('subjects')
                    ->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère si elle existe
            $foreignKeys = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys('grades');
            
            foreach ($foreignKeys as $foreignKey) {
                if (in_array('subject_id', $foreignKey->getLocalColumns())) {
                    $table->dropForeign($foreignKey->getName());
                    break;
                }
            }
            
            // Ajouter la contrainte par défaut (sans restriction explicite)
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects');
        });
    }
};
