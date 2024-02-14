<?php

namespace App\Livewire;

use App\Models\SchoolYear;
use App\Models\Student;
use Exception;
use Livewire\Component;

class UpdateStudents extends Component
{

    public $student;
    public $nom;
    public $prenom;
    public $naissance;
    public $contact_parent;
    public $matricule;
    public $sexe;

    //Etape ou le composant est monté

    public function mount(){
        $this->nom = $this->student->nom;
        $this->prenom = $this->student->prenom;
        $this->naissance = $this->student->naissance;
        $this->contact_parent = $this->student->contact_parent;
        $this->matricule = $this->student->matricule;
        $this->sexe = $this->student->sexe;

    }


    public function store(){
        $this->validate([
            "nom"=>'required',
            "contact_parent"=>"required",
            "naissance"=>"required"
           ]);

        $student = Student::findOrfail($this->student->id);

        try{
            $activeYear = SchoolYear::where('active', '1')->first();
            $listStudent = Student::all();
            $randomNumber = random_int(0, 999);
            $formattedNumber = str_pad($randomNumber, 3, '0', STR_PAD_LEFT);
            $initialName =strtoupper(substr($this->nom, 0, 2));

            $student->nom = $this->nom;
            $student->prenom = $this->prenom;
            $student->sexe = $this->sexe;
            $student->naissance = $this->naissance;
            $student->contact_parent = $this->contact_parent;
            $student->save();
            if($student){
            $this->nom ='';
            $this->prenom = "";
            $this->contact_parent ='';
            $this->naissance = "";
            $this->matricule = "";
            }
            return redirect()->route('students')->with('success', "L'élève à été ajouté(e) avec success, veillez vérifier si toutes les information sont correctes");
  }catch(Exception $e){
    dd($e);
    return ($e);
  }
}

    public function render()
    {
        return view('livewire.update-students');
    }
}
