<?php

namespace App\Console\Commands;

use App\Models\SchoolYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSchoolYearData extends Command
{
    protected $signature = 'fix:school-year-data';
    protected $description = 'Corriger les données des années scolaires';

    public function handle()
    {
        $this->info('Correction des données des années scolaires...');
        
        // Afficher les données actuelles
        $this->info('Données actuelles:');
        $schoolYears = DB::table('school_years')->get();
        $this->displaySchoolYears($schoolYears);
        
        // Corriger les données
        foreach ($schoolYears as $year) {
            // Extraire l'année de fin à partir de school_year (format: "YYYY-YYYY")
            $yearParts = explode('-', $year->school_year);
            if (count($yearParts) == 2) {
                $endYear = $yearParts[1];
                
                // Mettre à jour curent_year avec l'année de fin
                DB::table('school_years')
                    ->where('id', $year->id)
                    ->update([
                        'curent_year' => $endYear,
                        'updated_at' => now()
                    ]);
                
                $this->line("Année scolaire ID {$year->id} ({$year->school_year}): curent_year mis à jour à {$endYear}");
            } else {
                $this->error("Format d'année scolaire invalide pour ID {$year->id}: {$year->school_year}");
            }
        }
        
        // Vérifier les résultats
        $this->newLine();
        $this->info('Données après correction:');
        $updatedSchoolYears = DB::table('school_years')->get();
        $this->displaySchoolYears($updatedSchoolYears);
        
        // Vérifier l'année active
        $activeYear = DB::table('school_years')->where('active', '1')->first();
        if ($activeYear) {
            $this->info("Année scolaire active: {$activeYear->school_year} (ID: {$activeYear->id})");
        } else {
            $this->error("Aucune année scolaire active trouvée!");
        }
        
        return 0;
    }
    
    private function displaySchoolYears($schoolYears)
    {
        $this->table(
            ['ID', 'school_year', 'curent_year', 'active'],
            $schoolYears->map(function($year) {
                return [
                    'ID' => $year->id,
                    'school_year' => $year->school_year,
                    'curent_year' => $year->curent_year,
                    'active' => $year->active
                ];
            })
        );
    }
} 