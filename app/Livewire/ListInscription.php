<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Component;

class ListInscription extends Component
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
                $inscriptionList = Attributtion::where('comments', 'like', '%' . $this->search . "%")->
                whereHas('classe', function ($query) use ($select_id) {
                $query->where('libelle', $select_id);
                })->paginate(3);

            } else {
                $select_id = $this->selected_class_id;
                $inscriptionList = Attributtion::whereHas('classe', function ($query) use ($select_id) {
                $query->where('libelle', $select_id);
                })->paginate(3);
            }
        } else {

            if (!empty($this->search)){
                $inscriptionList = Attributtion::where('comments', 'like', '%' . $this->search . "%")->paginate(3);

            }else{
                $inscriptionList = Attributtion::paginate(3);
            }
        }

        return view('livewire.list-inscription', compact('inscriptionList', 'allClass'));
    }


    public function delete(Attributtion $attributtion){
        $attributtion->delete();
        return redirect()->route('inscriptions')->with('success', 'Exclusion réussi , un mail été envoyé aux parents de cet élève');
    }


    public function confirmingAttributtionDeletion(Attributtion $attributtion){
        $currentStudent = Student::where('id', $attributtion->student_id)->first();
        $this->selectName = $currentStudent->nom . " " . $currentStudent->prenom;
        $this->dialogAttDeletion = true;
    }
}
