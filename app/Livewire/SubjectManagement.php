<?php

namespace App\Livewire;

use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;

class SubjectManagement extends Component
{
    use WithPagination;
    
    // Définir explicitement le thème de pagination
    protected $paginationTheme = 'tailwind';
    
    // Propriétés pour la recherche et le filtrage
    public $search = '';
    public $category = '';
    public $showInactive = true;
    
    // Propriétés pour le formulaire
    public $name = '';
    public $description = '';
    public $subjectCategory = '';
    public $isActive = true;
    public $showForm = false;
    
    // Liste des catégories prédéfinies
    public $predefinedCategories = [
        'Sciences' => 'Sciences',
        'Mathématiques' => 'Mathématiques',
        'Langues' => 'Langues',
        'Lettres' => 'Lettres',
        'Sciences Humaines' => 'Sciences Humaines',
        'Arts' => 'Arts',
        'Éducation Physique' => 'Éducation Physique',
        'Technologie' => 'Technologie',
        'Autre' => 'Autre'
    ];
    
    // Propriétés pour l'édition
    public $editingSubjectId = null;
    public $isEditing = false;
    
    // Propriétés pour la suppression
    public $confirmingSubjectDeletion = false;
    public $subjectIdToDelete = null;
    public $subjectHasGrades = false;
    
    // Propriétés pour l'association des matières aux classes
    public $selectedClasse = null;
    public $selectedSubject = null;
    public $coefficient = 1;
    
    public $classes = [];
    public $subjects = [];
    
    public $showCoefficientModal = false;
    
