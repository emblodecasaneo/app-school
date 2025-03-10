<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Student;
use Exception;
use Livewire\Component;

class CreateInscription extends Component
{
    public $level_id;
    public $matricule;
    public $classe_id;
    public $school_year_id;
    public $student_id;
    public $nom;
    public $comments;
    public $activeYear;
    
    // Nouvelles propriétés pour la recherche avancée
    public $searchQuery = '';
    public $searchResults = [];
    public $showSearchResults = false;
    
    // Propriétés pour l'affichage des informations de l'élève
    public $selectedStudent = null;
    public $studentInfo = [];
    
    // Propriétés pour les statistiques de classe
    public $selectedClassInfo = null;
    public $classeEffectif = 0;

    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
        if (!$this->activeYear) {
            $this->activeYear = SchoolYear::latest()->first();
        }
    }

    public function store(Attributtion $attributtion)
    {
        $this->validate([
            "student_id" => 'required',
            "level_id" => "required",
            "classe_id" => "required",
        ], [
            "student_id.required" => "Veuillez sélectionner un élève",
            "level_id.required" => "Veuillez sélectionner un niveau",
            "classe_id.required" => "Veuillez sélectionner une classe",
        ]);

        try {
            // Vérifier si l'élève est déjà inscrit pour cette année scolaire
            $existingInscription = Attributtion::where('student_id', $this->student_id)
                ->where('school_year_id', $this->activeYear->id)
                ->first();

            if ($existingInscription) {
                session()->flash('error', "Cet élève est déjà inscrit pour cette année scolaire");
                return;
            }

            // Créer l'inscription
            $attributtion->student_id = $this->student_id;
            $attributtion->school_year_id = $this->activeYear->id;
            $attributtion->classe_id = $this->classe_id;
            $attributtion->comments = $this->comments;
            $attributtion->save();

            // Réinitialiser les champs
            $this->reset(['nom', 'level_id', 'classe_id', 'matricule', 'student_id', 'comments', 'searchQuery', 'selectedStudent', 'studentInfo', 'selectedClassInfo']);
            
            session()->flash('success', "Inscription réussie avec succès");
            
            return redirect()->route('inscriptions');
        } catch (Exception $e) {
            session()->flash('error', "Une erreur est survenue lors de l'inscription: " . $e->getMessage());
        }
    }

    // Méthode pour rechercher des élèves
    public function search()
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->searchResults = Student::where('nom', 'LIKE', '%' . $this->searchQuery . '%')
                ->orWhere('prenom', 'LIKE', '%' . $this->searchQuery . '%')
                ->orWhere('matricule', 'LIKE', '%' . $this->searchQuery . '%')
                ->limit(10)
                ->get();
            
            $this->showSearchResults = true;
            
            // Si un seul résultat est trouvé, sélectionner automatiquement cet élève
            if (count($this->searchResults) === 1) {
                $this->selectStudent($this->searchResults[0]->id);
            }
        } else {
            $this->searchResults = [];
            $this->showSearchResults = false;
            
            // Si le champ de recherche est vide, réinitialiser l'élève sélectionné
            if (empty($this->searchQuery)) {
                $this->reset(['student_id', 'matricule', 'nom', 'selectedStudent', 'studentInfo']);
            }
        }
    }
    
    // Méthode pour mettre à jour le champ de recherche
    public function updatedSearchQuery()
    {
        $this->search();
    }
    
    // Méthode pour sélectionner un élève depuis les résultats de recherche
    public function selectStudent($studentId)
    {
        $this->student_id = $studentId;
        $student = Student::find($studentId);
        
        if ($student) {
            $this->matricule = $student->matricule;
            $this->nom = $student->nom . ' ' . $student->prenom;
            $this->searchQuery = $student->nom . ' ' . $student->prenom . ' (' . $student->matricule . ')';
            $this->selectedStudent = $student;
            
            // Vérifier si l'élève est déjà inscrit pour cette année scolaire
            $existingInscription = Attributtion::where('student_id', $this->student_id)
                ->where('school_year_id', $this->activeYear->id)
                ->first();
                
            if ($existingInscription) {
                $classe = Classe::find($existingInscription->classe_id);
                $this->studentInfo = [
                    'alreadyRegistered' => true,
                    'classe' => $classe ? $classe->libelle : 'Inconnue',
                ];
            } else {
                $this->studentInfo = [
                    'alreadyRegistered' => false,
                    'gender' => $student->sexe,
                    'birthdate' => $student->naissance,
                    'contact' => $student->contact_parent,
                ];
            }
        }
        
        $this->showSearchResults = false;
    }
    
    // Méthode pour rechercher par matricule (pour maintenir la compatibilité)
    public function updatedMatricule()
    {
        if (!empty($this->matricule)) {
            $currentStudent = Student::where('matricule', 'LIKE', '%' . $this->matricule . "%")->first();
            
            if ($currentStudent) {
                $this->selectStudent($currentStudent->id);
            } else {
                $this->nom = "Ce matricule n'est lié à aucun élève, vérifier votre matricule et réessayez svp !";
                $this->student_id = null;
                $this->selectedStudent = null;
                $this->studentInfo = [];
            }
        } else {
            $this->nom = "";
            $this->student_id = null;
            $this->selectedStudent = null;
            $this->studentInfo = [];
        }
    }
    
    // Méthode pour mettre à jour les informations de classe lorsqu'une classe est sélectionnée
    public function updatedClasseId()
    {
        if ($this->classe_id) {
            $classe = Classe::find($this->classe_id);
            if ($classe) {
                // Compter le nombre d'élèves dans cette classe pour l'année active
                $this->classeEffectif = Attributtion::where('classe_id', $this->classe_id)
                    ->where('school_year_id', $this->activeYear->id)
                    ->count();
                
                $this->selectedClassInfo = [
                    'libelle' => $classe->libelle,
                    'effectif' => $this->classeEffectif,
                ];
            }
        } else {
            $this->selectedClassInfo = null;
            $this->classeEffectif = 0;
        }
    }

    public function render()
    {
        // Charger les niveaux
        $getAllLevels = Level::all();
        
        // Charger les classes du niveau sélectionné
        $classList = [];
        if ($this->level_id) {
            $classList = Classe::where('level_id', $this->level_id)->get();
        }

        return view('livewire.create-inscription', [
            'getAllLevels' => $getAllLevels,
            'classList' => $classList,
        ]);
    }
}
