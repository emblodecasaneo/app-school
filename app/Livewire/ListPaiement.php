<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class ListPaiement extends Component
{
    use WithPagination;
    
    public $search = "";
    public $selected_class_id;
    public $dialogAttDeletion = false;
    public $selectName;
    public $activeYear;

    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
    }

    public function render()
    {
        $query = Payment::query();
        
        // Filtrer par année scolaire active
        if ($this->activeYear) {
            $query->where('school_year_id', $this->activeYear->id);
        }
        
        // Filtrer par classe si sélectionnée
        if ($this->selected_class_id) {
            $query->whereHas('classe', function ($q) {
                $q->where('libelle', $this->selected_class_id);
            });
        }
        
        // Filtrer par recherche
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('montant', 'like', '%' . $this->search . '%')
                  ->orWhere('comments', 'like', '%' . $this->search . '%')
                  ->orWhereHas('student', function($sq) {
                      $sq->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('matricule', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        $paiementList = $query->with(['student', 'classe'])->paginate(10);
        $allClass = Classe::whereHas('level', function($q) {
            $q->where('school_year_id', $this->activeYear->id ?? 0);
        })->get();

        return view('livewire.list-paiement', [
            'paiementList' => $paiementList, 
            'allClass' => $allClass,
            'activeYear' => $this->activeYear
        ]);
    }

    public function delete(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('paiements')->with('success', 'Paiement supprimé avec succès');
    }

    public function confirmingAttributtionDeletion(Payment $attributtion)
    {
        $currentStudent = Student::where('id', $attributtion->student_id)->first();
        $this->selectName = $currentStudent->nom . " " . $currentStudent->prenom;
        $this->dialogAttDeletion = true;
    }
}
