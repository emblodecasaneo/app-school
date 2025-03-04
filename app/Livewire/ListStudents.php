<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ListStudents extends Component
{
    use WithPagination;
    
    public $search = '';
    public $genre = 'FM';
    public $activeYear;
    public $message;
    public $messageType;
    public $filterInscription = 'all'; // 'all', 'inscribed', 'not_inscribed'
    public $filterClass = 'all'; // Filter by class
    public $filterSolvability = 'all'; // 'all', 'solvable', 'not_solvable'
    public $classes = []; // To store available classes
    
    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
        
        // Load classes for the active year
        if ($this->activeYear) {
            $this->classes = Classe::whereHas('attributions', function($query) {
                $query->where('school_year_id', $this->activeYear->id);
            })->get();
        }
    }
    
    public function render()
    {
        $query = Student::query();
        
        // Filtrer par statut d'inscription
        if ($this->activeYear) {
            if ($this->filterInscription === 'inscribed') {
                $query->whereHas('attributtions', function($q) {
                    $q->where('school_year_id', $this->activeYear->id);
                });
            } elseif ($this->filterInscription === 'not_inscribed') {
                $query->whereDoesntHave('attributtions', function($q) {
                    $q->where('school_year_id', $this->activeYear->id);
                });
            }
            
            // Filtrer par classe
            if ($this->filterClass !== 'all') {
                $query->whereHas('attributtions', function($q) {
                    $q->where('school_year_id', $this->activeYear->id)
                      ->where('classe_id', $this->filterClass);
                });
            }
            
            // Filtrer par solvabilité
            if ($this->filterSolvability !== 'all') {
                if ($this->filterSolvability === 'solvable') {
                    // Élèves solvables : ceux qui n'ont pas de paiements marqués comme non solvables
                    $query->whereDoesntHave('payments', function($q) {
                        $q->where('school_year_id', $this->activeYear->id)
                          ->where('solvable', '0');
                    })->whereHas('attributtions', function($q) {
                        $q->where('school_year_id', $this->activeYear->id);
                    });
                } else {
                    // Élèves insolvables : ceux qui ont au moins un paiement marqué comme non solvable
                    $query->whereHas('payments', function($q) {
                        $q->where('school_year_id', $this->activeYear->id)
                          ->where('solvable', '0');
                    });
                }
            }
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
        
        // Récupérer la liste des élèves
        $studentList = $query->paginate(8);
        
        // Pour chaque élève, déterminer s'il est inscrit pour l'année active et son statut de solvabilité
        foreach ($studentList as $student) {
            $student->is_inscribed = false;
            $student->current_class = null;
            $student->is_solvable = true; // Par défaut, on considère l'élève comme solvable
            
            if ($this->activeYear) {
                $attribution = Attributtion::where('student_id', $student->id)
                    ->where('school_year_id', $this->activeYear->id)
                    ->with(['classe', 'classe.level'])
                    ->first();
                
                if ($attribution) {
                    $student->is_inscribed = true;
                    
                    // Vérifier si la classe existe et a un nom
                    if ($attribution->classe) {
                        $student->current_class = $attribution->classe->libelle;
                    } else {
                        $student->current_class = 'Classe inconnue';
                        // Log pour débogage
                        Log::warning("Élève inscrit sans classe: ID {$student->id}, Nom: {$student->nom} {$student->prenom}");
                    }
                    
                    // Vérifier la solvabilité (si l'élève a des paiements en retard)
                    // Vérifier s'il existe des paiements marqués comme non solvables pour cet élève
                    $hasPendingPayments = Payment::where('student_id', $student->id)
                        ->where('school_year_id', $this->activeYear->id)
                        ->where('solvable', '0')
                        ->exists();
                    
                    // Un élève est considéré comme insolvable s'il a au moins un paiement en retard
                    $student->is_solvable = !$hasPendingPayments;
                    
                    // Calculer le montant total payé
                    $totalPayments = Payment::where('student_id', $student->id)
                        ->where('school_year_id', $this->activeYear->id)
                        ->sum('montant');
                    
                    $student->total_paid = $totalPayments;
                    
                    // Récupérer le montant total de la scolarité pour cet élève
                    if ($attribution->classe && $attribution->classe->level) {
                        $totalFees = $attribution->classe->level->scolarite;
                        // Calculer le montant restant à payer
                        $student->remaining_amount = max(0, $totalFees - $totalPayments);
                    } else {
                        $student->remaining_amount = 0;
                    }
                }
            }
        }
        
        return view('livewire.list-students', [
            'students' => $studentList,
            'activeYear' => $this->activeYear,
            'classes' => $this->classes,
            'filterSolvability' => $this->filterSolvability
        ]);
    }

    public function delete(Student $student)
    {
        try {
            $student->delete();
            $this->message = "Élève supprimé avec succès";
            $this->messageType = "success";
            
            // Émettre un événement pour rafraîchir le tableau de bord
            $this->dispatch('studentDeleted');
            $this->dispatch('refresh-dashboard');
        } catch (\Exception $e) {
            $this->message = "Erreur lors de la suppression: " . $e->getMessage();
            $this->messageType = "error";
        }
    }
}
