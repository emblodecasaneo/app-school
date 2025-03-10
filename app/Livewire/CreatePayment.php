<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;
use Livewire\Attributes\Url;

class CreatePayment extends Component
{
    public $student_id;
    public $classe_id;
    public $montant;
    public $reste;
    public $solvable = '0';
    public $students = [];
    public $classes = [];
    public $activeYear;
    public $searchStudent = '';
    public $selectedStudent = null;
    public $newPaymentId = null;
    public $showSuccessModal = false;

    public function mount()
    {
        $this->activeYear = SchoolYear::where('active', '1')->first();
        if (!$this->activeYear) {
            $this->activeYear = SchoolYear::latest()->first();
        }

        $this->loadClasses();
    }

    public function render()
    {
        $this->searchStudents();
        
        return view('livewire.create-payment');
    }

    public function loadClasses()
    {
        $this->classes = Classe::all();
    }

    public function searchStudents()
    {
        if (strlen($this->searchStudent) >= 2) {
            $this->students = Student::where('nom', 'like', '%' . $this->searchStudent . '%')
                ->orWhere('prenom', 'like', '%' . $this->searchStudent . '%')
                ->orWhere('matricule', 'like', '%' . $this->searchStudent . '%')
                ->get();
        } else {
            $this->students = [];
        }
    }

    public function selectStudent($id)
    {
        $this->selectedStudent = Student::find($id);
        $this->student_id = $id;
        $this->searchStudent = $this->selectedStudent->nom . ' ' . $this->selectedStudent->prenom;
        $this->students = [];
    }

    public function save()
    {
        $this->validate([
            'student_id' => 'required|exists:students,id',
            'classe_id' => 'required|exists:classes,id',
            'montant' => 'required|numeric|min:1',
            'reste' => 'required|numeric|min:0',
            'solvable' => 'required|in:0,1',
        ], [
            'student_id.required' => 'Veuillez sélectionner un élève',
            'classe_id.required' => 'Veuillez sélectionner une classe',
            'montant.required' => 'Le montant est requis',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être supérieur à 0',
            'reste.required' => 'Le reste à payer est requis',
            'reste.numeric' => 'Le reste à payer doit être un nombre',
            'reste.min' => 'Le reste à payer doit être supérieur ou égal à 0',
        ]);

        $payment = Payment::create([
            'student_id' => $this->student_id,
            'classe_id' => $this->classe_id,
            'school_year_id' => $this->activeYear->id,
            'montant' => $this->montant,
            'reste' => $this->reste,
            'solvable' => $this->solvable,
        ]);

        $this->newPaymentId = $payment->id;
        $this->showSuccessModal = true;
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->reset(['student_id', 'classe_id', 'montant', 'reste', 'solvable', 'selectedStudent', 'searchStudent']);
    }

    public function printReceipt()
    {
        return redirect()->route('paiements.receipt', ['payment' => $this->newPaymentId]);
    }

    public function downloadPdf()
    {
        return redirect()->route('paiements.receipt-pdf', ['payment' => $this->newPaymentId]);
    }

    public function calculateReste()
    {
        if ($this->montant > 0) {
            // Exemple: si le montant total est de 100 000 FCFA
            $totalFees = 100000;
            $this->reste = max(0, $totalFees - $this->montant);
            $this->solvable = ($this->reste == 0) ? '1' : '0';
        }
    }
}
