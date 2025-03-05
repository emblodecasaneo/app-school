<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSchoolYearActiveColumn extends Command
{
    protected $signature = 'fix:school-year-active {year_id?}';
    protected $description = 'Corriger la colonne active dans la table school_years';

    public function handle()
    {
        $yearId = $this->argument('year_id');
        
        // Afficher les années scolaires actuelles
        $this->info('Années scolaires actuelles:');
        $schoolYears = DB::table('school_years')->get();
        foreach ($schoolYears as $year) {
            $this->line('- ' . $year->school_year . ' (ID: ' . $year->id . ', Active: ' . $year->active);
        }
        
        $this->newLine();
        
        // Si aucun ID n'est fourni, demander à l'utilisateur
        if (!$yearId) {
            $yearId = $this->ask('Entrez l\'ID de l\'année scolaire à définir comme active:');
        }
        
        // Vérifier si l'année existe
        $yearToActivate = DB::table('school_years')->where('id', $yearId)->first();
        if (!$yearToActivate) {
            $this->error('Année scolaire non trouvée avec ID: ' . $yearId);
            return 1;
        }
        
        // Désactiver toutes les années
        DB::table('school_years')->update(['active' => '0']);
        
        // Activer l'année spécifiée
        DB::table('school_years')->where('id', $yearId)->update([
            'active' => '1',
            'updated_at' => now()
        ]);
        
        $this->info('Année scolaire "' . $yearToActivate->school_year . '" (ID: ' . $yearToActivate->id . ') définie comme active.');
        
        // Vérifier le résultat
        $this->newLine();
        $this->info('Années scolaires après correction:');
        $schoolYears = DB::table('school_years')->get();
        foreach ($schoolYears as $year) {
            $this->line('- ' . $year->school_year . ' (ID: ' . $year->id . ', Active: ' . $year->active);
        }
        
        return 0;
    }
} 