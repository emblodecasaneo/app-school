<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use Exception;
use Livewire\Component;

class CreatePaiement extends Component
{   
    public $level_id;
    public $matricule;
    public $currentLevelAmount;
    public $classe_id;
    public $school_year_id;
    public $student_id;
    public $nom;
    public $montant;
    public $activeYear;
    public $montantDejaPayé = 0;
    public $montantRestant = 0;
    
    // Nouvelles propriétés pour la recherche avancée
    public $searchQuery = '';
    public $searchResults = [];
    public $showSearchResults = false;

    public function render()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();

        if(isset($this->student_id)){
            $this->loadStudentInfo();
        }

        return view('livewire.create-paiement');
    }
    
    // Nouvelle méthode pour rechercher des élèves
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
            
            // Si le champ de recherche est vide, réinitialiser tous les champs
            if (empty($this->searchQuery)) {
                $this->reset(['student_id', 'matricule', 'nom', 'level_id', 'classe_id', 'montantDejaPayé', 'montantRestant', 'currentLevelAmount']);
            }
        }
    }
    
    // Méthode pour mettre à jour le champ de recherche
    public function updatedSearchQuery()
    {
        $this->search();
    }
    
    // Nouvelle méthode pour sélectionner un élève depuis les résultats de recherche
    public function selectStudent($studentId)
    {
        $this->student_id = $studentId;
        $student = Student::find($studentId);
        if ($student) {
            $this->matricule = $student->matricule;
            $this->searchQuery = $student->nom . ' ' . $student->prenom . ' (' . $student->matricule . ')';
        }
        
        $this->showSearchResults = false;
        $this->loadStudentInfo();
    }
    
    // Méthode pour charger les informations d'un élève
    public function loadStudentInfo()
    {
        $currentStudent = Student::find($this->student_id);

        if($currentStudent){
            $this->nom = $currentStudent->nom . " " . $currentStudent->prenom;
            
            // Vérifier si l'élève est inscrit pour l'année scolaire active
            $currentIns = Attributtion::where('student_id', $this->student_id)
                ->where('school_year_id', $this->activeYear->id)
                ->first();
            
            if($currentIns) {
                // Récupérer directement la classe à partir de l'attribution
                $currentClass = Classe::find($currentIns->classe_id);
                
                if($currentClass) {
                    // Récupérer le niveau de la classe
                    $currentLevel = Level::find($currentClass->level_id);
                    
                    if($currentLevel) {
                        $this->currentLevelAmount = $currentLevel->scolarite;
                        $this->classe_id = $currentClass->id;
                        $this->level_id = $currentLevel->id;
                        
                        // Calculer le montant déjà payé par l'élève pour cette année scolaire
                        $this->montantDejaPayé = Payment::where('student_id', $this->student_id)
                            ->where('school_year_id', $this->activeYear->id)
                            ->sum('montant');
                            
                        // Calculer le montant restant à payer
                        $this->montantRestant = max(0, $this->currentLevelAmount - $this->montantDejaPayé);
                        
                        // Si l'élève a déjà tout payé
                        if ($this->montantRestant <= 0) {
                            $this->nom = $currentStudent->nom . " " . $currentStudent->prenom . " (Scolarité déjà entièrement payée)";
                        }
                    } else {
                        $this->nom = "Aucun niveau trouvé pour cet élève dans l'année scolaire actuelle.";
                    }
                } else {
                    $this->nom = "Aucune classe trouvée pour cet élève dans l'année scolaire actuelle.";
                }
            } else {
                $this->nom = "Cet élève n'est pas inscrit pour l'année scolaire actuelle.";
            }
        } else {
            $this->nom = "Cet élève n'existe pas, vérifier votre sélection et réessayez svp !";
        }
    }
    
    // Méthode pour rechercher par matricule (pour maintenir la compatibilité)
    public function updatedMatricule()
    {
        if (!empty($this->matricule)) {
            $currentStudent = Student::where('matricule', 'LIKE', '%' . $this->matricule . "%")->first();
            
            if ($currentStudent) {
                $this->student_id = $currentStudent->id;
                $this->searchQuery = $currentStudent->nom . ' ' . $currentStudent->prenom . ' (' . $currentStudent->matricule . ')';
                $this->loadStudentInfo();
            } else {
                $this->nom = "Ce matricule n'est lié à aucun élève, vérifier votre matricule et réessayez svp !";
                $this->student_id = null;
                $this->reset(['level_id', 'classe_id', 'montantDejaPayé', 'montantRestant', 'currentLevelAmount']);
            }
        } else {
            $this->nom = "";
            $this->student_id = null;
            $this->reset(['level_id', 'classe_id', 'montantDejaPayé', 'montantRestant', 'currentLevelAmount']);
        }
    }

    public function store(Payment $payment){
        $this->validate([
            "montant" => "integer|required|min:1|max:{$this->montantRestant}",
            "student_id" => 'required',
            "classe_id" => 'required',
        ], [
            "student_id.required" => "Aucun élève sélectionné. Veuillez rechercher et sélectionner un élève valide.",
            "classe_id.required" => "Aucune classe trouvée pour cet élève.",
            "montant.required" => "Le montant est requis.",
            "montant.integer" => "Le montant doit être un nombre entier.",
            "montant.min" => "Le montant doit être supérieur à 0.",
            "montant.max" => "Le montant ne peut pas dépasser le reste à payer ({$this->montantRestant} FCFA).",
        ]);

        try {
            // Vérifier si l'élève est inscrit pour l'année active
            $inscription = Attributtion::where('student_id', $this->student_id)
                ->where('school_year_id', $this->activeYear->id)
                ->first();
                
            if (!$inscription) {
                session()->flash('error', "Cet élève n'est pas inscrit pour l'année scolaire actuelle.");
                return;
            }
            
            // Vérifier si le montant ne dépasse pas le reste à payer
            if ($this->montant > $this->montantRestant) {
                session()->flash('error', "Le montant du paiement ({$this->montant} FCFA) ne peut pas dépasser le reste à payer ({$this->montantRestant} FCFA).");
                return;
            }
            
            // Créer le paiement
            $payment->student_id = $this->student_id;
            $payment->school_year_id = $this->activeYear->id;
            $payment->classe_id = $this->classe_id;
            $payment->montant = $this->montant;
            
            // Calculer le nouveau reste à payer après ce paiement
            $nouveauMontantRestant = $this->montantRestant - $this->montant;
            $payment->reste = $nouveauMontantRestant;
            
            // Déterminer si l'élève est solvable
            if ($nouveauMontantRestant <= 0) {
                $payment->solvable = '1';
            } else {
                $payment->solvable = '0';
            }
            
            $payment->save();
            
            // Réinitialiser les champs après l'enregistrement
            $this->reset(['nom', 'level_id', 'classe_id', 'matricule', 'montant', 'montantDejaPayé', 'montantRestant', 'searchQuery', 'student_id']);
            
            // Émettre un événement pour rafraîchir le tableau de bord
            $this->dispatch('refresh-dashboard');
            
            return redirect()->route('paiements')->with('success', "Paiement enregistré avec succès pour l'élève.");
        } catch (Exception $e) {
            return redirect()->route('paiements.create_paiement')->with('error', "Erreur lors de l'enregistrement du paiement: " . $e->getMessage());
        }
    }
}
