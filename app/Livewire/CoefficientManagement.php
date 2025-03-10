<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;

class CoefficientManagement extends Component
{
    use WithPagination;
    
    // Propriétés pour la recherche et le filtrage
    public $search = '';
    public $selectedClasse = null;
    public $selectedSubject = null;
    public $coefficient = 1;
    
    // Propriétés pour les listes
    public $classes = [];
    public $subjects = [];
    
    // Propriétés pour la pagination
    protected $paginationTheme = 'tailwind';
    
    // Propriétés pour le formulaire
    public $showForm = false;
    public $isEditing = false;
    
    // Règles de validation
    protected function rules()
    {
        return [
            'selectedClasse' => 'required',
            'selectedSubject' => 'required',
            'coefficient' => 'required|numeric|min:0.1|max:10',
        ];
    }
    
    public function mount()
    {
        $this->loadClasses();
        $this->loadSubjects();
    }
    
    public function loadClasses()
    {
        $this->classes = Classe::orderBy('libelle')->get();
    }
    
    public function loadSubjects()
    {
        $this->subjects = Subject::where('is_active', true)->orderBy('name')->get();
    }
    
    public function updatedSelectedClasse()
    {
        $this->resetValidation();
        $this->loadSubjectCoefficient();
    }
    
    public function updatedSelectedSubject()
    {
        $this->resetValidation();
        $this->loadSubjectCoefficient();
    }
    
    public function loadSubjectCoefficient()
    {
        if ($this->selectedClasse && $this->selectedSubject) {
            try {
                // Vérifier que les IDs sont bien des entiers
                $classeId = (int) $this->selectedClasse;
                $subjectId = (int) $this->selectedSubject;
                
                $classe = Classe::find($classeId);
                if ($classe) {
                    $this->coefficient = $classe->getSubjectCoefficient($subjectId);
                }
            } catch (\Exception $e) {
                session()->flash('error', "Erreur lors du chargement du coefficient : " . $e->getMessage());
            }
        }
    }
    
    public function saveCoefficient()
    {
        // Ajouter des messages de débogage
        session()->flash('info', "Tentative d'enregistrement du coefficient. Classe: {$this->selectedClasse}, Matière: {$this->selectedSubject}, Coefficient: {$this->coefficient}");
        
        $this->validate();
        
        try {
            // Vérifier que les IDs sont bien des entiers
            $classeId = (int) $this->selectedClasse;
            $subjectId = (int) $this->selectedSubject;
            
            $classe = Classe::findOrFail($classeId);
            $subject = Subject::findOrFail($subjectId);
            
            // Vérifier si la matière est déjà associée à la classe
            $exists = $classe->subjects()->where('subject_id', $subjectId)->exists();
            
            if ($exists) {
                // Mettre à jour le coefficient
                $classe->updateSubjectCoefficient($subjectId, $this->coefficient);
                session()->flash('success', "Le coefficient de {$subject->name} pour la classe {$classe->libelle} a été mis à jour.");
            } else {
                // Associer la matière à la classe avec le coefficient spécifié
                $classe->addSubject($subjectId, $this->coefficient);
                session()->flash('success', "{$subject->name} a été associée à la classe {$classe->libelle} avec un coefficient de {$this->coefficient}.");
            }
            
            // Recharger les données
            $this->loadSubjectCoefficient();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'enregistrement du coefficient : " . $e->getMessage());
        }
    }
    
    public function deleteSubjectFromClass($subjectId)
    {
        if (!$this->selectedClasse) {
            session()->flash('error', "Veuillez sélectionner une classe.");
            return;
        }
        
        try {
            // Vérifier que les IDs sont bien des entiers
            $classeId = (int) $this->selectedClasse;
            $subjectId = (int) $subjectId;
            
            $classe = Classe::findOrFail($classeId);
            $subject = Subject::findOrFail($subjectId);
            
            // Détacher la matière de la classe
            $classe->subjects()->detach($subjectId);
            
            session()->flash('success', "La matière {$subject->name} a été retirée de la classe {$classe->libelle}.");
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la suppression de la matière : " . $e->getMessage());
        }
    }
    
    public function getClassSubjectsProperty()
    {
        if (!$this->selectedClasse) {
            return collect([]);
        }
        
        $classe = Classe::find($this->selectedClasse);
        if (!$classe) {
            return collect([]);
        }
        
        return $classe->subjects()->orderBy('name')->get();
    }
    
    public function render()
    {
        return view('livewire.coefficient-management', [
            'classSubjects' => $this->classSubjects,
        ]);
    }
}
