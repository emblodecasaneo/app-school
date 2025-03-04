<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les classes dans la base de données';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des classes...');
        
        $classes = \App\Models\Classe::all();
        
        if ($classes->isEmpty()) {
            $this->error('Aucune classe trouvée dans la base de données.');
            
            if ($this->confirm('Voulez-vous créer quelques classes de test?', true)) {
                $this->createTestClasses();
                return 0;
            }
            
            return 1;
        }
        
        $this->info('Nombre total de classes: ' . $classes->count());
        
        $this->info('Liste des classes:');
        foreach ($classes as $classe) {
            $this->line('- ID: ' . $classe->id . ', Libellé: ' . $classe->libelle);
        }
        
        return 0;
    }
    
    /**
     * Créer des classes de test
     */
    private function createTestClasses()
    {
        $this->info('Création de classes de test...');
        
        // Récupérer ou créer un niveau
        $level = \App\Models\Level::first();
        if (!$level) {
            $this->info('Aucun niveau trouvé. Création d\'un niveau de test...');
            $level = new \App\Models\Level();
            $level->libelle = 'Niveau Test';
            $level->code = 'TEST';
            $level->scolarite = 100000;
            $level->save();
        }
        
        // Créer quelques classes
        $classNames = ['6ème A', '6ème B', '5ème A', '5ème B', '4ème A', '4ème B', '3ème A', '3ème B'];
        
        foreach ($classNames as $className) {
            $classe = new \App\Models\Classe();
            $classe->libelle = $className;
            $classe->level_id = $level->id;
            $classe->save();
            
            $this->info('Classe créée: ' . $className);
        }
        
        $this->info('Classes de test créées avec succès!');
    }
}