    // Règles de validation
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'subjectCategory' => 'nullable|string|max:100',
        'isActive' => 'boolean',
        'selectedClasse' => 'required',
        'selectedSubject' => 'required',
        'coefficient' => 'required|numeric|min:0.1|max:10',
    ];
    
    // Réinitialiser la pagination lors de la recherche
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingCategory()
    {
        $this->resetPage();
    }
    
    public function updatingShowInactive()
    {
        $this->resetPage();
    }
    
    // Ouvrir le formulaire d'ajout
    public function openAddForm()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showForm = true;
    }
    
    // Ouvrir le formulaire d'édition
    public function edit($subjectId)
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->editingSubjectId = $subjectId;
        
        $subject = Subject::findOrFail($subjectId);
        $this->name = $subject->name;
        $this->description = $subject->description;
        $this->subjectCategory = $subject->category;
        $this->isActive = $subject->is_active;
        
        $this->showForm = true;
    }
    
    // Enregistrer une matière (ajout ou modification)
    public function save()
    {
        if ($this->isEditing) {
            $this->update();
        } else {
            $this->store();
        }
    }
    
    // Ajouter une nouvelle matière
    public function store()
    {
        try {
            // Ajouter un message de débogage
            session()->flash('info', 'Tentative d\'ajout de la matière : ' . $this->name);
            
            // Valider avec une règle unique
            $this->validate([
                'name' => 'required|string|max:255|unique:subjects,name',
                'description' => 'nullable|string',
                'subjectCategory' => 'nullable|string|max:100',
                'isActive' => 'boolean',
            ]);
            
            $subject = Subject::create([
                'name' => $this->name,
                'description' => $this->description,
                'category' => $this->subjectCategory,
                'is_active' => $this->isActive,
            ]);
            
            // Ajouter un message de débogage
            session()->flash('info', 'Matière créée avec ID : ' . ($subject ? $subject->id : 'null'));
            
            $this->resetForm();
            $this->showForm = false; // Masquer le formulaire
            session()->flash('success', 'Matière ajoutée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout de la matière : ' . $e->getMessage());
        }
    }
    
    // Mettre à jour une matière existante
    public function update()
    {
        try {
            // Valider avec une règle unique qui ignore l'ID actuel
            $this->validate([
                'name' => 'required|string|max:255|unique:subjects,name,' . $this->editingSubjectId,
                'description' => 'nullable|string',
                'subjectCategory' => 'nullable|string|max:100',
                'isActive' => 'boolean',
            ]);
            
            $subject = Subject::findOrFail($this->editingSubjectId);
            $subject->update([
                'name' => $this->name,
                'description' => $this->description,
                'category' => $this->subjectCategory,
                'is_active' => $this->isActive,
            ]);
            
            $this->resetForm();
            $this->showForm = false;
            session()->flash('success', 'Matière mise à jour avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour de la matière : ' . $e->getMessage());
        }
    }
    
    // Confirmer la désactivation d'une matière
    public function confirmDelete($subjectId)
    {
        try {
            // Ajouter un message de débogage
            session()->flash('info', 'Méthode confirmDelete appelée. ID de la matière: ' . $subjectId);
            
            // Vérifier si l'ID est valide
            if (!$subjectId || !is_numeric($subjectId)) {
                session()->flash('error', 'ID de matière invalide: ' . $subjectId);
                return;
            }
            
            $subject = Subject::find($subjectId);
            
            // Vérifier si la matière existe
            if (!$subject) {
                session()->flash('error', 'Matière non trouvée avec l\'ID: ' . $subjectId);
                return;
            }
            
            // Ajouter un message de débogage
            session()->flash('info', 'Matière trouvée: ' . $subject->name . ' (ID: ' . $subject->id . ', is_active: ' . ($subject->is_active ? 'true' : 'false') . ')');
            
            // Vérifier si la matière est déjà inactive
            if (!$subject->is_active) {
                session()->flash('info', 'Cette matière est déjà inactive.');
                return;
            }
            
            $this->confirmingSubjectDeletion = true;
            $this->subjectIdToDelete = $subjectId;
            
            // Ajouter un message de débogage
            session()->flash('info', 'Boîte de dialogue de confirmation ouverte. ID de la matière à désactiver: ' . $this->subjectIdToDelete);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la confirmation de désactivation : ' . $e->getMessage());
        }
    }
    
    // Rediriger l'ancienne méthode delete vers deactivateSubject
    public function delete()
    {
        // Ajouter un message de débogage
        session()->flash('info', 'Méthode delete appelée. ID de la matière à désactiver: ' . ($this->subjectIdToDelete ?? 'null'));
        
        if ($this->subjectIdToDelete) {
            $this->deactivateSubject($this->subjectIdToDelete);
        } else {
            session()->flash('error', 'Aucune matière sélectionnée pour la désactivation.');
        }
    }
    
    // Désactiver une matière
    public function deactivateSubject($subjectId)
    {
        try {
            // Ajouter un message de débogage
            session()->flash('info', 'Tentative de désactivation de la matière ID: ' . $subjectId);
            
            // Vérifier si l'ID est valide
            if (!$subjectId || !is_numeric($subjectId)) {
                session()->flash('error', 'ID de matière invalide: ' . $subjectId);
                return;
            }
            
            $subject = Subject::find($subjectId);
            
            // Vérifier si la matière existe
            if (!$subject) {
                session()->flash('error', 'Matière non trouvée avec l\'ID: ' . $subjectId);
                return;
            }
            
            // Ajouter un message de débogage
            session()->flash('info', 'Matière trouvée: ' . $subject->name . ' (ID: ' . $subject->id . ', is_active: ' . ($subject->is_active ? 'true' : 'false') . ')');
            
            // Vérifier si la matière est déjà inactive
            if (!$subject->is_active) {
                session()->flash('info', 'Cette matière est déjà inactive.');
                return;
            }
            
            // Désactiver la matière
            $result = $subject->update(['is_active' => false]);
            
            if ($result) {
                session()->flash('success', 'La matière a été désactivée avec succès.');
                
                // Ajouter un message de débogage
                $updatedSubject = Subject::find($subjectId);
                if ($updatedSubject) {
                    session()->flash('info', 'Nouvel état de la matière: is_active: ' . ($updatedSubject->is_active ? 'true' : 'false'));
                }
            } else {
                session()->flash('error', 'Échec de la désactivation de la matière pour une raison inconnue.');
            }
            
            // Fermer la boîte de dialogue si elle est ouverte
            $this->confirmingSubjectDeletion = false;
            $this->subjectIdToDelete = null;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la désactivation de la matière : ' . $e->getMessage());
        }
    }
    
    // Réinitialiser le formulaire
    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->subjectCategory = '';
        $this->isActive = true;
        $this->editingSubjectId = null;
        $this->isEditing = false;
        $this->resetErrorBag();
    }
    
    // Annuler l'édition
    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }
    
    // Réactiver une matière
    public function reactivate($subjectId)
    {
        try {
            // Ajouter un message de débogage
            session()->flash('info', 'Méthode reactivate appelée. ID de la matière: ' . $subjectId);
            
            // Vérifier si l'ID est valide
            if (!$subjectId || !is_numeric($subjectId)) {
                session()->flash('error', 'ID de matière invalide: ' . $subjectId);
                return;
            }
            
            $subject = Subject::find($subjectId);
            
            // Vérifier si la matière existe
            if (!$subject) {
                session()->flash('error', 'Matière non trouvée avec l\'ID: ' . $subjectId);
                return;
            }
            
            // Ajouter un message de débogage
            session()->flash('info', 'Matière trouvée: ' . $subject->name . ' (ID: ' . $subject->id . ', is_active: ' . ($subject->is_active ? 'true' : 'false') . ')');
            
            // Vérifier si la matière est déjà active
            if ($subject->is_active) {
                session()->flash('info', 'Cette matière est déjà active.');
                return;
            }
            
            // Réactiver la matière
            $result = $subject->update(['is_active' => true]);
            
            if ($result) {
                session()->flash('success', 'La matière a été réactivée avec succès.');
                
                // Ajouter un message de débogage
                $updatedSubject = Subject::find($subjectId);
                if ($updatedSubject) {
                    session()->flash('info', 'Nouvel état de la matière: is_active: ' . ($updatedSubject->is_active ? 'true' : 'false'));
                }
            } else {
                session()->flash('error', 'Échec de la réactivation de la matière pour une raison inconnue.');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la réactivation de la matière : ' . $e->getMessage());
        }
    }
    
    public function mount()
    {
        $this->loadClasses();
        // Ne pas charger les matières ici, elles seront chargées dans la méthode render()
        // $this->loadSubjects();
    }
    
    public function loadClasses()
    {
        $this->classes = \App\Models\Classe::orderBy('libelle')->get();
    }
    
    public function loadSubjects()
    {
        // Cette méthode est utilisée pour charger les matières dans les formulaires, pas pour la pagination
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
            $classe = \App\Models\Classe::find($this->selectedClasse);
            if ($classe) {
                $this->coefficient = $classe->getSubjectCoefficient($this->selectedSubject);
            }
        }
    }
    
    public function openCoefficientModal()
    {
        $this->showCoefficientModal = true;
    }
    
    public function closeCoefficientModal()
    {
        $this->showCoefficientModal = false;
    }
    
    public function saveCoefficient()
    {
        $this->validate();
        
        try {
            $classe = \App\Models\Classe::findOrFail($this->selectedClasse);
            $subject = \App\Models\Subject::findOrFail($this->selectedSubject);
            
            // Vérifier si la matière est déjà associée à la classe
            $exists = $classe->subjects()->where('subject_id', $this->selectedSubject)->exists();
            
            if ($exists) {
                // Mettre à jour le coefficient
                $classe->updateSubjectCoefficient($this->selectedSubject, $this->coefficient);
                session()->flash('success', "Le coefficient de {$subject->name} pour la classe {$classe->libelle} a été mis à jour.");
            } else {
                // Associer la matière à la classe avec le coefficient spécifié
                $classe->addSubject($this->selectedSubject, $this->coefficient);
                session()->flash('success', "{$subject->name} a été associée à la classe {$classe->libelle} avec un coefficient de {$this->coefficient}.");
            }
            
            $this->closeCoefficientModal();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'enregistrement du coefficient : " . $e->getMessage());
        }
    }
    
    public function render()
    {
        try {
            // Récupérer les catégories distinctes pour le filtre
            $categories = Subject::distinct('category')
                ->whereNotNull('category')
                ->pluck('category');
                
            // Construire la requête avec les filtres
            $query = Subject::query();
            
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }
            
            if ($this->category) {
                $query->where('category', $this->category);
            }
            
            if (!$this->showInactive) {
                $query->where('is_active', true);
            }
            
            // Récupérer les résultats paginés
            $subjects = $query->orderBy('name')->paginate(10);
            
            // Vérifier que $subjects est bien un objet de pagination
            if (!method_exists($subjects, 'links')) {
                // Si ce n'est pas un objet de pagination, créer un objet de pagination vide
                $subjects = new \Illuminate\Pagination\LengthAwarePaginator(
                    [], // items
                    0,  // total
                    10, // per page
                    1   // current page
                );
            }
            
            return view('livewire.subject-management', [
                'subjects' => $subjects,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, créer un objet de pagination vide
            $subjects = new \Illuminate\Pagination\LengthAwarePaginator(
                [], // items
                0,  // total
                10, // per page
                1   // current page
            );
            
            return view('livewire.subject-management', [
                'subjects' => $subjects,
                'categories' => collect(),
            ]);
        }
    }
}
