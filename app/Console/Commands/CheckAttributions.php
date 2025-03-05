<?php

namespace App\Console\Commands;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Console\Command;

class CheckAttributions extends Command
{
    protected $signature = 'check:attributions {classe_id?}';
    protected $description = 'Vérifier les attributions pour une classe donnée ou toutes les classes';

    public function handle()
    {
        $classeId = $this->argument('classe_id');
        $activeYear = SchoolYear::where('active', true)->first();
        
        if (!$activeYear) {
            $this->error('Aucune année scolaire active trouvée.');
            return 1;
        }
        
        $this->info('Année scolaire active: ' . $activeYear->school_year . ' (ID: ' . $activeYear->id . ')');
        
        if ($classeId) {
            $classe = Classe::find($classeId);
            if (!$classe) {
                $this->error('Classe non trouvée avec ID: ' . $classeId);
                return 1;
            }
            $this->checkClasseAttributions($classe, $activeYear);
        } else {
            $classes = Classe::all();
            foreach ($classes as $classe) {
                $this->checkClasseAttributions($classe, $activeYear);
            }
        }
        
        return 0;
    }
    
    private function checkClasseAttributions(Classe $classe, SchoolYear $activeYear)
    {
        $this->info('Vérification de la classe: ' . $classe->libelle . ' (ID: ' . $classe->id . ')');
        
        // Méthode 1: Utilisation de la relation
        $count1 = $classe->attributions()->where('school_year_id', $activeYear->id)->count();
        $this->line('  - Nombre d\'élèves via relation: ' . $count1);
        
        // Méthode 2: Requête directe
        $count2 = Attributtion::where('classe_id', $classe->id)
            ->where('school_year_id', $activeYear->id)
            ->count();
        $this->line('  - Nombre d\'élèves via requête directe: ' . $count2);
        
        // Afficher les détails des attributions
        $attributions = Attributtion::where('classe_id', $classe->id)
            ->where('school_year_id', $activeYear->id)
            ->with('student')
            ->get();
        
        if ($attributions->count() > 0) {
            $this->line('  - Détails des attributions:');
            foreach ($attributions as $attribution) {
                $this->line('    * ID: ' . $attribution->id . 
                    ', Élève: ' . ($attribution->student ? $attribution->student->nom . ' ' . $attribution->student->prenom : 'N/A') . 
                    ', Date: ' . $attribution->created_at);
            }
        } else {
            $this->line('  - Aucune attribution trouvée.');
        }
        
        $this->newLine();
    }
} 