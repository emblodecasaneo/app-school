<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class YearProgression extends Component
{
    use WithPagination;

    public $activeYear;
    public $previousYear;
    public $availableYears;
    public $selectedYear;
    public $selectedClass;
    public $targetClass;
    public $students = [];
    public $selectedStudents = [];
    public $classes = [];
    public $targetClasses = [];
    public $search = '';
    public $progressionCompleted = false;
    public $progressionMessage = '';
    public $message = '';
    public $messageType = 'success';
    public $isCreatingNewYear = false;
    public $newYearName = '';
    public $showConfirmation = false;
    public $yearToActivate;

    public function mount()
    {
        // Récupérer l'année active
        $this->activeYear = SchoolYear::where('active', '1')->first();
        
        // Récupérer toutes les années scolaires pour la sélection
        $this->availableYears = SchoolYear::where('active', '0')->orderBy('created_at', 'desc')->get();
        
        // Récupérer les classes de l'année active pour la cible
        $this->targetClasses = Classe::whereHas('level', function($query) {
            $query->where('school_year_id', $this->activeYear->id);
        })->get();
    }

    public function updatedSelectedYear($value)
    {
        if ($value) {
            $this->previousYear = SchoolYear::find($value);
            $this->classes = Classe::whereHas('level', function($query) use ($value) {
                $query->where('school_year_id', $value);
            })->get();
            $this->selectedStudents = [];
            $this->students = [];
        }
    }

    public function updatedSelectedClass($value)
    {
        if ($value) {
            // Récupérer les élèves de la classe sélectionnée pour l'année précédente
            $attributions = Attributtion::where('classe_id', $value)
                ->where('school_year_id', $this->selectedYear)
                ->with('student')
                ->get();
            
            $this->students = $attributions->map(function ($attribution) {
                return [
                    'id' => $attribution->student->id,
                    'matricule' => $attribution->student->matricule,
                    'nom' => $attribution->student->nom,
                    'prenom' => $attribution->student->prenom,
                    'selected' => false
                ];
            })->toArray();
        }
    }

    public function toggleSelectAll()
    {
        $allSelected = count($this->selectedStudents) === count($this->students);
        
        if ($allSelected) {
            $this->selectedStudents = [];
            foreach ($this->students as $index => $student) {
                $this->students[$index]['selected'] = false;
            }
        } else {
            $this->selectedStudents = collect($this->students)->pluck('id')->toArray();
            foreach ($this->students as $index => $student) {
                $this->students[$index]['selected'] = true;
            }
        }
    }

    public function toggleStudent($studentId)
    {
        $index = array_search($studentId, $this->selectedStudents);
        
        if ($index !== false) {
            unset($this->selectedStudents[$index]);
            $this->selectedStudents = array_values($this->selectedStudents);
            
            // Mettre à jour l'état selected dans le tableau students
            foreach ($this->students as $key => $student) {
                if ($student['id'] == $studentId) {
                    $this->students[$key]['selected'] = false;
                    break;
                }
            }
        } else {
            $this->selectedStudents[] = $studentId;
            
            // Mettre à jour l'état selected dans le tableau students
            foreach ($this->students as $key => $student) {
                if ($student['id'] == $studentId) {
                    $this->students[$key]['selected'] = true;
                    break;
                }
            }
        }
    }

    public function progressStudents()
    {
        if (empty($this->selectedStudents) || !$this->targetClass) {
            $this->progressionMessage = "Veuillez sélectionner des élèves et une classe cible.";
            return;
        }

        $targetClassObj = Classe::find($this->targetClass);
        $count = 0;

        foreach ($this->selectedStudents as $studentId) {
            // Vérifier si l'élève n'est pas déjà attribué à l'année active
            $existingAttribution = Attributtion::where('student_id', $studentId)
                ->where('school_year_id', $this->activeYear->id)
                ->first();
            
            if (!$existingAttribution) {
                // Créer une nouvelle attribution pour l'année active
                Attributtion::create([
                    'student_id' => $studentId,
                    'classe_id' => $this->targetClass,
                    'school_year_id' => $this->activeYear->id,
                ]);
                $count++;
            }
        }

        $this->progressionCompleted = true;
        $this->progressionMessage = "{$count} élève(s) ont été promus vers la classe {$targetClassObj->libelle} pour l'année {$this->activeYear->libelle}.";
        
        // Réinitialiser la sélection
        $this->selectedStudents = [];
        foreach ($this->students as $index => $student) {
            $this->students[$index]['selected'] = false;
        }
    }

    public function activateSchoolYear($yearId)
    {
        try {
            // Désactiver l'année active actuelle
            if ($this->activeYear) {
                $this->activeYear->active = '0';
                $this->activeYear->save();
            }
            
            // Activer la nouvelle année
            $newActiveYear = SchoolYear::find($yearId);
            if ($newActiveYear) {
                $newActiveYear->active = '1';
                $newActiveYear->save();
                
                // Mettre à jour les propriétés du composant
                $this->activeYear = $newActiveYear;
                $this->availableYears = SchoolYear::where('active', '0')->orderBy('created_at', 'desc')->get();
                
                // Mettre à jour les classes cibles pour la nouvelle année active
                $this->targetClasses = Classe::whereHas('level', function($query) {
                    $query->where('school_year_id', $this->activeYear->id);
                })->get();
                
                $this->message = "L'année scolaire {$newActiveYear->libelle} a été activée avec succès. Toutes les opérations (inscriptions, paiements, etc.) concerneront désormais cette année scolaire.";
                $this->messageType = 'success';
                
                // Réinitialiser les variables de progression
                $this->selectedYear = null;
                $this->selectedClass = null;
                $this->students = [];
                $this->selectedStudents = [];
                $this->classes = [];
            } else {
                $this->message = "Impossible de trouver l'année scolaire sélectionnée.";
                $this->messageType = 'error';
            }
        } catch (\Exception $e) {
            $this->message = "Une erreur est survenue lors de l'activation de l'année scolaire: " . $e->getMessage();
            $this->messageType = 'error';
        }
        
        $this->showConfirmation = false;
    }

    public function confirmActivation($yearId)
    {
        $this->yearToActivate = $yearId;
        $this->showConfirmation = true;
    }

    public function cancelActivation()
    {
        $this->showConfirmation = false;
        $this->yearToActivate = null;
    }

    public function createNewYear()
    {
        $this->validate([
            'newYearName' => 'required|string|min:4|max:20|unique:school_years,school_year'
        ], [
            'newYearName.required' => 'Le nom de l\'année scolaire est requis.',
            'newYearName.unique' => 'Cette année scolaire existe déjà.',
            'newYearName.min' => 'Le nom de l\'année scolaire doit contenir au moins 4 caractères.',
            'newYearName.max' => 'Le nom de l\'année scolaire ne doit pas dépasser 20 caractères.'
        ]);
        
        try {
            // Créer la nouvelle année scolaire
            $newYear = new SchoolYear();
            $newYear->school_year = $this->newYearName;
            $newYear->libelle = $this->newYearName;
            $newYear->active = '0'; // Par défaut, la nouvelle année n'est pas active
            $newYear->curent_year = date('Y');
            $newYear->save();
            
            // Mettre à jour la liste des années disponibles
            $this->availableYears = SchoolYear::where('active', '0')->orderBy('created_at', 'desc')->get();
            
            $this->message = "L'année scolaire {$this->newYearName} a été créée avec succès. Vous pouvez maintenant l'activer ou créer des niveaux et des classes pour cette année.";
            $this->messageType = 'success';
            
            // Réinitialiser le formulaire
            $this->newYearName = '';
            $this->isCreatingNewYear = false;
        } catch (\Exception $e) {
            $this->message = "Une erreur est survenue lors de la création de l'année scolaire: " . $e->getMessage();
            $this->messageType = 'error';
        }
    }
    
    public function toggleCreateYearForm()
    {
        $this->isCreatingNewYear = !$this->isCreatingNewYear;
        if (!$this->isCreatingNewYear) {
            $this->newYearName = '';
        }
    }

    public function render()
    {
        $schoolYears = SchoolYear::orderBy('created_at', 'desc')->get();
        
        return view('livewire.year-progression', [
            'activeYear' => $this->activeYear,
            'availableYears' => $this->availableYears,
            'targetClasses' => $this->targetClasses,
            'schoolYears' => $schoolYears
        ]);
    }
}
