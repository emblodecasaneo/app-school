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
        
        // Ne pas écraser les classes chargées par loadClasses
        // $this->classes = \App\Models\Classe::all();
        
        // Ne plus appeler la méthode de débogage
        // $this->debugDatabase();
    }
    
    public function loadClasses()
    {
        if ($this->activeYear) {
            // Charger uniquement les classes qui ont des élèves attribués pour l'année active
            $this->classes = Classe::whereHas('attributions', function($query) {
                $query->where('school_year_id', $this->activeYear->id);
            })->orderBy('libelle')->get();
            
            // Ajouter un message d'information sans utiliser count() qui cause l'erreur
            $classCount = count($this->classes);
            session()->flash('info', "Chargement de {$classCount} classes ayant des élèves attribués pour l'année active.");
        } else {
            $this->classes = collect();
            session()->flash('warning', "Aucune année scolaire active n'est définie.");
        }
    }
    
    public function loadSubjects()
    {
        try {
            // Charger toutes les matières actives, indépendamment de la classe sélectionnée
            $this->subjects = \App\Models\Subject::select('id', 'name', 'description', 'category', 'is_active', 'created_at', 'updated_at')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            
            // Ajouter un message d'information
            $subjectCount = count($this->subjects);
            if ($subjectCount > 0) {
                session()->flash('info', "{$subjectCount} matière(s) active(s) disponible(s).");
            } else {
                session()->flash('warning', "Aucune matière active n'est disponible. Veuillez activer des matières.");
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du chargement des matières : ' . $e->getMessage());
            $this->subjects = collect();
        }
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedClasse()
    {
        $this->resetPage();
        
        // Ne pas réinitialiser la matière sélectionnée
        // $this->selectedSubject = '';
        
        // Ajouter des messages de débogage
        $classe = \App\Models\Classe::find($this->selectedClasse);
        $classeName = $classe ? $classe->libelle : 'Inconnue';
        
        session()->flash('info', "Classe sélectionnée: {$classeName} (ID: {$this->selectedClasse})");
        
        // Vérifier si des élèves sont associés à cette classe
        $studentsCount = \App\Models\Student::whereHas('attributions', function($query) {
            $query->where('school_year_id', $this->activeYear ? $this->activeYear->id : 0)
                ->where('classe_id', $this->selectedClasse);
        })->count();
        
        if ($studentsCount == 0) {
            session()->flash('warning', "Aucun élève trouvé pour cette classe. Veuillez vérifier que des élèves sont inscrits dans cette classe pour l'année scolaire active.");
        }
        
        // Ne plus appeler la méthode de débogage
        // $this->debugDatabase();
        
        $this->loadStudents();
    }
    
    public function updatedSelectedSubject()
    {
        $this->resetPage();
        
        // Récupérer le coefficient de la matière pour la classe sélectionnée
        if ($this->selectedClasse && $this->selectedSubject) {
            $classe = \App\Models\Classe::find($this->selectedClasse);
            $subject = \App\Models\Subject::find($this->selectedSubject);
            
            if ($classe && $subject) {
                // Récupérer le coefficient depuis la relation pivot
                $this->coefficient = $classe->getSubjectCoefficient($this->selectedSubject);
                
                session()->flash('info', "Coefficient de {$subject->name} pour cette classe : {$this->coefficient}");
            }
        }
        
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
            session()->flash('warning', "Veuillez sélectionner une classe et vérifier qu'une année scolaire est active.");
            return;
        }
        
        // Récupérer directement les élèves de la classe sélectionnée
        $students = \App\Models\Student::whereHas('attributions', function($query) {
            $query->where('school_year_id', $this->activeYear->id)
                ->where('classe_id', $this->selectedClasse);
        })->get();
        
        if ($students->isEmpty()) {
            $classe = \App\Models\Classe::find($this->selectedClasse);
            $classeName = $classe ? $classe->libelle : 'Inconnue';
            session()->flash('warning', "Aucun élève trouvé pour la classe {$classeName}. Veuillez vérifier que des élèves sont inscrits dans cette classe pour l'année scolaire active.");
            $this->studentGrades = [];
            return;
        }
        
        $this->studentGrades = [];
        $notesCount = 0;
        
        // Vérifier si une matière est sélectionnée
        $subjectName = '';
        if ($this->selectedSubject) {
            $subject = \App\Models\Subject::find($this->selectedSubject);
            if ($subject) {
                $subjectName = $subject->name;
            } else {
                session()->flash('warning', "La matière sélectionnée n'existe pas ou n'est pas active.");
                $this->selectedSubject = '';
            }
        }
        
        foreach ($students as $student) {
            // Récupérer la note existante si elle existe
            $grade = null;
            if ($this->selectedSubject) {
                $grade = \App\Models\Grade::where('student_id', $student->id)
                    ->where('classe_id', $this->selectedClasse)
                    ->where('school_year_id', $this->activeYear->id)
                    ->where('subject_id', $this->selectedSubject)
                    ->where('period', $this->selectedPeriod)
                    ->where('type', $this->selectedType)
                    ->first();
                
                if ($grade) {
                    $notesCount++;
                }
            }
            
            $this->studentGrades[$student->id] = [
                'student_id' => $student->id,
                'name' => $student->nom . ' ' . $student->prenom,
                'matricule' => $student->matricule,
                'value' => $grade ? $grade->value : null,
                'grade_id' => $grade ? $grade->id : null,
            ];
        }
        
        // Afficher un message sur les notes existantes seulement s'il y en a
        if ($notesCount > 0 && $this->selectedSubject) {
            session()->flash('info', "{$notesCount} note(s) existante(s) pour {$subjectName} ({$this->selectedPeriod}, {$this->selectedType})");
        }
    }
    
    public function saveGrades()
    {
        try {
            $this->validate();
            
            if (empty($this->studentGrades)) {
                session()->flash('error', 'Aucun élève trouvé pour cette classe.');
                return;
            }
            
            if (!$this->selectedSubject) {
                session()->flash('error', 'Veuillez sélectionner une matière.');
                return;
            }
            
            // Vérifier si la matière existe et est active
            $subject = \App\Models\Subject::find($this->selectedSubject);
            if (!$subject) {
                session()->flash('error', 'La matière sélectionnée n\'existe pas. ID: ' . $this->selectedSubject);
                return;
            }
            
            // Récupérer le coefficient de la matière pour la classe sélectionnée
            $classe = \App\Models\Classe::find($this->selectedClasse);
            if ($classe) {
                $this->coefficient = $classe->getSubjectCoefficient($this->selectedSubject);
            }
            
            $savedCount = 0;
            $updatedCount = 0;
            $errorCount = 0;
            $errorMessages = [];
            
            foreach ($this->studentGrades as $studentId => $studentGradeData) {
                if (isset($studentGradeData['value']) && $studentGradeData['value'] !== null && $studentGradeData['value'] !== '') {
                    try {
                        // Vérifier si la note existe déjà
                        $isUpdate = !empty($studentGradeData['grade_id']);
                        
                        // Vérifier si la valeur de la note est valide
                        $value = $studentGradeData['value'];
                        if (!is_numeric($value)) {
                            throw new \Exception('La valeur de la note doit être un nombre.');
                        }
                        
                        // Convertir en nombre
                        $value = floatval($value);
                        
                        if ($value < 0 || $value > 20) {
                            throw new \Exception('La valeur de la note doit être entre 0 et 20.');
                        }
                        
                        // Préparer les données pour la note
                        $gradeData = [
                            'student_id' => $studentId,
                            'classe_id' => $this->selectedClasse,
                            'school_year_id' => $this->activeYear->id,
                            'subject_id' => $this->selectedSubject,
                            'period' => $this->selectedPeriod,
                            'type' => $this->selectedType,
                            'value' => $value,
                            'coefficient' => $this->coefficient,
                            'comment' => $this->gradeComment,
                            'date' => $this->gradeDate ?? now()->format('Y-m-d'),
                        ];
                        
                        // Créer ou mettre à jour la note
                        if ($isUpdate) {
                            // Mettre à jour une note existante
                            $grade = \App\Models\Grade::find($studentGradeData['grade_id']);
                            if ($grade) {
                                $grade->fill($gradeData);
                                $grade->save();
                                $updatedCount++;
                            } else {
                                // La note n'existe plus, en créer une nouvelle
                                \App\Models\Grade::create($gradeData);
                                $savedCount++;
                            }
                        } else {
                            // Créer une nouvelle note
                            \App\Models\Grade::create($gradeData);
                            $savedCount++;
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMessages[] = 'Erreur pour l\'élève ' . ($studentGradeData['name'] ?? $studentId) . ' : ' . $e->getMessage();
                    }
                }
            }
            
            $message = '';
            if ($savedCount > 0) {
                $message .= $savedCount . ' nouvelle(s) note(s) enregistrée(s). ';
            }
            if ($updatedCount > 0) {
                $message .= $updatedCount . ' note(s) mise(s) à jour. ';
            }
            if ($errorCount > 0) {
                $message .= $errorCount . ' erreur(s) lors de l\'enregistrement. ';
            }
            
            if ($savedCount > 0 || $updatedCount > 0) {
                session()->flash('success', $message);
                $this->loadStudents(); // Recharger les données
            } else if ($errorCount > 0) {
                session()->flash('error', $message);
                // Afficher les messages d'erreur spécifiques
                foreach ($errorMessages as $errorMessage) {
                    session()->flash('error', $errorMessage);
                }
            } else {
                session()->flash('warning', 'Aucune note n\'a été enregistrée. Veuillez saisir au moins une note.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur générale lors de l\'enregistrement des notes : ' . $e->getMessage());
        }
    }
    
    public function calculatePeriodAverages()
    {
        try {
            if (!$this->selectedClasse || !$this->activeYear) {
                session()->flash('error', 'Veuillez sélectionner une classe et vérifier qu\'une année scolaire est active.');
                return;
            }

            $classeId = $this->selectedClasse;
            $schoolYearId = $this->activeYear->id;
            $period = $this->selectedPeriod;

            // Récupérer les élèves de la classe
            $students = \App\Models\Student::whereHas('attributions', function($query) use ($classeId, $schoolYearId) {
                $query->where('classe_id', $classeId)
                    ->where('school_year_id', $schoolYearId);
            })->get();

            if ($students->isEmpty()) {
                session()->flash('warning', 'Aucun élève trouvé dans cette classe.');
                return;
            }

            // Récupérer la classe et ses matières
            $classe = \App\Models\Classe::find($classeId);
            if (!$classe) {
                session()->flash('error', 'Classe non trouvée.');
                return;
            }

            // Récupérer toutes les matières actives associées à la classe
            $allSubjects = $classe->getActiveSubjects();
            $subjectsList = [];
            foreach ($allSubjects as $subject) {
                $subjectsList[$subject->id] = $subject->name;
            }

            if (empty($subjectsList)) {
                session()->flash('warning', 'Aucune matière active n\'est associée à cette classe.');
                return;
            }

            $savedCount = 0;
            $subjectsWithoutGrades = [];

            foreach ($students as $student) {
                // Récupérer les notes de l'élève pour la période sélectionnée
                $grades = \App\Models\Grade::where('student_id', $student->id)
                    ->where('school_year_id', $schoolYearId)
                    ->where('period', $period)
                    ->get()
                    ->groupBy('subject_id'); // Grouper par subject_id au lieu de subject

                // Vérifier les matières sans notes pour cet élève
                $studentSubjectsWithoutGrades = [];
                foreach ($subjectsList as $subjectId => $subjectName) {
                    if (!$grades->has($subjectId)) {
                        $studentSubjectsWithoutGrades[] = $subjectName;
                    }
                }

                // Ajouter les matières sans notes à la liste globale
                foreach ($studentSubjectsWithoutGrades as $subjectName) {
                    if (!in_array($subjectName, $subjectsWithoutGrades)) {
                        $subjectsWithoutGrades[] = $subjectName;
                    }
                }

                // Calculer la moyenne de l'élève pour la période
                $average = $this->gradeService->calculatePeriodAverage($student->id, $period, $schoolYearId, $classeId);
                
                if ($average !== null) {
                    // Vérifier si une moyenne existe déjà
                    $existingAverage = \App\Models\Average::where('student_id', $student->id)
                        ->where('school_year_id', $schoolYearId)
                        ->where('classe_id', $classeId)
                        ->where('period', $period)
                        ->first();
                    
                    if ($existingAverage) {
                        $existingAverage->update([
                            'value' => $average,
                            'updated_at' => now(),
                        ]);
                    } else {
                        \App\Models\Average::create([
                            'student_id' => $student->id,
                            'school_year_id' => $schoolYearId,
                            'classe_id' => $classeId,
                            'period' => $period,
                            'value' => $average,
                        ]);
                    }
                    
                    $savedCount++;
                }
            }

            // Calculer les rangs
            $this->gradeService->calculateAndSaveRanks($classeId, $period, $schoolYearId);

            // Afficher un message d'avertissement pour les matières sans notes
            if (!empty($subjectsWithoutGrades) && $period !== 'Annuelle') {
                session()->flash('warning', 'Attention : les matières suivantes n\'ont pas de notes pour la période ' .
                    $period . ' : ' . implode(', ', $subjectsWithoutGrades));
            }

            if ($savedCount > 0) {
                session()->flash('success', $savedCount . ' moyenne(s) calculée(s) et enregistrée(s) pour la période ' . $period);
            } else {
                session()->flash('warning', 'Aucune moyenne n\'a pu être calculée. Vérifiez que des notes ont été saisies.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du calcul des moyennes : ' . $e->getMessage());
        }
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
            session()->flash('warning', "Veuillez sélectionner une classe et vérifier qu'une année scolaire est active.");
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
        
        $students = $query->orderBy('nom')->orderBy('prenom')->get();
        
        if ($students->isEmpty()) {
            $classe = \App\Models\Classe::find($this->selectedClasse);
            $classeName = $classe ? $classe->libelle : 'Inconnue';
            session()->flash('warning', "Aucun élève trouvé pour la classe {$classeName}. Veuillez vérifier que des élèves sont inscrits dans cette classe pour l'année scolaire active.");
        }
        
        return $students;
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
        try {
            // Retourner toutes les matières actives, indépendamment de la classe sélectionnée
            return \App\Models\Subject::select('id', 'name', 'description', 'category', 'is_active', 'created_at', 'updated_at')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la récupération des matières : ' . $e->getMessage());
            return collect([]);
        }
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
    
    /**
     * Récupère le nom de la matière sélectionnée
     */
    public function getSelectedSubjectNameProperty()
    {
        if (!$this->selectedSubject) {
            return '';
        }
        
        $subject = \App\Models\Subject::find($this->selectedSubject);
        return $subject ? $subject->name : '';
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
    
    public function debugDatabase()
    {
        $debug = [];
        
        // Vérifier l'année scolaire active
        $activeYear = \App\Models\SchoolYear::where('active', '1')->first();
        $debug[] = "Année scolaire active: " . ($activeYear ? $activeYear->school_year . " (ID: {$activeYear->id})" : "Aucune");
        
        // Vérifier les classes
        $classes = \App\Models\Classe::all();
        $debug[] = "Nombre de classes: " . $classes->count();
        if ($classes->count() > 0) {
            $debug[] = "Classes: " . $classes->pluck('libelle')->implode(', ');
        }
        
        // Vérifier les élèves
        $students = \App\Models\Student::all();
        $debug[] = "Nombre d'élèves: " . $students->count();
        
        // Vérifier les attributions
        $attributions = \App\Models\Attributtion::all();
        $debug[] = "Nombre d'attributions: " . $attributions->count();
        
        if ($attributions->count() > 0) {
            // Vérifier les attributions pour l'année active
            if ($activeYear) {
                $attributionsForActiveYear = \App\Models\Attributtion::where('school_year_id', $activeYear->id)->get();
                $debug[] = "Attributions pour l'année active: " . $attributionsForActiveYear->count();
                
                // Vérifier les attributions par classe
                foreach ($classes as $classe) {
                    $attributionsForClass = \App\Models\Attributtion::where('school_year_id', $activeYear->id)
                        ->where('classe_id', $classe->id)
                        ->get();
                    $debug[] = "Attributions pour la classe {$classe->libelle}: " . $attributionsForClass->count();
                    
                    if ($attributionsForClass->count() > 0) {
                        $studentIds = $attributionsForClass->pluck('student_id');
                        $studentsInClass = \App\Models\Student::whereIn('id', $studentIds)->get();
                        $debug[] = "Élèves dans la classe {$classe->libelle}: " . $studentsInClass->count();
                        if ($studentsInClass->count() > 0) {
                            $debug[] = "Noms des élèves: " . $studentsInClass->pluck('nom')->implode(', ');
                        }
                    }
                }
            }
        }
        
        // Afficher les informations de débogage
        session()->flash('debug', implode("<br>", $debug));
    }
    
    /**
     * Met à jour le coefficient de la matière sélectionnée pour la classe sélectionnée
     */
    public function updateCoefficient()
    {
        if (!$this->selectedClasse || !$this->selectedSubject) {
            session()->flash('error', 'Veuillez sélectionner une classe et une matière.');
            return;
        }
        
        $this->validate([
            'coefficient' => 'required|numeric|min:0.1|max:10',
        ]);
        
        try {
            $classe = \App\Models\Classe::findOrFail($this->selectedClasse);
            $subject = \App\Models\Subject::findOrFail($this->selectedSubject);
            
            // Vérifier si la matière est déjà associée à la classe
            $exists = $classe->subjects()->where('subject_id', $this->selectedSubject)->exists();
            
            if ($exists) {
                // Mettre à jour le coefficient
                $classe->updateSubjectCoefficient($this->selectedSubject, $this->coefficient);
                session()->flash('success', "Le coefficient de {$subject->name} pour la classe {$classe->libelle} a été mis à jour.");
            } else {
                // Associer la matière à la classe avec le coefficient spécifié
                $classe->addSubject($this->selectedSubject, $this->coefficient);
                session()->flash('success', "{$subject->name} a été associée à la classe {$classe->libelle} avec un coefficient de {$this->coefficient}.");
            }
            
            // Recharger les étudiants pour mettre à jour les notes avec le nouveau coefficient
            $this->loadStudents();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la mise à jour du coefficient : " . $e->getMessage());
        }
    }
}
