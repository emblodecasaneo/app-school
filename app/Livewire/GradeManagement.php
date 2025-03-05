<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Average;
use App\Services\GradeCalculationService;
use Illuminate\Support\Collection;

class GradeManagement extends Component
{
    use WithPagination;
    
    // Propriétés pour la recherche et le filtrage
    public $search = '';
    public $selectedClasse = '';
    public $selectedSubject = '';
    public $selectedPeriod = 'Trimestre 1';
    public $selectedType = 'Devoir';
    
    // Propriétés pour l'ajout/modification de notes
    public $studentGrades = [];
    public $coefficient = 1;
    public $gradeDate;
    public $gradeComment = '';
    
    // Propriétés pour la gestion des formulaires
    public $showGradeForm = false;
    public $isEditing = false;
    public $confirmingGradeDeletion = false;
    public $gradeIdToDelete;
    
    // Propriétés pour les listes déroulantes
    public $classes = [];
    public $subjects = [];
    public $periods = ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'];
    public $gradeTypes = ['Devoir', 'Examen', 'Contrôle', 'Projet'];
    
    // Propriétés pour les statistiques
    public $activeYear;
    public $studentsWithGrades = [];
    
    // Écouteurs d'événements
    protected $listeners = [
        'gradeAdded' => '$refresh',
        'gradeUpdated' => '$refresh',
        'gradeDeleted' => '$refresh'
    ];
    
    // Service de calcul des notes
    protected $gradeService;
    
    // Règles de validation
    protected $rules = [
        'selectedClasse' => 'required',
        'selectedSubject' => 'required',
        'selectedPeriod' => 'required',
        'selectedType' => 'required',
        'coefficient' => 'required|numeric|min:1|max:10',
        'gradeDate' => 'required|date',
        'studentGrades.*.value' => 'nullable|numeric|min:0|max:20',
    ];
    
    public function boot(GradeCalculationService $gradeService)
    {
        $this->gradeService = $gradeService;
    }
    
    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
        $this->gradeDate = now()->format('Y-m-d');
        
        if (!$this->activeYear) {
            session()->flash('error', 'Aucune année scolaire active n\'est définie.');
            return;
        }
        
