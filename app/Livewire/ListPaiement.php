<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;

class ListPaiement extends Component
{
    public $search = "";
    public $selected_class_id;
    public $dialogAttDeletion = false;
    public $selectName;


    public function render()
    {
        $currentYear = SchoolYear::where('active', '1')->first();
        $allClass = Classe::all();

        if ($this->selected_class_id) {

            if (!empty($this->search)) {
                $select_id = $this->selected_class_id;
                $paiementList = Payment::where('comments', 'like', '%' . $this->search . "%")->
                whereHas('classe', function ($query) use ($select_id) {
                $query->where('libelle', $select_id);
                })->paginate(3);

            } else {
                $select_id = $this->selected_class_id;
                $paiementList = Payment::whereHas('classe', function ($query) use ($select_id) {
                $query->where('libelle', $select_id);
                })->paginate(3);
            }
        } else {

            if (!empty($this->search)){
                $paiementList =Payment::where('montnat', 'like', '%' . $this->search . "%")->paginate(3);

            }else{
                $paiementList = Payment::paginate(3);
            }
        }

        return view('livewire.list-paiement', compact('paiementList', 'allClass'));
    }


    public function delete(Payment $payment){
        $payment->delete();
        return redirect()->route('paiements')->with('success', 'Exclusion réussi , un mail été envoyé aux parents de cet élève');
    }


    public function confirmingAttributtionDeletion(Payment $attributtion){
        $currentStudent = Student::where('id', $attributtion->student_id)->first();
        $this->selectName = $currentStudent->nom . " " . $currentStudent->prenom;
        $this->dialogAttDeletion = true;
    }
}
