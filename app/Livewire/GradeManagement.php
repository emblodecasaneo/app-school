<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class GradeManagement extends Component
{
    use WithPagination;
    
    // Propriétés pour la recherche et le filtrage
    public $search = '';
    public $selectedClass = '';
    public $selectedSubject = '';
    public $selectedPeriod = '';
    
    // Propriétés pour l'ajout/modification de notes
    public $studentId;
    public $subject;
    public $period;
    public $value;
    public $coefficient = 1;
    public $comment;
    public $type = 'exam';
    public $gradeId;
    
    // Propriétés pour la gestion des formulaires
    public $showGradeForm = false;
    public $isEditing = false;
    public $confirmingGradeDeletion = false;
    public $gradeIdToDelete;
    
    // Propriétés pour les listes déroulantes
    public $classes = [];
    public $subjects = [];
    public $periods = ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'];
    public $types = ['exam' => 'Examen', 'devoir' => 'Devoir', 'controle' => 'Contrôle', 'projet' => 'Projet'];
    public $selectedClasse = '';
    
    // Propriétés pour les statistiques
    public $activeYear;
    public $studentsWithGrades = [];
    
    // Écouteurs d'événements
    protected $listeners = [
        'gradeAdded' => '$refresh',
        'gradeUpdated' => '$refresh',
        'gradeDeleted' => '$refresh'
    ];
    
    // Règles de validation
    protected function rules()
    {
        return [
            'studentId' => 'required|exists:students,id',
            'subject' => 'required|string|max:255',
            'period' => 'required|string|max:255',
            'value' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'comment' => 'nullable|string',
            'type' => 'required|string|in:exam,devoir,controle,projet'
        ];
    }
    
    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', 1)->first();
        
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
    
    public function updatedSelectedClass()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedSubject()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedPeriod()
    {
        $this->resetPage();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function openGradeForm($studentId = null)
    {
        $this->resetValidation();
        $this->resetGradeForm();
        
        if ($studentId) {
            $this->studentId = $studentId;
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
        $this->studentId = '';
        $this->subject = '';
        $this->period = '';
        $this->value = '';
        $this->coefficient = 1;
        $this->comment = '';
        $this->type = 'exam';
        $this->gradeId = null;
        $this->isEditing = false;
    }
    
    public function saveGrade()
    {
        // Validation using existing properties
        $this->validate([
            'studentId' => 'required|exists:students,id',
            'selectedClass' => 'required', // Using the existing property
            'subject' => 'required',
            'period' => 'required',
            'value' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:1',
            'type' => 'required',
        ]);

        // Create grade with the correct property names
        Grade::create([
            'student_id' => $this->studentId,
            'school_year_id' => $this->activeYear->id,
            'classe_id' => $this->selectedClass, // Using the existing property
            'subject' => $this->subject,
            'period' => $this->period,
            'value' => $this->value,
            'coefficient' => $this->coefficient,
            'comment' => $this->comment,
            'type' => $this->type,
        ]);
        
        session()->flash('success', 'Note ajoutée avec succès.');
        $this->dispatch('gradeAdded');
        
        $this->closeGradeForm();
    }
    
    public function editGrade($gradeId)
    {
        $grade = Grade::find($gradeId);
        
        if (!$grade) {
            session()->flash('error', 'Note introuvable.');
            return;
        }
        
        $this->gradeId = $grade->id;
        $this->studentId = $grade->student_id;
        $this->subject = $grade->subject;
        $this->period = $grade->period;
        $this->value = $grade->value;
        $this->coefficient = $grade->coefficient;
        $this->comment = $grade->comment;
        $this->type = $grade->type;
        
        $this->isEditing = true;
        $this->showGradeForm = true;
    }
    
    public function confirmGradeDeletion($gradeId)
    {
        $this->confirmingGradeDeletion = true;
        $this->gradeIdToDelete = $gradeId;
    }
    
    public function deleteGrade()
    {
        $grade = Grade::find($this->gradeIdToDelete);
        
        if ($grade) {
            $grade->delete();
            session()->flash('success', 'Note supprimée avec succès.');
            $this->dispatch('gradeDeleted');
        } else {
            session()->flash('error', 'Note introuvable.');
        }
        
        $this->confirmingGradeDeletion = false;
        $this->gradeIdToDelete = null;
    }
    
    public function cancelGradeDeletion()
    {
        $this->confirmingGradeDeletion = false;
        $this->gradeIdToDelete = null;
    }
    
    public function getStudentsProperty()
    {
        if (!$this->activeYear) {
            return [];
        }
        
        $query = Student::query()
            ->whereHas('attributions', function($query) {
                $query->where('school_year_id', $this->activeYear->id);
                
                if ($this->selectedClass) {
                    $query->where('classe_id', $this->selectedClass);
                }
            })
            ->when($this->search, function($query) {
                $query->where(function($query) {
                    $query->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('matricule', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('nom')
            ->orderBy('prenom');
        
        return $query->paginate(10);
    }
    
    public function getGradesProperty()
    {
        if (!$this->activeYear) {
            return [];
        }
        
        $query = Grade::query()
            ->where('school_year_id', $this->activeYear->id)
            ->when($this->selectedClass, function($query) {
                $query->where('classe_id', $this->selectedClass);
            })
            ->when($this->selectedSubject, function($query) {
                $query->where('subject', $this->selectedSubject);
            })
            ->when($this->selectedPeriod, function($query) {
                $query->where('period', $this->selectedPeriod);
            })
            ->orderBy('created_at', 'desc');
        
        return $query->paginate(15);
    }
    
    public function calculateStudentAverage($studentId, $period = null)
    {
        $student = Student::find($studentId);
        
        if (!$student || !$this->activeYear) {
            return 0;
        }
        
        return $student->calculateAverage($this->activeYear->id, $period);
    }
    
    public function getStudentAverageProperty()
    {
        return function($studentId, $period = null) {
            return $this->calculateStudentAverage($studentId, $period);
        };
    }
    
    public function render()
    {
        $students = $this->students;
        
        // Calculer les moyennes des étudiants
        $studentAverages = [];
        foreach ($students as $student) {
            $studentAverages[$student->id] = $this->calculateStudentAverage($student->id, $this->selectedPeriod);
        }
        
        return view('livewire.grade-management', [
            'students' => $students,
            'grades' => $this->grades,
            'studentAverages' => $studentAverages,
        ])->layout('layouts.app', ['header' => 'Gestion des Notes']);
    }
}
