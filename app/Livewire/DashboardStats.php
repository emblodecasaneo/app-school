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
    
    // Nouveaux KPIs
    public $attendanceRate; // Taux d'assiduité global
    public $attendanceByClass; // Taux d'assiduité par classe
    public $enrollmentTrend; // Évolution des inscriptions
    public $previousYearStudents; // Nombre d'élèves de l'année précédente
    public $enrollmentGrowth; // Pourcentage de croissance des inscriptions
    public $revenueProjection; // Projection des revenus pour les prochains mois
    public $paymentTrend; // Tendance des paiements
    
    // Suivi académique
    public $academicPerformance; // Performance académique globale
    public $performanceByLevel; // Performance par niveau
    public $performanceByClass; // Performance par classe
    public $averagesByPeriod; // Moyennes par période
    public $averagesByClass; // Moyennes par classe
    public $averagesByLevel; // Moyennes par niveau
    public $passRateByClass; // Taux de réussite par classe
    public $passRateByLevel; // Taux de réussite par niveau
    public $performanceBySubject; // Performance par matière
    public $topPerformingStudents; // Meilleurs élèves
    public $improvementNeededStudents; // Élèves nécessitant un soutien
    public $performanceTrend; // Tendance des performances au fil du temps

    // Écouter les événements de création/suppression d'élèves pour rafraîchir les statistiques
    protected $listeners = [
        'studentCreated' => 'loadStats',
        'studentDeleted' => 'loadStats',
        'refresh-dashboard' => 'loadStats'
    ];

    public function mount()
    {
        // Initialiser les variables académiques avec des valeurs par défaut
        $this->academicPerformance = [
            'average' => 0,
            'median' => 0,
            'highest' => 0,
            'lowest' => 0,
            'passRate' => 0
        ];
        $this->performanceByLevel = [];
        $this->performanceByClass = [];
        $this->performanceBySubject = [];
        $this->topPerformingStudents = [];
        $this->improvementNeededStudents = [];
        $this->performanceTrend = [];
        
        // Charger les statistiques
        $this->loadStats();
    }

    public function loadStats()
    {
        // Récupérer l'année scolaire active
        $this->activeYear = SchoolYear::where('active', '1')->first();
        
        if (!$this->activeYear) {
            // Réinitialiser toutes les statistiques à zéro si aucune année active
            $this->resetStats();
            return;
        }

        // Obtenir les IDs des élèves inscrits pour l'année active
        $studentIds = Attributtion::where('school_year_id', $this->activeYear->id)
            ->pluck('student_id')
            ->unique()
            ->toArray();
        
        // Statistiques générales - ne compter que les élèves inscrits pour l'année active
        $this->totalStudents = count($studentIds);
        
        // Ne compter que les classes qui ont au moins une attribution pour l'année active
        $this->totalClasses = Classe::whereHas('attributions', function($query) {
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
        // Ne récupérer que les classes qui ont au moins une attribution pour l'année active
        $classes = Classe::whereHas('attributions', function($query) {
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
        // Ne récupérer que les niveaux qui ont des classes avec au moins une attribution pour l'année active
        $levels = Level::whereHas('classes.attributions', function($query) {
            $query->where('school_year_id', $this->activeYear->id);
        })->get();
        
        $this->studentsPerLevel = [];

        foreach ($levels as $level) {
            // Récupérer les IDs des classes associées à ce niveau qui ont des attributions pour l'année active
            $classeIds = Classe::where('level_id', $level->id)
                ->whereHas('attributions', function($query) {
                    $query->where('school_year_id', $this->activeYear->id);
                })
                ->pluck('id')
                ->toArray();
            
            // Compter les attributions (inscriptions) pour ces classes dans l'année active
            $count = Attributtion::whereIn('classe_id', $classeIds)
                ->where('school_year_id', $this->activeYear->id)
                ->count();
            
            // Ajouter seulement si le niveau a des élèves ou si on veut afficher tous les niveaux
            if ($count > 0 || true) { // Afficher tous les niveaux, même ceux sans élèves
                $this->studentsPerLevel[] = [
                    'name' => $level->libelle,
                    'count' => $count
                ];
            }
        }

        // Trier les niveaux par nombre d'élèves (décroissant)
        usort($this->studentsPerLevel, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Si aucun niveau n'a d'élèves, ajouter un niveau fictif pour éviter les erreurs d'affichage
        if (empty($this->studentsPerLevel)) {
            $this->studentsPerLevel[] = [
                'name' => 'Aucun niveau',
                'count' => 0
            ];
        }
        
        // Récents paiements
        $this->recentPayments = Payment::where('school_year_id', $this->activeYear->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Élèves avec paiements en retard (non solvables)
        $this->unpaidStudents = Payment::where('school_year_id', $this->activeYear->id)
            ->where('solvable', '0')
            ->count();
            
        // Statistiques par genre - ne compter que les élèves inscrits pour l'année active
        if (!empty($studentIds)) {
            $this->maleStudents = Student::whereIn('id', $studentIds)->where('sexe', 'M')->count();
            $this->femaleStudents = Student::whereIn('id', $studentIds)->where('sexe', 'F')->count();
            $this->genderRatio = $this->totalStudents > 0 ? 
                round(($this->maleStudents / $this->totalStudents) * 100, 2) . '% / ' . 
                round(($this->femaleStudents / $this->totalStudents) * 100, 2) . '%' : 
                '0% / 0%';
        } else {
            $this->maleStudents = 0;
            $this->femaleStudents = 0;
            $this->genderRatio = '0% / 0%';
        }
            
        // Paiements par mois pour l'année scolaire en cours
        $this->paymentsByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthTotal = Payment::where('school_year_id', $this->activeYear->id)
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('montant');
            $this->paymentsByMonth[$i] = $monthTotal;
        }
        
        // Calcul du taux d'assiduité (simulé pour le moment - à remplacer par des données réelles)
        // Dans un système réel, cela serait basé sur des enregistrements de présence
        $this->attendanceRate = rand(85, 98); // Simulation d'un taux entre 85% et 98%
        
        // Taux d'assiduité par classe (simulé)
        $this->attendanceByClass = [];
        foreach ($classes as $classe) {
            $this->attendanceByClass[$classe->id] = [
                'name' => $classe->libelle,
                'rate' => rand(80, 99) // Simulation d'un taux entre 80% et 99%
            ];
        }
        
        // Évolution des inscriptions par rapport à l'année précédente
        $previousYear = SchoolYear::where('id', '<', $this->activeYear->id)
            ->orderBy('id', 'desc')
            ->first();
            
        $this->previousYearStudents = 0;
        if ($previousYear) {
            // Compter les élèves de l'année précédente
            $previousYearStudentIds = Attributtion::where('school_year_id', $previousYear->id)
                ->pluck('student_id')
                ->unique()
                ->toArray();
            $this->previousYearStudents = count($previousYearStudentIds);
            
            // Calculer le pourcentage de croissance
            if ($this->previousYearStudents > 0) {
                $this->enrollmentGrowth = round((($this->totalStudents - $this->previousYearStudents) / $this->previousYearStudents) * 100, 1);
            } else {
                $this->enrollmentGrowth = 100; // Si pas d'élèves l'année précédente, croissance de 100%
            }
        } else {
            $this->enrollmentGrowth = 100; // Si pas d'année précédente, croissance de 100%
        }
        
        // Tendance des inscriptions sur les 5 dernières années (ou moins si pas assez d'années)
        $this->enrollmentTrend = [];
        $pastYears = SchoolYear::where('id', '<=', $this->activeYear->id)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get()
            ->sortBy('id');
            
        foreach ($pastYears as $year) {
            $yearStudentCount = Attributtion::where('school_year_id', $year->id)
                ->pluck('student_id')
                ->unique()
                ->count();
                
            $this->enrollmentTrend[] = [
                'year' => $year->school_year,
                'count' => $yearStudentCount
            ];
        }
        
        // Projection des revenus pour les prochains mois
        $this->revenueProjection = [];
        $currentMonth = (int)date('m');
        $monthsAhead = 6; // Projeter sur 6 mois
        
        // Calculer la moyenne des paiements des 3 derniers mois pour faire une projection
        $recentMonthsAvg = 0;
        $monthsToAverage = min(3, $currentMonth);
        $sumRecentMonths = 0;
        
        for ($i = 0; $i < $monthsToAverage; $i++) {
            $monthToCheck = $currentMonth - $i;
            if ($monthToCheck > 0) {
                $sumRecentMonths += $this->paymentsByMonth[$monthToCheck];
            }
        }
        
        if ($monthsToAverage > 0) {
            $recentMonthsAvg = $sumRecentMonths / $monthsToAverage;
        }
        
        // Projeter les revenus futurs basés sur la moyenne récente et le revenu attendu restant
        $remainingExpectedRevenue = $this->totalExpectedRevenue - $this->totalRevenue;
        $monthlyProjection = $remainingExpectedRevenue > 0 ? $remainingExpectedRevenue / $monthsAhead : $recentMonthsAvg;
        
        // Ajuster la projection en fonction de la tendance récente
        $projectionFactor = $recentMonthsAvg > 0 ? $recentMonthsAvg / $monthlyProjection : 1;
        $adjustedProjection = $monthlyProjection * $projectionFactor;
        
        // Générer les projections pour les prochains mois
        for ($i = 1; $i <= $monthsAhead; $i++) {
            $projectionMonth = $currentMonth + $i;
            $year = date('Y');
            if ($projectionMonth > 12) {
                $projectionMonth -= 12;
                $year++;
            }
            
            // Variation aléatoire de ±10% pour rendre la projection plus réaliste
            $randomFactor = rand(90, 110) / 100;
            $projectedAmount = $adjustedProjection * $randomFactor;
            
            $this->revenueProjection[] = [
                'month' => date('F', mktime(0, 0, 0, $projectionMonth, 1, $year)),
                'amount' => round($projectedAmount, 2)
            ];
        }
        
        // Tendance des paiements (pourcentage d'augmentation/diminution par mois)
        $this->paymentTrend = [];
        $previousMonthAmount = 0;
        
        for ($i = 1; $i <= 12; $i++) {
            $currentMonthAmount = $this->paymentsByMonth[$i];
            $percentChange = 0;
            
            if ($previousMonthAmount > 0) {
                $percentChange = round((($currentMonthAmount - $previousMonthAmount) / $previousMonthAmount) * 100, 1);
            }
            
            $this->paymentTrend[$i] = $percentChange;
            $previousMonthAmount = $currentMonthAmount > 0 ? $currentMonthAmount : $previousMonthAmount;
        }

        // Charger les statistiques académiques basées sur les moyennes
        $this->loadAcademicStats();
    }

    public function loadAcademicStats()
    {
        if (!$this->activeYear) {
            return;
        }
        
        // Récupérer toutes les moyennes pour l'année active
        $averages = \App\Models\Average::where('school_year_id', $this->activeYear->id)->get();
        
        if ($averages->isEmpty()) {
            return;
        }
        
        // Moyennes par période
        $this->averagesByPeriod = [
            'Trimestre 1' => [
                'count' => $averages->where('period', 'Trimestre 1')->count(),
                'avg' => $averages->where('period', 'Trimestre 1')->avg('value') ?? 0,
                'pass_rate' => $averages->where('period', 'Trimestre 1')->where('value', '>=', 10)->count() / max(1, $averages->where('period', 'Trimestre 1')->count()) * 100
            ],
            'Trimestre 2' => [
                'count' => $averages->where('period', 'Trimestre 2')->count(),
                'avg' => $averages->where('period', 'Trimestre 2')->avg('value') ?? 0,
                'pass_rate' => $averages->where('period', 'Trimestre 2')->where('value', '>=', 10)->count() / max(1, $averages->where('period', 'Trimestre 2')->count()) * 100
            ],
            'Trimestre 3' => [
                'count' => $averages->where('period', 'Trimestre 3')->count(),
                'avg' => $averages->where('period', 'Trimestre 3')->avg('value') ?? 0,
                'pass_rate' => $averages->where('period', 'Trimestre 3')->where('value', '>=', 10)->count() / max(1, $averages->where('period', 'Trimestre 3')->count()) * 100
            ],
            'Annuelle' => [
                'count' => $averages->where('period', 'Annuelle')->count(),
                'avg' => $averages->where('period', 'Annuelle')->avg('value') ?? 0,
                'pass_rate' => $averages->where('period', 'Annuelle')->where('value', '>=', 10)->count() / max(1, $averages->where('period', 'Annuelle')->count()) * 100
            ]
        ];
        
        // Performance académique globale
        $this->academicPerformance = [
            'average' => $averages->avg('value') ?? 0,
            'median' => $this->calculateMedian($averages->pluck('value')->toArray()),
            'highest' => $averages->max('value') ?? 0,
            'lowest' => $averages->min('value') ?? 0,
            'passRate' => $averages->where('value', '>=', 10)->count() / max(1, $averages->count()) * 100
        ];
        
        // Performance par niveau
        $this->performanceByLevel = [];
        $this->averagesByLevel = [];
        $this->passRateByLevel = [];
        
        // Récupérer tous les niveaux qui ont des classes avec des moyennes
        $levels = Level::whereHas('classes.averages', function($query) {
            $query->where('school_year_id', $this->activeYear->id);
        })->get();
        
        foreach ($levels as $level) {
            $levelAverages = \App\Models\Average::whereHas('classe', function($query) use ($level) {
                $query->where('level_id', $level->id);
            })->where('school_year_id', $this->activeYear->id)->get();
            
            if ($levelAverages->isNotEmpty()) {
                $this->performanceByLevel[$level->id] = [
                    'name' => $level->libelle,
                    'average' => $levelAverages->avg('value') ?? 0,
                    'count' => $levelAverages->count(),
                    'passRate' => $levelAverages->where('value', '>=', 10)->count() / $levelAverages->count() * 100
                ];
                
                // Moyennes par niveau et par période
                $this->averagesByLevel[$level->id] = [
                    'name' => $level->libelle,
                    'Trimestre 1' => $levelAverages->where('period', 'Trimestre 1')->avg('value') ?? 0,
                    'Trimestre 2' => $levelAverages->where('period', 'Trimestre 2')->avg('value') ?? 0,
                    'Trimestre 3' => $levelAverages->where('period', 'Trimestre 3')->avg('value') ?? 0,
                    'Annuelle' => $levelAverages->where('period', 'Annuelle')->avg('value') ?? 0
                ];
                
                // Taux de réussite par niveau
                $this->passRateByLevel[$level->id] = [
                    'name' => $level->libelle,
                    'passRate' => $levelAverages->where('value', '>=', 10)->count() / $levelAverages->count() * 100
                ];
            }
        }
        
        // Performance par classe
        $this->performanceByClass = [];
        $this->averagesByClass = [];
        $this->passRateByClass = [];
        
        $classes = Classe::whereHas('averages', function($query) {
            $query->where('school_year_id', $this->activeYear->id);
        })->get();
        
        foreach ($classes as $classe) {
            $classeAverages = \App\Models\Average::where('classe_id', $classe->id)
                ->where('school_year_id', $this->activeYear->id)
                ->get();
            
            if ($classeAverages->isNotEmpty()) {
                $this->performanceByClass[$classe->id] = [
                    'name' => $classe->libelle,
                    'average' => $classeAverages->avg('value') ?? 0,
                    'count' => $classeAverages->count(),
                    'passRate' => $classeAverages->where('value', '>=', 10)->count() / $classeAverages->count() * 100
                ];
                
                // Moyennes par classe et par période
                $this->averagesByClass[$classe->id] = [
                    'name' => $classe->name,
                    'Trimestre 1' => $classeAverages->where('period', 'Trimestre 1')->avg('value') ?? 0,
                    'Trimestre 2' => $classeAverages->where('period', 'Trimestre 2')->avg('value') ?? 0,
                    'Trimestre 3' => $classeAverages->where('period', 'Trimestre 3')->avg('value') ?? 0,
                    'Annuelle' => $classeAverages->where('period', 'Annuelle')->avg('value') ?? 0
                ];
                
                // Taux de réussite par classe
                $this->passRateByClass[$classe->id] = [
                    'name' => $classe->name,
                    'passRate' => $classeAverages->where('value', '>=', 10)->count() / $classeAverages->count() * 100
                ];
            }
        }
    }
    
    private function calculateMedian($array)
    {
        if (empty($array)) {
            return 0;
        }
        
        sort($array);
        $count = count($array);
        $middle = floor($count / 2);
        
        if ($count % 2 === 0) {
            return ($array[$middle - 1] + $array[$middle]) / 2;
        } else {
            return $array[$middle];
        }
    }

    // Méthode pour réinitialiser toutes les statistiques à zéro
    private function resetStats()
    {
        $this->totalStudents = 0;
        $this->totalClasses = 0;
        $this->totalLevels = 0;
        $this->totalPayments = 0;
        $this->totalRevenue = 0;
        $this->totalExpectedRevenue = 0;
        $this->paymentRate = 0;
        $this->studentsPerClass = [];
        $this->studentsPerLevel = [];
        $this->recentPayments = [];
        $this->unpaidStudents = 0;
        $this->maleStudents = 0;
        $this->femaleStudents = 0;
        $this->genderRatio = '0% / 0%';
        $this->paymentsByMonth = array_fill(1, 12, 0);
        
        // Réinitialiser les nouveaux KPIs
        $this->attendanceRate = 0;
        $this->attendanceByClass = [];
        $this->enrollmentTrend = [];
        $this->previousYearStudents = 0;
        $this->enrollmentGrowth = 0;
        $this->revenueProjection = [];
        $this->paymentTrend = array_fill(1, 12, 0);
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
