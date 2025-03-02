<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class ListStudents extends Component
{
    use WithPagination;
    
    public $search;
    public $genre = 'FM';
    public $activeYear;
    
    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
    }
    
    public function render()
    {
        $query = Student::query();
        
        // Filtrer par année scolaire active
        if ($this->activeYear) {
            $query->whereHas('attributions', function($q) {
                $q->where('school_year_id', $this->activeYear->id);
            });
        }
        
        // Filtrer par recherche
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                  ->orWhere('prenom', 'like', '%' . $this->search . '%')
                  ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }
        
        // Filtrer par genre
        if ($this->genre !== 'FM') {
            $query->where('sexe', $this->genre);
        }
        
        $studentList = $query->paginate(10);
        
        return view('livewire.list-students', [
            'studentList' => $studentList,
            'activeYear' => $this->activeYear
        ]);
    }

    public function delete(Student $student)
    {
        $student->delete();
        return redirect()->route('students')->with('success', "L'élève ". $student->nom . " Matricule ". $student->matricule . " a été supprimé de votre fichier et ne fait plus partie de vos effectifs");
    }
}
