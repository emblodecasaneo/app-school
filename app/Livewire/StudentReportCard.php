<?php

namespace App\Livewire;

use App\Models\Average;
use App\Models\Classe;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Services\GradeCalculationService;
use Livewire\Component;
use Livewire\WithPagination;

class StudentReportCard extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'tailwind';
    
    public $search = '';
    public $selectedStudent = null;
    public $selectedClasse = '';
    public $selectedPeriod = 'Trimestre 1';
    public $activeYear;
    public $showReportCard = false;
    public $studentData = [];
    public $subjectAverages = [];
    public $generalAverage = null;
    public $rank = null;
    public $classAverage = null;
    public $highestAverage = null;
    public $lowestAverage = null;
    public $teacherComment = '';
    public $decision = '';
    public $periods = ['Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Annuelle'];
    
    protected $gradeService;
    
    protected $listeners = [
        'refresh' => '$refresh'
    ];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedClasse' => ['except' => ''],
        'selectedPeriod' => ['except' => 'Trimestre 1'],
    ];
    
    protected $rules = [
        'teacherComment' => 'nullable|string|max:500',
        'decision' => 'nullable|string|max:100',
    ];
    
    public function boot(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }
    
    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedStudent = null;
        $this->showReportCard = false;
    }
    
    public function updatedSelectedClasse()
    {
        $this->resetPage();
        $this->selectedStudent = null;
        $this->showReportCard = false;
    }
    
    public function updatedSelectedPeriod()
    {
        if ($this->selectedStudent) {
            $this->loadReportCard();
        }
    }
    
    public function selectStudent($studentId)
    {
        $this->selectedStudent = $studentId;
        $this->loadReportCard();
    }
    
    public function loadReportCard()
    {
        if (!$this->selectedStudent || !$this->activeYear) {
            $this->showReportCard = false;
            return;
        }
        
        try {
            $student = Student::with(['attributions' => function($query) {
                $query->where('school_year_id', $this->activeYear->id);
            }])->findOrFail($this->selectedStudent);
            
            // Vérifier si l'élève a une attribution pour l'année active
            if ($student->attributions->isEmpty()) {
                session()->flash('error', "L'élève n'est pas inscrit pour l'année scolaire active.");
                $this->showReportCard = false;
                return;
            }
            
            // Récupérer la classe actuelle de l'élève pour l'année active
            $currentAttribution = $student->attributions->first();
            if (!$currentAttribution) {
                session()->flash('error', 'Aucune inscription trouvée pour cet élève dans l\'année active.');
                $this->showReportCard = false;
                return;
            }
            
            $classeId = $currentAttribution->classe_id;
            $classeName = $currentAttribution->classe->libelle ?? 'Classe inconnue';
            
            // Récupérer les informations de l'élève
            $this->studentData = [
                'id' => $student->id,
                'name' => $student->nom . ' ' . $student->prenom,
                'matricule' => $student->matricule,
                'classe' => $classeName,
                'classe_id' => $classeId,
            ];
            
            // Récupérer toutes les notes de l'élève pour la période sélectionnée
            $grades = Grade::where('student_id', $student->id)
                ->where('school_year_id', $this->activeYear->id)
                ->where('period', $this->selectedPeriod)
                ->with('subject')
                ->get()
                ->groupBy(function($grade) {
                    return $grade->subject_id;
                });
            
            // Vérifier si toutes les matières ont des notes
            $classe = Classe::find($classeId);
            if ($classe) {
                // Récupérer la liste des matières depuis la méthode getActiveSubjects()
                $allSubjects = [];
                $subjectIds = [];
                $activeSubjects = $classe->getActiveSubjects();
                foreach ($activeSubjects as $subject) {
                    $allSubjects[] = $subject->name;
                    $subjectIds[] = $subject->id;
                }
                
                $subjectsWithGrades = $grades->keys()->toArray();
                $subjectsWithoutGradesIds = array_diff($subjectIds, $subjectsWithGrades);
                
                // Récupérer les noms des matières sans notes
                $subjectsWithoutGrades = [];
                if (!empty($subjectsWithoutGradesIds)) {
                    foreach ($activeSubjects as $subject) {
                        if (in_array($subject->id, $subjectsWithoutGradesIds)) {
                            $subjectsWithoutGrades[] = $subject->name;
                        }
                    }
                }
                
                if (!empty($subjectsWithoutGrades) && $this->selectedPeriod !== 'Annuelle') {
                    session()->flash('warning', 'Attention : les matières suivantes n\'ont pas de notes pour la période ' . 
                        $this->selectedPeriod . ' : ' . implode(', ', $subjectsWithoutGrades));
                }
            }
            
            // Calculer les moyennes par matière
            $this->subjectAverages = [];
            
            // Si la période est "Annuelle", récupérer les moyennes trimestrielles pour chaque matière
            if ($this->selectedPeriod === 'Annuelle') {
                // Récupérer les moyennes par matière pour chaque trimestre
                $trimesterGrades = [];
                foreach (['Trimestre 1', 'Trimestre 2', 'Trimestre 3'] as $period) {
                    $periodGrades = Grade::where('student_id', $student->id)
                        ->where('school_year_id', $this->activeYear->id)
                        ->where('period', $period)
                        ->with('subject')
                        ->get()
                        ->groupBy(function($grade) {
                            return $grade->subject_id;
                        });
                    
                    foreach ($periodGrades as $subjectId => $grades) {
                        if (!isset($trimesterGrades[$subjectId])) {
                            $trimesterGrades[$subjectId] = [];
                        }
                        
                        // Calculer la moyenne pour cette matière et ce trimestre
                        $totalWeightedValue = 0;
                        $totalCoefficient = 0;
                        
                        foreach ($grades as $grade) {
                            $totalWeightedValue += $grade->value * $grade->coefficient;
                            $totalCoefficient += $grade->coefficient;
                        }
                        
                        if ($totalCoefficient > 0) {
                            $trimesterGrades[$subjectId][$period] = [
                                'average' => round($totalWeightedValue / $totalCoefficient, 2),
                                'coefficient' => $totalCoefficient
                            ];
                        }
                    }
                }
                
                // Calculer la moyenne annuelle pour chaque matière
                foreach ($trimesterGrades as $subjectId => $periods) {
                    if (!empty($periods)) {
                        $totalAverage = 0;
                        $periodCount = count($periods);
                        
                        foreach ($periods as $periodData) {
                            $totalAverage += $periodData['average'];
                        }
                        
                        $annualAverage = $periodCount > 0 ? round($totalAverage / $periodCount, 2) : null;
                        
                        // Utiliser le coefficient du dernier trimestre disponible
                        $lastPeriod = array_key_last($periods);
                        $coefficient = $periods[$lastPeriod]['coefficient'] ?? 1;
                        
                        // Récupérer l'objet matière
                        $subject = \App\Models\Subject::find($subjectId);
                        $subjectName = $subject ? $subject->name : "Matière ID: {$subjectId}";
                        
                        $this->subjectAverages[$subjectName] = [
                            'average' => $annualAverage,
                            'coefficient' => $coefficient,
                            'grades' => [],
                            'trimester_averages' => $periods
                        ];
                    }
                }
            } else {
                foreach ($grades as $subjectId => $subjectGrades) {
                    $totalWeightedValue = 0;
                    $totalCoefficient = 0;
                    
                    foreach ($subjectGrades as $grade) {
                        $totalWeightedValue += $grade->value * $grade->coefficient;
                        $totalCoefficient += $grade->coefficient;
                    }
                    
                    if ($totalCoefficient > 0) {
                        // Récupérer l'objet matière
                        $subject = \App\Models\Subject::find($subjectId);
                        $subjectName = $subject ? $subject->name : "Matière ID: {$subjectId}";
                        
                        $this->subjectAverages[$subjectName] = [
                            'average' => round($totalWeightedValue / $totalCoefficient, 2),
                            'coefficient' => $totalCoefficient,
                            'grades' => $subjectGrades->toArray(),
                        ];
                    }
                }
            }
            
            // Récupérer la moyenne générale depuis la table des moyennes
            $average = Average::where('student_id', $student->id)
                ->where('classe_id', $classeId)
                ->where('school_year_id', $this->activeYear->id)
                ->where('period', $this->selectedPeriod)
                ->first();
            
            if ($average) {
                $this->generalAverage = $average->value;
                $this->rank = $average->rank;
                $this->teacherComment = $average->teacher_comment;
                $this->decision = $average->decision;
            } else {
                // Calculer la moyenne générale si elle n'existe pas encore
                $this->generalAverage = $this->calculateGeneralAverage();
                $this->rank = null;
                $this->teacherComment = '';
                $this->decision = '';
            }
            
            // Calculer les statistiques de classe
            $this->calculateClassStatistics($classeId);
            
            $this->showReportCard = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Élève introuvable.');
            $this->showReportCard = false;
        }
    }
    
    private function calculateGeneralAverage()
    {
        if (empty($this->subjectAverages)) {
            return null;
        }
        
        $totalWeightedAverage = 0;
        $totalCoefficient = 0;
        
        foreach ($this->subjectAverages as $subject => $data) {
            $totalWeightedAverage += $data['average'] * $data['coefficient'];
            $totalCoefficient += $data['coefficient'];
        }
        
        if ($totalCoefficient === 0) {
            return null;
        }
        
        return round($totalWeightedAverage / $totalCoefficient, 2);
    }
    
    private function calculateClassStatistics($classeId)
    {
        // Récupérer toutes les moyennes de la classe pour la période sélectionnée
        $classAverages = Average::where('classe_id', $classeId)
            ->where('school_year_id', $this->activeYear->id)
            ->where('period', $this->selectedPeriod)
            ->pluck('value')
            ->toArray();
        
        if (!empty($classAverages)) {
            $this->classAverage = round(array_sum($classAverages) / count($classAverages), 2);
            $this->highestAverage = max($classAverages);
            $this->lowestAverage = min($classAverages);
        } else {
            $this->classAverage = null;
            $this->highestAverage = null;
            $this->lowestAverage = null;
        }
    }
    
    public function saveComment()
    {
        if (!$this->selectedStudent || !$this->activeYear || !$this->studentData['classe_id']) {
            return;
        }
        
        Average::updateOrCreate(
            [
                'student_id' => $this->selectedStudent,
                'classe_id' => $this->studentData['classe_id'],
                'school_year_id' => $this->activeYear->id,
                'period' => $this->selectedPeriod
            ],
            [
                'value' => $this->generalAverage,
                'teacher_comment' => $this->teacherComment,
                'decision' => $this->decision
            ]
        );
        
        session()->flash('success', 'Commentaire et décision enregistrés avec succès.');
    }
    
    public function calculateAllAverages()
    {
        if (!$this->selectedStudent || !$this->activeYear || !$this->studentData['classe_id']) {
            return;
        }
        
        $classeId = $this->studentData['classe_id'];
        $studentId = $this->selectedStudent;
        
        // Vérifier si la période sélectionnée est "Annuelle"
        if ($this->selectedPeriod === 'Annuelle') {
            // Vérifier si toutes les périodes ont des moyennes calculées
            $requiredPeriods = ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'];
            $periodsWithAverages = Average::where('student_id', $studentId)
                ->where('classe_id', $classeId)
                ->where('school_year_id', $this->activeYear->id)
                ->whereIn('period', $requiredPeriods)
                ->distinct('period')
                ->pluck('period')
                ->toArray();
            
            $periodsWithoutAverages = array_diff($requiredPeriods, $periodsWithAverages);
            
            if (!empty($periodsWithoutAverages)) {
                session()->flash('warning', 'Attention : les périodes suivantes n\'ont pas encore de moyennes calculées : ' . implode(', ', $periodsWithoutAverages));
            }
        }
        
        // Calculer les moyennes trimestrielles
        $calculatedTrimesters = 0;
        foreach (['Trimestre 1', 'Trimestre 2', 'Trimestre 3'] as $period) {
            $average = $this->gradeService->calculatePeriodAverage($studentId, $period, $this->activeYear->id, $classeId);
            
            if ($average !== null) {
                Average::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'classe_id' => $classeId,
                        'school_year_id' => $this->activeYear->id,
                        'period' => $period
                    ],
                    [
                        'value' => $average
                    ]
                );
                
                $calculatedTrimesters++;
            }
        }
        
        // Calculer la moyenne annuelle si au moins un trimestre est disponible
        if ($calculatedTrimesters > 0) {
            $annualAverage = $this->gradeService->calculateAnnualAverage($studentId, $this->activeYear->id, $classeId);
            
            if ($annualAverage !== null) {
                Average::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'classe_id' => $classeId,
                        'school_year_id' => $this->activeYear->id,
                        'period' => 'Annuelle'
                    ],
                    [
                        'value' => $annualAverage
                    ]
                );
                
                if ($calculatedTrimesters === 3) {
                    session()->flash('success', 'Toutes les moyennes ont été calculées avec succès, y compris la moyenne annuelle.');
                } else {
                    session()->flash('success', 'La moyenne annuelle a été calculée sur la base des ' . $calculatedTrimesters . ' trimestre(s) disponible(s).');
                }
            } else {
                session()->flash('success', 'Les moyennes trimestrielles ont été calculées, mais la moyenne annuelle n\'a pas pu être calculée.');
            }
        } else {
            session()->flash('success', 'Aucune moyenne trimestrielle n\'a pu être calculée. Veuillez d\'abord saisir des notes.');
        }
        
        // Recharger le bulletin
        $this->loadReportCard();
    }
    
    /**
     * Calcule et attribue les rangs aux élèves pour la période sélectionnée
     */
    public function calculateRanks()
    {
        if (!$this->selectedStudent || !$this->activeYear || !$this->studentData['classe_id'] || !$this->selectedPeriod) {
            return;
        }
        
        $classeId = $this->studentData['classe_id'];
        
        // Utiliser le service pour calculer et enregistrer les rangs
        $this->gradeService->calculateAndSaveRanks(
            $classeId,
            $this->selectedPeriod,
            $this->activeYear->id
        );
        
        session()->flash('success', 'Les rangs ont été calculés et attribués avec succès.');
        
        // Recharger le bulletin pour afficher le nouveau rang
        $this->loadReportCard();
    }
    
    public function getStudentsProperty()
    {
        if (!$this->activeYear) {
            return collect([]);
        }
        
        $query = Student::query()
            ->with(['attributions' => function($query) {
                $query->where('school_year_id', $this->activeYear->id);
                $query->with('classe');
            }])
            ->whereHas('attributions', function($query) {
                $query->where('school_year_id', $this->activeYear->id);
                
                if ($this->selectedClasse) {
                    $query->where('classe_id', $this->selectedClasse);
                }
            });
        
        if ($this->search && strlen(trim($this->search)) > 0) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function($query) use ($searchTerm) {
                $query->where('nom', 'like', $searchTerm)
                    ->orWhere('prenom', 'like', $searchTerm)
                    ->orWhere('matricule', 'like', $searchTerm);
            });
        }
        
        return $query->orderBy('nom')->orderBy('prenom')->paginate(10);
    }
    
    public function getClassesProperty()
    {
        if (!$this->activeYear) {
            return [];
        }
        
        return Classe::whereHas('attributions', function($query) {
            $query->where('school_year_id', $this->activeYear->id);
        })->orderBy('libelle')->get();
    }
    
    /**
     * Prépare le bulletin pour l'impression
     */
    public function printReportCard()
    {
        if (!$this->showReportCard) {
            session()->flash('error', 'Veuillez d\'abord sélectionner un élève et charger son bulletin.');
            return;
        }
        
        // Envoyer un événement JavaScript pour déclencher l'impression
        $this->dispatch('print-report-card');
    }
    
    public function render()
    {
        return view('livewire.student-report-card', [
            'students' => $this->students,
            'classes' => $this->classes,
        ]);
    }
} 