<?php

namespace App\Console\Commands;

use App\Models\SchoolYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSchoolYears extends Command
{
    protected $signature = 'check:school-years';
    protected $description = 'Vérifier les années scolaires dans la base de données';

    public function handle()
    {
        $this->info('Vérification des années scolaires...');
        
        // Vérifier avec le modèle Eloquent
        $schoolYears = SchoolYear::all();
        $this->line('Nombre d\'années scolaires via Eloquent: ' . $schoolYears->count());
        foreach ($schoolYears as $year) {
            $this->line('- ' . $year->school_year . ' (ID: ' . $year->id . ', Active: ' . ($year->active ? 'Oui' : 'Non') . ')');
        }
        
        $this->newLine();
        
        // Vérifier avec une requête DB directe
        $schoolYearsDB = DB::table('school_years')->get();
        $this->line('Nombre d\'années scolaires via DB Query: ' . $schoolYearsDB->count());
        foreach ($schoolYearsDB as $year) {
            $this->line('- ' . $year->school_year . ' (ID: ' . $year->id . ', Active: ' . ($year->active ? 'Oui' : 'Non') . ')');
        }
        
        // Vérifier l'année active
        $activeYear = SchoolYear::where('active', true)->first();
        $this->newLine();
        if ($activeYear) {
            $this->info('Année scolaire active: ' . $activeYear->school_year . ' (ID: ' . $activeYear->id . ')');
        } else {
            $this->error('Aucune année scolaire active trouvée!');
        }
        
        return 0;
    }
} 