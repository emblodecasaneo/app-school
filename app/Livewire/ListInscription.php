<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class ListInscription extends Component
{
    use WithPagination;
    
    public $search = "";
    public $selected_class_id;
    public $dialogAttDeletion = false;
    public $selectName;
    public $activeYear;
    public $message;
    public $messageType;

    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
    }

    public function render()
    {
        $allClass = Classe::all();
        $query = Attributtion::query();
        
        // Filtrer par année active
        if ($this->activeYear) {
            $query->where('school_year_id', $this->activeYear->id);
        }
        
        // Filtrer par classe sélectionnée
        if ($this->selected_class_id) {
            $query->whereHas('classe', function ($q) {
                $q->where('libelle', $this->selected_class_id);
            });
        }
        
        // Filtrer par recherche
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('comments', 'like', '%' . $this->search . '%')
                  ->orWhereHas('student', function($sq) {
                      $sq->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('matricule', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Charger les relations
        $query->with(['student', 'classe.level']);
        
        // Paginer les résultats
        $inscriptionList = $query->paginate(10);
        
        return view('livewire.list-inscription', [
            'inscriptionList' => $inscriptionList,
            'allClass' => $allClass,
            'activeYear' => $this->activeYear
        ]);
    }

    public function delete(Attributtion $attributtion){
        try {
            $attributtion->delete();
            $this->message = "Exclusion réussie, un mail a été envoyé aux parents de cet élève";
            $this->messageType = "success";
            
            // Réinitialiser le dialogue
            $this->dialogAttDeletion = false;
        } catch (\Exception $e) {
            $this->message = "Erreur lors de la suppression: " . $e->getMessage();
            $this->messageType = "error";
        }
    }

    public function confirmingAttributtionDeletion(Attributtion $attributtion){
        $currentStudent = Student::where('id', $attributtion->student_id)->first();
        $this->selectName = $currentStudent->nom . " " . $currentStudent->prenom;
        $this->dialogAttDeletion = true;
    }
}
