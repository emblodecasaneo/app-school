<?php

namespace App\Console\Commands;

use App\Models\SchoolYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSchoolYears extends Command
{
    protected $signature = 'fix:school-years {year_id?}';
    protected $description = 'Corriger les années scolaires dans la base de données';

    public function handle()
    {
        $yearId = $this->argument('year_id');
        
        // Afficher les années scolaires actuelles
        $this->info('Années scolaires actuelles:');
        $schoolYears = SchoolYear::all();
        foreach ($schoolYears as $year) {
            $this->line('- ' . $year->school_year . ' (ID: ' . $year->id . ', Active: ' . ($year->active ? 'Oui' : 'Non') . ')');
        }
        
        $this->newLine();
        
        // Si aucun ID n'est fourni, demander à l'utilisateur
        if (!$yearId) {
            $yearId = $this->ask('Entrez l\'ID de l\'année scolaire à définir comme active:');
        }
        
        // Vérifier si l'année existe
        $yearToActivate = SchoolYear::find($yearId);
        if (!$yearToActivate) {
            $this->error('Année scolaire non trouvée avec ID: ' . $yearId);
            return 1;
        }
        
        // Désactiver toutes les années une par une
        foreach (SchoolYear::all() as $year) {
            $year->active = 0;
            $year->save();
        }
        
        // Activer l'année spécifiée
        $yearToActivate->active = 1;
        $yearToActivate->save();
        
        $this->info('Année scolaire "' . $yearToActivate->school_year . '" (ID: ' . $yearToActivate->id . ') définie comme active.');
        
        // Vérifier le résultat
        $this->newLine();
        $this->info('Années scolaires après correction:');
        $schoolYears = SchoolYear::all();
        foreach ($schoolYears as $year) {
            $this->line('- ' . $year->school_year . ' (ID: ' . $year->id . ', Active: ' . ($year->active ? 'Oui' : 'Non') . ')');
        }
        
        return 0;
    }
} 