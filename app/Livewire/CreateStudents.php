<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Student;
use Exception;
use Illuminate\Support\Collection;
use Livewire\Component;

class CreateStudents extends Component
{
    public $nom;
    public $prenom;
    public $naissance;
    public $contact_parent;
    public $matricule;
    public $sexe;
    
    // Propriétés pour l'inscription
    public $level_id;
    public $classe_id;
    public $classes;
    public $levels = [];
    public $activeYear;
    
    public function mount()
    {
        $this->classes = collect([]);
        // Récupérer l'année scolaire active
        $this->activeYear = SchoolYear::where('active', '1')->first();
        
        // Charger les niveaux disponibles pour l'année active
        if ($this->activeYear) {
            $this->levels = Level::all();
        }
    }
    
    public function updatedLevelId($value)
    {
        // Mettre à jour les classes disponibles lorsque le niveau change
        if ($value) {
            // Charger les classes du niveau sélectionné
            $this->classes = Classe::where('level_id', $value)->get();
            
            // Si aucune classe n'est disponible pour ce niveau, afficher un message
            if ($this->classes->count() === 0) {
                session()->flash('warning', 'Aucune classe n\'est disponible pour ce niveau. Veuillez d\'abord créer une classe pour ce niveau.');
            }
            
            // Réinitialiser la classe sélectionnée
            $this->classe_id = null;
        } else {
            $this->classes = collect([]);
            $this->classe_id = null;
        }
    }

    public function store(){
       $this->validate([
        "nom" => 'required',
        "contact_parent" => "required",
        "matricule" => "unique:students,matricule",
        "naissance" => "required",
        "sexe" => "required",
        "level_id" => "required",
        "classe_id" => "required"
       ]);

      try{
                $student = new Student();
                $activeYear = SchoolYear::where('active', '1')->first();
                $listStudent = Student::all();
                $randomNumber = random_int(0, 999);
                $formattedNumber = str_pad($randomNumber, 3, '0', STR_PAD_LEFT);
                $initialName = strtoupper(substr($this->nom, 0, 2));

                $student->nom = $this->nom;
                $student->prenom = $this->prenom;
                $student->sexe = $this->sexe;
                $student->matricule = $listStudent->count() ."". $formattedNumber . "" . $initialName. "" . $activeYear->curent_year ."". $this->matricule;
                $student->naissance = $this->naissance;
                $student->contact_parent = $this->contact_parent;
                $student->save();
                
                // Créer l'attribution (inscription) pour l'élève
                if ($student) {
                    $attribution = new Attributtion();
                    $attribution->student_id = $student->id;
                    $attribution->classe_id = $this->classe_id;
                    $attribution->school_year_id = $activeYear->id;
                    $attribution->save();
                    
                    // Réinitialiser les champs
                    $this->reset(['nom', 'prenom', 'contact_parent', 'naissance', 'matricule', 'sexe', 'level_id', 'classe_id']);
                    $this->classes = [];
                    
                    // Émettre un événement pour rafraîchir le tableau de bord
                    $this->dispatch('studentCreated');
                    $this->dispatch('refresh-dashboard');
                }
                
                return redirect()->route('students')->with('success', "L'élève a été ajouté(e) avec succès et inscrit(e) dans la classe sélectionnée");
      } catch(Exception $e) {
        return redirect()->route('students.create_student')->with('error', "Erreur lors de l'ajout de l'élève: " . $e->getMessage());
      }
    }

    public function render()
    {
        return view('livewire.create-students', [
            'levels' => $this->levels,
            'classes' => $this->classes,
            'activeYear' => $this->activeYear
        ]);
    }
}
