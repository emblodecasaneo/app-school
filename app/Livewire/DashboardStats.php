<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;

class DashboardStats extends Component
{
    public $totalStudents;
    public $totalClasses;
    public $totalLevels;
    public $totalPayments;
    public $totalRevenue;
    public $totalExpectedRevenue;
    public $paymentRate;
    public $studentsPerClass;
    public $studentsPerLevel;
    public $recentPayments;
    public $unpaidStudents;
    public $activeYear;
    public $maleStudents;
    public $femaleStudents;
    public $genderRatio;
    public $paymentsByMonth;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // Récupérer l'année scolaire active
        $this->activeYear = SchoolYear::where('active', '1')->first();
        
        if (!$this->activeYear) {
            return;
        }

        // Statistiques générales
        $this->totalStudents = Student::count();
        $this->totalClasses = Classe::whereHas('level', function($query) {
            $query->where('school_year_id', $this->activeYear->id);
        })->count();
        $this->totalLevels = Level::where('school_year_id', $this->activeYear->id)->count();
        
        // Statistiques financières
        $this->totalPayments = Payment::where('school_year_id', $this->activeYear->id)->count();
        $this->totalRevenue = Payment::where('school_year_id', $this->activeYear->id)->sum('montant');
        
        // Calculer le revenu attendu total (somme des scolarités de tous les élèves inscrits)
        $inscriptions = Attributtion::where('school_year_id', $this->activeYear->id)->get();
        $expectedRevenue = 0;
        foreach ($inscriptions as $inscription) {
            $classe = Classe::find($inscription->classe_id);
            if ($classe) {
                $level = Level::find($classe->level_id);
                if ($level) {
                    $expectedRevenue += $level->scolarite;
                }
            }
        }
        $this->totalExpectedRevenue = $expectedRevenue;
        
        // Taux de paiement (pourcentage du revenu attendu qui a été payé)
        $this->paymentRate = $expectedRevenue > 0 ? round(($this->totalRevenue / $expectedRevenue) * 100, 2) : 0;
        
        // Répartition des élèves par classe
        $classes = Classe::whereHas('level', function($query) {
            $query->where('school_year_id', $this->activeYear->id);
        })->get();
        
        $this->studentsPerClass = [];
        foreach ($classes as $classe) {
            $count = Attributtion::where('classe_id', $classe->id)
                ->where('school_year_id', $this->activeYear->id)
                ->count();
            $this->studentsPerClass[$classe->id] = [
                'name' => $classe->libelle,
                'count' => $count
            ];
        }
        
        // Répartition des élèves par niveau
        $levels = Level::where('school_year_id', $this->activeYear->id)->get();
        $this->studentsPerLevel = [];
        foreach ($levels as $level) {
            $classeIds = Classe::where('level_id', $level->id)->pluck('id')->toArray();
            $count = Attributtion::whereIn('classe_id', $classeIds)
                ->where('school_year_id', $this->activeYear->id)
                ->count();
            $this->studentsPerLevel[$level->id] = [
                'name' => $level->libelle,
                'count' => $count
            ];
        }
        
        // Récents paiements
        $this->recentPayments = Payment::where('school_year_id', $this->activeYear->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Élèves avec paiements en retard (non solvables)
        $this->unpaidStudents = Payment::where('school_year_id', $this->activeYear->id)
            ->where('solvable', '0')
            ->count();
            
        // Statistiques par genre
        $this->maleStudents = Student::where('sexe', 'M')->count();
        $this->femaleStudents = Student::where('sexe', 'F')->count();
        $this->genderRatio = $this->totalStudents > 0 ? 
            round(($this->maleStudents / $this->totalStudents) * 100, 2) . '% / ' . 
            round(($this->femaleStudents / $this->totalStudents) * 100, 2) . '%' : 
            '0% / 0%';
            
        // Paiements par mois pour l'année scolaire en cours
        $this->paymentsByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthTotal = Payment::where('school_year_id', $this->activeYear->id)
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('montant');
            $this->paymentsByMonth[$i] = $monthTotal;
        }
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
