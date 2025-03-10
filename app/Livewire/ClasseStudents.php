<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class ClasseStudents extends Component
{
    use WithPagination;

    public $classeId;
    public $classe;
    public $activeSchoolYear;
    
    #[Url(history: true)]
    public $search = '';
    
    public $showAddModal = false;
    public $selectedStudent = null;
    public $availableStudents = [];

    public function mount($classeId)
    {
        $this->classeId = $classeId;
        $this->classe = Classe::findOrFail($classeId);
        $this->activeSchoolYear = SchoolYear::where('active', '1')->first();
        
        if (!$this->activeSchoolYear) {
            $this->activeSchoolYear = SchoolYear::latest()->first();
        }
    }

    public function render()
    {
        $students = $this->getClassStudents();
        
        return view('livewire.classe-students', [
            'students' => $students,
            'classe' => $this->classe,
            'activeSchoolYear' => $this->activeSchoolYear
        ]);
    }

    #[On('refreshStudents')]
    public function refreshStudents()
    {
        // Cette méthode sera appelée lorsque l'événement refreshStudents est dispatché
    }

    public function getClassStudents()
    {
        return Attributtion::where('classe_id', $this->classeId)
            ->where('school_year_id', $this->activeSchoolYear->id)
            ->with('student')
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('matricule', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);
    }

    public function openAddModal()
    {
        // Récupérer les élèves qui ne sont pas déjà inscrits dans cette classe pour l'année active
        $this->availableStudents = Student::whereDoesntHave('attributions', function ($query) {
                $query->where('school_year_id', $this->activeSchoolYear->id)
                      ->where('classe_id', $this->classeId);
            })
            ->get();
        
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->selectedStudent = null;
    }

    public function addStudent()
    {
        $this->validate([
            'selectedStudent' => 'required|exists:students,id',
        ], [
            'selectedStudent.required' => 'Veuillez sélectionner un élève',
            'selectedStudent.exists' => 'L\'élève sélectionné n\'existe pas',
        ]);

        // Vérifier si l'élève est déjà inscrit dans une autre classe pour l'année active
        $existingAttribution = Attributtion::where('student_id', $this->selectedStudent)
            ->where('school_year_id', $this->activeSchoolYear->id)
            ->first();

        if ($existingAttribution) {
            session()->flash('error', 'Cet élève est déjà inscrit dans une autre classe pour cette année scolaire.');
            return;
        }

        // Créer l'attribution
        Attributtion::create([
            'student_id' => $this->selectedStudent,
            'classe_id' => $this->classeId,
            'school_year_id' => $this->activeSchoolYear->id
        ]);

        session()->flash('success', 'Élève inscrit avec succès dans la classe.');
        $this->closeAddModal();
        $this->dispatch('refreshStudents');
    }

    public function removeStudent($attributionId)
    {
        $attribution = Attributtion::findOrFail($attributionId);
        $attribution->delete();
        
        session()->flash('success', 'Élève retiré de la classe avec succès.');
        $this->dispatch('refreshStudents');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
