<?php

namespace App\Console\Commands;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Console\Command;

class CheckAllAttributions extends Command
{
    protected $signature = 'check:all-attributions {classe_id?}';
    protected $description = 'Vérifier toutes les attributions pour une classe donnée ou toutes les classes, quelle que soit l\'année scolaire';

    public function handle()
    {
        $classeId = $this->argument('classe_id');
        
        // Récupérer toutes les années scolaires
        $schoolYears = SchoolYear::all();
        $this->info('Années scolaires disponibles: ' . $schoolYears->count());
        foreach ($schoolYears as $year) {
            $this->line('- ' . $year->school_year . ' (ID: ' . $year->id . ', Active: ' . ($year->active ? 'Oui' : 'Non') . ')');
        }
        $this->newLine();
        
        if ($classeId) {
            $classe = Classe::find($classeId);
            if (!$classe) {
                $this->error('Classe non trouvée avec ID: ' . $classeId);
                return 1;
            }
            $this->checkClasseAttributions($classe);
        } else {
            $classes = Classe::all();
            foreach ($classes as $classe) {
                $this->checkClasseAttributions($classe);
            }
        }
        
        return 0;
    }
    
    private function checkClasseAttributions(Classe $classe)
    {
        $this->info('Vérification de la classe: ' . $classe->libelle . ' (ID: ' . $classe->id . ')');
        
        // Récupérer toutes les attributions pour cette classe
        $attributions = Attributtion::where('classe_id', $classe->id)
            ->with(['student', 'schoolyear'])
            ->get();
        
        if ($attributions->count() > 0) {
            $this->line('  - Nombre total d\'attributions: ' . $attributions->count());
            
            // Grouper par année scolaire
            $attributionsByYear = $attributions->groupBy('school_year_id');
            foreach ($attributionsByYear as $yearId => $yearAttributions) {
                $yearName = $yearAttributions->first()->schoolyear ? $yearAttributions->first()->schoolyear->school_year : 'Année inconnue';
                $this->line('  - Année ' . $yearName . ' (ID: ' . $yearId . '): ' . $yearAttributions->count() . ' élèves');
                
                foreach ($yearAttributions as $attribution) {
                    $this->line('    * ID: ' . $attribution->id . 
                        ', Élève: ' . ($attribution->student ? $attribution->student->nom . ' ' . $attribution->student->prenom : 'N/A') . 
                        ', Date: ' . $attribution->created_at);
                }
            }
        } else {
            $this->line('  - Aucune attribution trouvée pour cette classe.');
        }
        
        $this->newLine();
    }
} 