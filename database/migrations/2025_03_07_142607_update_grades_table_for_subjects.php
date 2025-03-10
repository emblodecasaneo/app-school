<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étape 1: Créer les matières à partir des données existantes
        $this->createSubjectsFromExistingData();
        
        // Étape 2: Ajouter la colonne subject_id à la table grades si elle n'existe pas déjà
        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->after('classe_id');
            }
            
            if (!Schema::hasColumn('grades', 'date')) {
                $table->date('date')->nullable()->after('type');
            }
        });
        
        // Étape 3: Migrer les données de la colonne subject vers subject_id
        $this->migrateSubjectData();
        
        // Étape 4: Supprimer l'ancienne colonne subject (optionnel, à faire plus tard)
        // Schema::table('grades', function (Blueprint $table) {
        //     $table->dropColumn('subject');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('subject_id');
            $table->dropColumn('date');
        });
    }
    
    /**
     * Crée les matières à partir des données existantes dans la table grades
     */
    private function createSubjectsFromExistingData(): void
    {
        // Vérifier si la colonne subject existe dans la table grades
        if (Schema::hasColumn('grades', 'subject')) {
            // Récupérer toutes les matières distinctes
            $subjects = DB::table('grades')
                ->select('subject')
                ->distinct()
                ->whereNotNull('subject')
                ->get()
                ->pluck('subject');
                
            // Créer les matières
            foreach ($subjects as $subjectName) {
                DB::table('subjects')->insertOrIgnore([
                    'name' => $subjectName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    /**
     * Migre les données de la colonne subject vers subject_id
     */
    private function migrateSubjectData(): void
    {
        // Vérifier si les deux colonnes existent
        if (Schema::hasColumn('grades', 'subject') && Schema::hasColumn('grades', 'subject_id')) {
            // Récupérer toutes les matières
            $subjects = DB::table('subjects')->get();
            
            // Pour chaque matière, mettre à jour les notes correspondantes
            foreach ($subjects as $subject) {
                DB::table('grades')
                    ->where('subject', $subject->name)
                    ->whereNull('subject_id')
                    ->update(['subject_id' => $subject->id]);
            }
        }
    }
};
