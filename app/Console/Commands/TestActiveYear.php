<?php

namespace App\Console\Commands;

use App\Models\SchoolYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestActiveYear extends Command
{
    protected $signature = 'test:active-year';
    protected $description = 'Tester la récupération de l\'année scolaire active';

    public function handle()
    {
        $this->info('Test de récupération de l\'année scolaire active...');
        
        // Test avec DB Query
        $this->info('1. Test avec DB Query:');
        $activeYearDB = DB::table('school_years')->where('active', '1')->first();
        if ($activeYearDB) {
            $this->line("  - Année active trouvée: {$activeYearDB->school_year} (ID: {$activeYearDB->id})");
        } else {
            $this->error("  - Aucune année active trouvée avec DB Query");
        }
        
        // Test avec Eloquent (true)
        $this->info('2. Test avec Eloquent (active = true):');
        $activeYearEloquentTrue = SchoolYear::where('active', true)->first();
        if ($activeYearEloquentTrue) {
            $this->line("  - Année active trouvée: {$activeYearEloquentTrue->school_year} (ID: {$activeYearEloquentTrue->id})");
        } else {
            $this->error("  - Aucune année active trouvée avec Eloquent (active = true)");
        }
        
        // Test avec Eloquent (1)
        $this->info('3. Test avec Eloquent (active = \'1\'):');
        $activeYearEloquentOne = SchoolYear::where('active', '1')->first();
        if ($activeYearEloquentOne) {
            $this->line("  - Année active trouvée: {$activeYearEloquentOne->school_year} (ID: {$activeYearEloquentOne->id})");
        } else {
            $this->error("  - Aucune année active trouvée avec Eloquent (active = '1')");
        }
        
        // Test avec Eloquent (1 sans guillemets)
        $this->info('4. Test avec Eloquent (active = 1):');
        $activeYearEloquentOneNoQuotes = SchoolYear::where('active', 1)->first();
        if ($activeYearEloquentOneNoQuotes) {
            $this->line("  - Année active trouvée: {$activeYearEloquentOneNoQuotes->school_year} (ID: {$activeYearEloquentOneNoQuotes->id})");
        } else {
            $this->error("  - Aucune année active trouvée avec Eloquent (active = 1)");
        }
        
        // Afficher toutes les années
        $this->newLine();
        $this->info('Toutes les années scolaires:');
        $allYears = SchoolYear::all();
        foreach ($allYears as $year) {
            $this->line("  - {$year->school_year} (ID: {$year->id}, Active: {$year->active}, Type: " . gettype($year->active) . ")");
        }
        
        return 0;
    }
} 