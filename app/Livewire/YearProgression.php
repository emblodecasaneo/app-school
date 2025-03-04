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
    /** @var array */
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
        
        // Récupérer toutes les années scolaires pour la sélection (y compris l'année active)
        $this->availableYears = SchoolYear::orderBy('created_at', 'desc')->get();
        
        // Récupérer TOUTES les classes disponibles pour la destination sans aucun filtre
        // Utiliser une requête directe à la base de données
        $this->targetClasses = Classe::orderBy('libelle')->get();
        
        // Vérifier si des classes sont disponibles
        if (count($this->targetClasses) === 0) {
            $this->message = "Attention: Aucune classe n'est disponible. Veuillez créer des classes avant de procéder à la progression.";
            $this->messageType = "error";
        } else {
            // Afficher un message de confirmation
            $this->message = count($this->targetClasses) . " classes disponibles pour la destination.";
            $this->messageType = "success";
        }
        
        // Sélectionner automatiquement l'année précédente comme année source
        if ($this->activeYear && count($this->availableYears) > 1) {
            // Trouver l'année précédente (différente de l'année active)
            foreach ($this->availableYears as $year) {
                if ($year->id !== $this->activeYear->id) {
                    $this->selectedYear = $year->id;
                    $this->previousYear = $year;
                    // Charger les classes pour cette année
                    $this->updatedSelectedYear($year->id);
                    break;
                }
            }
        }
    }

    public function updatedSelectedYear($value)
    {
        if ($value) {
            $this->previousYear = SchoolYear::find($value);
            
            // Vérifier si l'année sélectionnée est l'année active
            if ($this->activeYear && $this->previousYear && $this->previousYear->id === $this->activeYear->id) {
                $this->message = "Vous ne pouvez pas sélectionner l'année active comme année source.";
                $this->messageType = "error";
                $this->selectedYear = null;
                $this->previousYear = null;
                $this->classes = collect([]);
                return;
            }
            
            // Récupérer les classes qui ont des attributions dans l'année sélectionnée
            $this->classes = Classe::whereHas('attributions', function($query) use ($value) {
                $query->where('school_year_id', $value);
            })->orderBy('libelle')->get();

            
            $this->selectedStudents = [];
            $this->students = [];
            
            if (count($this->classes) === 0) {
                $this->message = "Aucune classe n'a d'inscriptions pour cette année scolaire.";
                $this->messageType = "error";
            } else {
                $this->message = count($this->classes) . " classes trouvées avec des inscriptions pour cette année.";
                $this->messageType = "success";
            }
        } else {
            $this->classes = collect([]);
            $this->selectedClass = null;
            $this->students = [];
            $this->selectedStudents = [];
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
            
            if ($attributions->isEmpty()) {
                $this->message = "Aucun élève trouvé dans cette classe pour l'année sélectionnée.";
                $this->messageType = "error";
                $this->students = [];
                $this->selectedStudents = [];
                return;
            }
            
            // Convertir les attributions en tableau d'élèves
            $this->students = [];
            foreach ($attributions as $attribution) {
                if ($attribution->student) {
                    $this->students[] = [
                        'id' => $attribution->student->id,
                        'matricule' => $attribution->student->matricule,
                        'nom' => $attribution->student->nom,
                        'prenom' => $attribution->student->prenom,
                        'selected' => false
                    ];
                }
            }
            
            // Réinitialiser la sélection
            $this->selectedStudents = [];
            $this->message = count($this->students) . " élèves trouvés dans cette classe.";
            $this->messageType = "success";
        } else {
            $this->students = [];
            $this->selectedStudents = [];
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
        if (empty($this->selectedStudents)) {
            $this->message = "Veuillez sélectionner au moins un élève à promouvoir.";
            $this->messageType = "error";
            return;
        }

        if (empty($this->targetClass)) {
            $this->message = "Veuillez sélectionner une classe de destination.";
            $this->messageType = "error";
            return;
        }

        if (!$this->activeYear) {
            $this->message = "Aucune année scolaire active. Veuillez activer une année scolaire avant de procéder à la progression.";
            $this->messageType = "error";
            return;
        }

        $targetClasse = Classe::find($this->targetClass);
        if (!$targetClasse) {
            $this->message = "La classe de destination sélectionnée n'existe pas.";
            $this->messageType = "error";
            return;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($this->selectedStudents as $studentId) {
            // Vérifier si l'élève existe
            $student = Student::find($studentId);
            if (!$student) {
                $errorCount++;
                continue;
            }

            // Vérifier si l'élève est déjà attribué à une classe pour l'année active
            $existingAttribution = Attributtion::where('student_id', $studentId)
                ->where('school_year_id', $this->activeYear->id)
                ->first();

            if ($existingAttribution) {
                // Si l'élève est déjà attribué à la même classe, ignorer
                if ($existingAttribution->classe_id == $this->targetClass) {
                    continue;
                }
                
                // Sinon, mettre à jour l'attribution existante
                $existingAttribution->classe_id = $this->targetClass;
                $existingAttribution->save();
                $successCount++;
            } else {
                // Créer une nouvelle attribution pour l'année active
                try {
                    Attributtion::create([
                        'student_id' => $studentId,
                        'classe_id' => $this->targetClass,
                        'school_year_id' => $this->activeYear->id
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }
        }

        if ($successCount > 0) {
            $this->progressionCompleted = true;
            $this->progressionMessage = "{$successCount} élève(s) promu(s) avec succès vers la classe {$targetClasse->libelle}.";
            if ($errorCount > 0) {
                $this->progressionMessage .= " {$errorCount} élève(s) n'ont pas pu être promus.";
            }
            $this->message = $this->progressionMessage;
            $this->messageType = "success";
            
            // Réinitialiser la sélection
            $this->selectedStudents = [];
            foreach ($this->students as $index => $student) {
                $this->students[$index]['selected'] = false;
            }
            
            // Émettre un événement pour rafraîchir les autres composants
            $this->dispatch('studentsProgressed');
            $this->dispatch('refresh-dashboard');
        } else {
            $this->message = "Aucun élève n'a pu être promu. Veuillez vérifier vos sélections.";
            $this->messageType = "error";
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
        
        // Forcer la récupération de toutes les classes pour la destination
        // en utilisant une requête directe à la base de données
        $allClasses = Classe::orderBy('libelle')->get();
        
        // Mettre à jour la propriété targetClasses
        $this->targetClasses = $allClasses;
        
        // Afficher un message de débogage
        if ($allClasses->isEmpty()) {
            $this->message = "ATTENTION: Aucune classe n'a été trouvée dans la base de données. Veuillez créer des classes.";
            $this->messageType = "error";
        }
        
        return view('livewire.year-progression', [
            'activeYear' => $this->activeYear,
            'availableYears' => $this->availableYears,
            'targetClasses' => $allClasses,
            'schoolYears' => $schoolYears
        ]);
    }
}