        $this->loadClasses();
        $this->loadSubjects();
        $this->classes = \App\Models\Classe::all();
    }
    
    public function loadClasses()
    {
        if ($this->activeYear) {
            $this->classes = Classe::whereHas('attributions', function($query) {
                $query->where('school_year_id', $this->activeYear->id);
            })->orderBy('libelle')->get();
        } else {
            $this->classes = [];
        }
    }
    
    public function loadSubjects()
    {
        $this->subjects = [
            'Français',
            'Mathématiques',
            'Histoire-Géographie',
            'Anglais',
            'Physique-Chimie',
            'SVT',
            'Éducation Physique',
            'Arts Plastiques',
            'Musique',
            'Technologie',
            'Philosophie'
        ];
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedClasse()
    {
        $this->resetPage();
        $this->loadStudents();
    }
    
    public function updatedSelectedSubject()
    {
        $this->loadStudents();
    }
    
    public function updatedSelectedPeriod()
    {
        $this->loadStudents();
    }
    
    public function openGradeForm($studentId = null)
    {
        $this->resetValidation();
        $this->resetGradeForm();
        
        if ($studentId) {
            $this->studentGrades[$studentId] = [
                'student_id' => $studentId,
                'name' => '',
                'matricule' => '',
                'value' => null,
                'grade_id' => null,
            ];
        }
        
        $this->showGradeForm = true;
        $this->isEditing = false;
    }
    
    public function closeGradeForm()
    {
        $this->showGradeForm = false;
        $this->resetGradeForm();
    }
    
    public function resetGradeForm()
    {
        $this->studentGrades = [];
        $this->coefficient = 1;
        $this->gradeDate = now()->format('Y-m-d');
        $this->gradeComment = '';
        $this->isEditing = false;
    }
    
    public function loadStudents()
    {
        if (!$this->selectedClasse || !$this->activeYear) {
            $this->studentGrades = [];
            return;
        }
        
        $students = $this->students;
        $this->studentGrades = [];
        
        foreach ($students as $student) {
            // Récupérer la note existante si elle existe
            $grade = Grade::where('student_id', $student->id)
                ->where('classe_id', $this->selectedClasse)
                ->where('school_year_id', $this->activeYear->id)
                ->where('subject', $this->selectedSubject)
                ->where('period', $this->selectedPeriod)
                ->where('type', $this->selectedType)
                ->first();
            
            $this->studentGrades[$student->id] = [
                'student_id' => $student->id,
                'name' => $student->nom . ' ' . $student->prenom,
                'matricule' => $student->matricule,
                'value' => $grade ? $grade->value : null,
                'grade_id' => $grade ? $grade->id : null,
            ];
        }
    }
    
    public function saveGrades()
    {
        $this->validate();
        
        if (empty($this->studentGrades)) {
            session()->flash('error', 'Aucun élève trouvé pour cette classe.');
            return;
        }
        
        $savedCount = 0;
        
        foreach ($this->studentGrades as $studentId => $gradeData) {
            if (isset($gradeData['value']) && $gradeData['value'] !== null && $gradeData['value'] !== '') {
                Grade::updateOrCreate(
                    [
                        'id' => $gradeData['grade_id'] ?? null,
                    ],
                    [
                        'student_id' => $studentId,
                        'classe_id' => $this->selectedClasse,
                        'school_year_id' => $this->activeYear->id,
                        'subject' => $this->selectedSubject,
                        'period' => $this->selectedPeriod,
                        'type' => $this->selectedType,
                        'value' => $gradeData['value'],
                        'coefficient' => $this->coefficient,
                        'comment' => $this->gradeComment,
                        'date' => $this->gradeDate,
                    ]
                );
                
                $savedCount++;
            }
        }
        
        if ($savedCount > 0) {
            session()->flash('success', $savedCount . ' note(s) enregistrée(s) avec succès.');
            $this->loadStudents(); // Recharger les données
        } else {
            session()->flash('error', 'Aucune note n\'a été enregistrée. Veuillez saisir au moins une note.');
        }
    }
    
    public function calculatePeriodAverages()
    {
        if (!$this->selectedClasse || !$this->selectedPeriod || !$this->activeYear) {
            session()->flash('error', 'Veuillez sélectionner une classe et une période.');
            return;
        }
        
        // Vérifier si toutes les matières ont des notes pour cette période
        $subjectsWithoutGrades = $this->checkSubjectsWithoutGrades();
        if (!empty($subjectsWithoutGrades)) {
            session()->flash('warning', 'Attention : les matières suivantes n\'ont pas de notes pour la période ' . 
                $this->selectedPeriod . ' : ' . implode(', ', $subjectsWithoutGrades));
        }
        
        // Calculer les moyennes
        $count = $this->gradeService->calculateAndSaveClassPeriodAverages(
            $this->selectedClasse,
            $this->selectedPeriod,
            $this->activeYear->id
        );
        
        // Calculer et enregistrer les rangs
        $this->gradeService->calculateAndSaveRanks(
            $this->selectedClasse,
            $this->selectedPeriod,
            $this->activeYear->id
        );
        
        if ($count > 0) {
            session()->flash('success', 'Les moyennes ont été calculées pour ' . $count . ' élève(s) et les rangs ont été attribués.');
        } else {
            session()->flash('error', 'Aucune moyenne n\'a pu être calculée. Vérifiez que des notes ont été saisies pour cette période.');
        }
    }
    
    /**
     * Vérifie quelles matières n'ont pas de notes pour la période sélectionnée
     * 
     * @return array Liste des matières sans notes
     */
    private function checkSubjectsWithoutGrades(): array
    {
        if (!$this->selectedClasse || !$this->selectedPeriod || !$this->activeYear) {
            return [];
        }
        
        // Récupérer toutes les matières de la classe
        $classe = Classe::find($this->selectedClasse);
        if (!$classe) {
            return [];
        }
        
        // Récupérer la liste des matières depuis la méthode subjects()
        $allSubjects = [];
        foreach ($classe->subjects() as $subject) {
            $allSubjects[$subject['name']] = $subject['name'];
        }
        
        // Récupérer les matières qui ont des notes pour cette période
        $subjectsWithGrades = Grade::where('classe_id', $this->selectedClasse)
            ->where('period', $this->selectedPeriod)
            ->where('school_year_id', $this->activeYear->id)
            ->distinct('subject')
            ->pluck('subject')
            ->toArray();
        
        // Identifier les matières sans notes
        $subjectsWithoutGrades = array_diff(array_keys($allSubjects), $subjectsWithGrades);
        
        // Retourner un tableau associatif des matières sans notes
        $result = [];
        foreach ($subjectsWithoutGrades as $subject) {
            $result[$subject] = $subject;
        }
        
        return $result;
    }
    
    public function calculateAnnualAverages()
    {
        if (!$this->selectedClasse || !$this->activeYear) {
            session()->flash('error', 'Veuillez sélectionner une classe.');
            return;
        }
        
        // Vérifier si toutes les périodes ont des moyennes calculées
        $periodsWithoutAverages = $this->checkPeriodsWithoutAverages();
        if (!empty($periodsWithoutAverages)) {
            session()->flash('warning', 'Attention : les périodes suivantes n\'ont pas encore de moyennes calculées : ' . implode(', ', $periodsWithoutAverages));
        }
        
        // Calculer les moyennes annuelles
        $count = $this->gradeService->calculateAndSaveClassAnnualAverages(
            $this->selectedClasse,
            $this->activeYear->id
        );
        
        // Calculer et enregistrer les rangs pour la période annuelle
        $this->gradeService->calculateAndSaveRanks(
            $this->selectedClasse,
            'Annuelle',
            $this->activeYear->id
        );
        
        if ($count > 0) {
            session()->flash('success', 'Les moyennes annuelles ont été calculées pour ' . $count . ' élève(s) et les rangs ont été attribués.');
        } else {
            session()->flash('error', 'Aucune moyenne annuelle n\'a pu être calculée. Vérifiez que les moyennes trimestrielles ont été calculées.');
        }
    }
    
    /**
     * Vérifie quelles périodes n'ont pas encore de moyennes calculées
     * 
     * @return array Liste des périodes sans moyennes
     */
    private function checkPeriodsWithoutAverages(): array
    {
        if (!$this->selectedClasse || !$this->activeYear) {
            return [];
        }
        
        $requiredPeriods = ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'];
        $periodsWithAverages = Average::where('classe_id', $this->selectedClasse)
            ->where('school_year_id', $this->activeYear->id)
            ->whereIn('period', $requiredPeriods)
            ->distinct('period')
            ->pluck('period')
            ->toArray();
        
        return array_diff($requiredPeriods, $periodsWithAverages);
    }
    
    public function getStudentsProperty()
    {
        if (!$this->selectedClasse || !$this->activeYear) {
            return collect([]);
        }
        
        $query = Student::query()
            ->with(['attributions' => function($query) {
                $query->where('school_year_id', $this->activeYear->id);
                $query->with('classe');
            }])
            ->whereHas('attributions', function($query) {
                $query->where('school_year_id', $this->activeYear->id)
                    ->where('classe_id', $this->selectedClasse);
            });
        
        if ($this->search) {
            $query->where(function($query) {
                $query->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy('nom')->orderBy('prenom')->get();
    }
    
    public function getClassesProperty()
    {
        if (!$this->activeYear) {
            return collect([]);
        }
        
        return Classe::orderBy('niveau')->orderBy('libelle')->get();
    }
    
    public function getSubjectsProperty()
    {
        // Récupérer les matières disponibles pour la classe sélectionnée
        // Vous pouvez adapter cette méthode selon votre structure de données
        return [
            'Mathématiques',
            'Français',
            'Anglais',
            'Histoire-Géographie',
            'Sciences',
            'Physique-Chimie',
            'SVT',
            'Éducation Physique',
            'Arts Plastiques',
            'Musique',
            'Technologie',
            'Philosophie',
            'Économie',
        ];
    }
    
    public function getClassAveragesProperty()
    {
        if (!$this->selectedClasse || !$this->selectedPeriod || !$this->activeYear) {
            return collect([]);
        }
        
        return Average::with('student')
            ->where('classe_id', $this->selectedClasse)
            ->where('school_year_id', $this->activeYear->id)
            ->where('period', $this->selectedPeriod)
            ->orderBy('rank')
            ->get();
    }
    
    /**
     * Alias pour calculatePeriodAverages pour maintenir la compatibilité
     */
    public function calculateAverages()
    {
        return $this->calculatePeriodAverages();
    }
    
    public function render()
    {
        return view('livewire.grade-management', [
            'students' => $this->students,
            'classes' => $this->classes,
            'subjects' => $this->subjects,
            'classAverages' => $this->classAverages,
        ]);
    }
}
