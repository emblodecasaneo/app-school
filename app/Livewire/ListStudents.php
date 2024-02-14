<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;

class ListStudents extends Component
{
    public $search;
    public function render()
    {

        if(!empty($this->search)){
            $studentList = Student::where('nom', 'like' , '%' .$this->search. "%")->
            orWhere('prenom', 'like' , '%' .$this->search. "%")->paginate(3);
           }else{
            $studentList = Student::paginate(3);
        }
        return view('livewire.list-students', compact($studentList?'studentList':""));
    }

    public function delete(Student $student){
        $student->delete();
        return redirect()->route('students')->with('success', "L'élève ". $student->nom . " Matricule ". $student->matricule . "à été supprimer de votre fichier et ne fait plus parti de vos éffectifs");
    }
}
