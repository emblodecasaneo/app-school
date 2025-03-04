<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Support\Collection;
use Livewire\Component;

class StudentDetails extends Component
{
    public $student;
    public $studentId;
    public $attributions = [];
    public $payments = [];
    public $activeYear;
    public $currentAttribution;
    public $attribute;
    public $totalPaid = 0;
    public $totalDue = 0;
    public $paymentStatus = '';
    public $academicHistory = [];
    public $searchMatricule = '';
    public $showSearchResults = false;
    public $searchResults = [];

    public function mount($studentId = null)
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
        
        if ($studentId) {
            $this->studentId = $studentId;
            $this->loadStudentData();
        }
    }

    public function searchStudent()
    {
        if (empty($this->searchMatricule)) {
            return;
        }

        $this->searchResults = Student::where('matricule', 'LIKE', '%' . $this->searchMatricule . '%')
            ->orWhere('nom', 'LIKE', '%' . $this->searchMatricule . '%')
            ->orWhere('prenom', 'LIKE', '%' . $this->searchMatricule . '%')
            ->limit(5)
            ->get();
            
        $this->showSearchResults = true;
    }

    public function selectStudent($id)
    {
        $this->studentId = $id;
        $this->showSearchResults = false;
        $this->searchMatricule = '';
        $this->loadStudentData();
    }

    public function loadStudentData()
    {
        $this->student = Student::find($this->studentId);
        
        if (!$this->student) {
            return;
        }

        // Récupérer toutes les attributions (inscriptions) de l'élève
        $this->attributions = Attributtion::where('student_id', $this->studentId)
            ->with(['classe', 'classe.level', 'schoolyear'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
            
        // Récupérer l'attribution pour l'année active
        $attributions = collect($this->attributions);
        $this->currentAttribution = $attributions->where('school_year_id', $this->activeYear->id)->first();
        //dd($this->currentAttribution);
        
        // Récupérer tous les paiements de l'élève
        $this->payments = Payment::where('student_id', $this->studentId)
            ->with(['classe', 'schoolyear'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
            
        // Calculer le total payé et dû pour l'année active
        $payments = collect($this->payments);
        $this->totalPaid = $payments
            ->where('school_year_id', $this->activeYear->id)
            ->sum('montant');
            
        if ($this->currentAttribution && isset($this->currentAttribution['classe']) && isset($this->currentAttribution['classe']['level'])) {
            $this->totalDue = $this->currentAttribution['classe']['level']['scolarite'];
            
            if ($this->totalPaid >= $this->totalDue) {
                $this->paymentStatus = 'Soldé';
            } else {
                $this->paymentStatus = 'En cours';
            }
        }
        
        // Construire l'historique académique
        $this->academicHistory = [];
        foreach ($this->attributions as $attribution) {
            if (isset($attribution['classe']) && isset($attribution['schoolyear'])) {
                $yearPayments = $payments
                    ->where('school_year_id', $attribution['school_year_id'])
                    ->sum('montant');
                    
                $levelScolarite = $attribution['classe']['level']['scolarite'] ?? 0;
                
                $this->academicHistory[] = [
                    'year' => $attribution['schoolyear']['school_year'],
                    'classe' => $attribution['classe']['libelle'],
                    'level' => $attribution['classe']['level']['libelle'] ?? 'N/A',
                    'scolarite' => $levelScolarite,
                    'paid' => $yearPayments,
                    'status' => $yearPayments >= $levelScolarite ? 'Soldé' : 'Non soldé',
                    'is_active' => $attribution['schoolyear']['active'] == '1'
                ];

            }
        }
    }

    public function render()
    {
        return view('livewire.student-details');
    }
}
