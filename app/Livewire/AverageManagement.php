<?php

namespace App\Livewire;

use App\Models\Average;
use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class AverageManagement extends Component
{
    use WithPagination;

    // Filtres
    public $search = '';
    public $selectedClasse = '';
    public $selectedPeriod = '';
    public $activeYear;

    // Gestion en masse
    public $isBulkMode = false;
    public $studentAverages = [];
    public $classStudents = [];
    public $showSuccessMessage = false;
    public $successMessage = '';

    // Formulaire d'ajout/édition
    public $averageId;
    public $studentId;
    public $classeId;
    public $period = 'Trimestre 1';
    public $value;
    public $rank;
    public $teacherComment;
    public $decision;
    public $isOpen = false;
    public $isEditing = false;
    public $confirmingDeletion = false;

    protected $rules = [
        'studentAverages.*.value' => 'nullable|numeric|min:0|max:20',
        'studentAverages.*.rank' => 'nullable|integer|min:1',
        'studentAverages.*.teacher_comment' => 'nullable|string',
        'studentAverages.*.decision' => 'nullable|string',
        'studentId' => 'required|exists:students,id',
        'classeId' => 'required|exists:classes,id',
        'period' => 'required|in:Trimestre 1,Trimestre 2,Trimestre 3,Annuelle',
        'value' => 'required|numeric|min:0|max:20',
        'rank' => 'nullable|integer|min:1',
        'teacherComment' => 'nullable|string',
        'decision' => 'nullable|string',
    ];

    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedClasse()
    {
        $this->resetPage();
        $this->loadClassStudents();
    }

    public function updatedSelectedPeriod()
    {
        $this->resetPage();
        $this->loadClassStudents();
    }

    public function toggleBulkMode()
    {
        $this->isBulkMode = !$this->isBulkMode;
        if ($this->isBulkMode && $this->selectedClasse) {
            $this->loadClassStudents();
        }
    }

    public function loadClassStudents()
    {
        if (!$this->selectedClasse || !$this->activeYear) {
            $this->classStudents = [];
            $this->studentAverages = [];
            return;
        }

        // Récupérer tous les élèves inscrits dans cette classe pour l'année active
        $this->classStudents = Attributtion::where('classe_id', $this->selectedClasse)
            ->where('school_year_id', $this->activeYear->id)
            ->with('student')
            ->get()
            ->map(function($attribution) {
                return $attribution->student;
            });

        // Initialiser ou charger les moyennes existantes pour chaque élève
        $this->studentAverages = [];
        foreach ($this->classStudents as $student) {
            // Vérifier si une moyenne existe déjà pour cet élève dans cette période
            $average = Average::where('student_id', $student->id)
                ->where('classe_id', $this->selectedClasse)
                ->where('school_year_id', $this->activeYear->id)
                ->where('period', $this->selectedPeriod)
                ->first();

            if ($average) {
                $this->studentAverages[$student->id] = [
                    'value' => $average->value,
                    'rank' => $average->rank,
                    'teacher_comment' => $average->teacher_comment,
                    'decision' => $average->decision,
                    'exists' => true,
                    'average_id' => $average->id
                ];
            } else {
                $this->studentAverages[$student->id] = [
                    'value' => '',
                    'rank' => '',
                    'teacher_comment' => '',
                    'decision' => '',
                    'exists' => false,
                    'average_id' => null
                ];
            }
        }
    }

    public function saveBulkAverages()
    {
        // Validation simplifiée pour les champs obligatoires uniquement
        $this->validate([
            'studentAverages.*.value' => 'nullable|numeric|min:0|max:20',
        ]);

        $savedCount = 0;

        foreach ($this->studentAverages as $studentId => $data) {
            // Ignorer les élèves sans moyenne
            if (empty($data['value']) || !is_numeric($data['value'])) {
                continue;
            }

            $averageData = [
                'student_id' => $studentId,
                'classe_id' => $this->selectedClasse,
                'school_year_id' => $this->activeYear->id,
                'period' => $this->selectedPeriod,
                'value' => $data['value'],
                'rank' => $data['rank'] ?? null,
                'teacher_comment' => $data['teacher_comment'] ?? null,
                'decision' => $data['decision'] ?? null,
            ];

            if (!empty($data['exists']) && !empty($data['average_id'])) {
                Average::find($data['average_id'])->update($averageData);
            } else {
                Average::create($averageData);
            }
            
            $savedCount++;
        }

        $this->successMessage = "Moyennes enregistrées avec succès pour $savedCount élèves.";
        $this->showSuccessMessage = true;
        
        // Recharger les données
        $this->loadClassStudents();
    }

    public function calculateAllAnnualAverages()
    {
        if (!$this->selectedClasse || !$this->activeYear) {
            return;
        }

        $attributions = Attributtion::where('classe_id', $this->selectedClasse)
            ->where('school_year_id', $this->activeYear->id)
            ->get();
            
        $studentIds = [];
        foreach ($attributions as $attribution) {
            $studentIds[] = $attribution->student_id;
        }

        $count = 0;
        foreach ($studentIds as $studentId) {
            $trimesters = Average::where('student_id', $studentId)
                ->where('classe_id', $this->selectedClasse)
                ->where('school_year_id', $this->activeYear->id)
                ->whereIn('period', ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'])
                ->get();
                
            if ($trimesters->count() === 3) {
                $annualValue = $trimesters->avg('value');
                
                // Vérifier si une moyenne annuelle existe déjà
                $annual = Average::where('student_id', $studentId)
                    ->where('classe_id', $this->selectedClasse)
                    ->where('school_year_id', $this->activeYear->id)
                    ->where('period', 'Annuelle')
                    ->first();
                    
                if ($annual) {
                    $annual->update(['value' => $annualValue]);
                } else {
                    Average::create([
                        'student_id' => $studentId,
                        'classe_id' => $this->selectedClasse,
                        'school_year_id' => $this->activeYear->id,
                        'period' => 'Annuelle',
                        'value' => $annualValue,
                    ]);
                }
                $count++;
            }
        }
        
        if ($count > 0) {
            $this->successMessage = "Moyennes annuelles calculées pour $count élèves.";
            $this->showSuccessMessage = true;
        } else {
            $this->successMessage = "Aucune moyenne annuelle calculée. Assurez-vous que tous les trimestres sont complétés.";
            $this->showSuccessMessage = true;
        }
    }

    public function closeSuccessMessage()
    {
        $this->showSuccessMessage = false;
    }

    public function openModal($studentId = null)
    {
        $this->resetValidation();
        $this->resetForm();
        
        if ($studentId) {
            $this->studentId = $studentId;
        }
        
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isEditing = false;
    }

    public function resetForm()
    {
        $this->studentId = '';
        $this->classeId = $this->selectedClasse ?: '';
        $this->period = $this->selectedPeriod ?: 'Trimestre 1';
        $this->value = '';
        $this->rank = '';
        $this->teacherComment = '';
        $this->decision = '';
        $this->averageId = null;
        $this->isEditing = false;
    }

    public function saveAverage()
    {
        $this->validate();

        $data = [
            'student_id' => $this->studentId,
            'classe_id' => $this->classeId,
            'school_year_id' => $this->activeYear->id,
            'period' => $this->period,
            'value' => $this->value,
            'rank' => $this->rank,
            'teacher_comment' => $this->teacherComment,
            'decision' => $this->decision,
        ];

        if ($this->isEditing) {
            Average::find($this->averageId)->update($data);
            session()->flash('message', 'Moyenne mise à jour avec succès.');
        } else {
            // Vérifier si une moyenne existe déjà pour cet élève, cette classe, cette période et cette année
            $exists = Average::where('student_id', $this->studentId)
                ->where('classe_id', $this->classeId)
                ->where('school_year_id', $this->activeYear->id)
                ->where('period', $this->period)
                ->exists();

            if ($exists) {
                session()->flash('error', 'Une moyenne existe déjà pour cet élève dans cette période.');
                return;
            }

            Average::create($data);
            session()->flash('message', 'Moyenne ajoutée avec succès.');
        }

        $this->closeModal();
    }

    public function editAverage($averageId)
    {
        $this->isEditing = true;
        $this->averageId = $averageId;
        
        $average = Average::find($averageId);
        
        $this->studentId = $average->student_id;
        $this->classeId = $average->classe_id;
        $this->period = $average->period;
        $this->value = $average->value;
        $this->rank = $average->rank;
        $this->teacherComment = $average->teacher_comment;
        $this->decision = $average->decision;
        
        $this->isOpen = true;
    }

    public function confirmDelete($averageId)
    {
        $this->confirmingDeletion = true;
        $this->averageId = $averageId;
    }

    public function deleteAverage()
    {
        Average::find($this->averageId)->delete();
        session()->flash('message', 'Moyenne supprimée avec succès.');
        $this->confirmingDeletion = false;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
    }

    public function render()
    {
        $classes = Classe::all();
        
        $averages = [];
        $students = [];
        
        if ($this->isBulkMode) {
            // En mode gestion en masse, on utilise les données déjà chargées
            $students = $this->classStudents;
        } else {
            // En mode normal, on charge les données avec pagination
            $studentsQuery = Student::query()
                ->whereHas('attributtions', function($query) {
                    $query->where('school_year_id', $this->activeYear->id)
                        ->when($this->selectedClasse, function($q) {
                            $q->where('classe_id', $this->selectedClasse);
                        });
                })
                ->when($this->search, function($query) {
                    return $query->where(function($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%')
                          ->orWhere('prenom', 'like', '%' . $this->search . '%')
                          ->orWhere('matricule', 'like', '%' . $this->search . '%');
                    });
                })
                ->with(['attributtions' => function($query) {
                    $query->where('school_year_id', $this->activeYear->id)
                          ->with('classe');
                }]);
                
            $students = $studentsQuery->paginate(10);
            
            // Charger les moyennes pour l'affichage
            $studentIds = [];
            foreach ($students as $student) {
                $studentIds[] = $student->id;
            }
            
            $averagesData = Average::query()
                ->whereIn('student_id', $studentIds)
                ->when($this->activeYear, function($query) {
                    return $query->where('school_year_id', $this->activeYear->id);
                })
                ->when($this->selectedClasse, function($query) {
                    return $query->where('classe_id', $this->selectedClasse);
                })
                ->when($this->selectedPeriod, function($query) {
                    return $query->where('period', $this->selectedPeriod);
                })
                ->get();
                
            foreach ($averagesData as $average) {
                $averages[$average->student_id][$average->period] = $average;
            }
        }
            
        return view('livewire.average-management', [
            'students' => $students,
            'classes' => $classes,
            'averages' => $averages,
            'periods' => ['Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Annuelle'],
        ]);
    }
}
