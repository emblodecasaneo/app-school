<?php

namespace App\Console\Commands;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Console\Command;

class TestActiveYearInModels extends Command
{
    protected $signature = 'test:active-year-in-models {classe_id?}';
    protected $description = 'Tester la récupération de l\'année scolaire active dans les modèles';

    public function handle()
    {
        $classeId = $this->argument('classe_id') ?: 2; // Par défaut, classe 5A (ID: 2)
        
        $this->info('Test de récupération de l\'année scolaire active dans les modèles...');
        
        // Récupérer l'année scolaire active
        $activeYear = SchoolYear::where('active', '1')->first();
        $this->info('Année scolaire active: ' . ($activeYear ? $activeYear->school_year . ' (ID: ' . $activeYear->id . ')' : 'Aucune'));
        
        // Récupérer la classe
        $classe = Classe::find($classeId);
        if (!$classe) {
            $this->error('Classe non trouvée avec ID: ' . $classeId);
            return 1;
        }
        
        $this->info('Classe: ' . $classe->libelle . ' (ID: ' . $classe->id . ')');
        
        // Compter les attributions pour cette classe et l'année active
        $attributionsCount = Attributtion::where('classe_id', $classe->id)
            ->where('school_year_id', $activeYear->id)
            ->count();
        
        $this->info('Nombre d\'attributions pour cette classe et l\'année active: ' . $attributionsCount);
        
        // Lister les attributions
        $attributions = Attributtion::where('classe_id', $classe->id)
            ->where('school_year_id', $activeYear->id)
            ->with('student')
            ->get();
        
        if ($attributions->count() > 0) {
            $this->info('Attributions:');
            foreach ($attributions as $attribution) {
                $this->line('  - ID: ' . $attribution->id . 
                    ', Élève: ' . ($attribution->student ? $attribution->student->nom . ' ' . $attribution->student->prenom : 'N/A') . 
                    ', Date: ' . $attribution->created_at);
            }
        } else {
            $this->line('Aucune attribution trouvée pour cette classe et l\'année active.');
        }
        
        return 0;
    }
} 