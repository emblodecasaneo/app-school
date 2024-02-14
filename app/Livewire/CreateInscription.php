<?php

namespace App\Livewire;

use App\Models\Attributtion;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Student;
use Exception;
use Livewire\Component;

class CreateInscription extends Component
{
    public $level_id;
    public $matricule;
    public $classe_id;
    public $school_year_id;
    public $student_id;
    public $nom;
    public $comments;
    public $activeYear;

    public function store(Attributtion $attributtion){


        $query = Attributtion::where('student_id', $this->student_id)->where('school_year_id', $this->activeYear->id)->get();

        $this->validate([
         "matricule"=>'required',
         "level_id"=>"required",
         "classe_id"=>"unique:students,matricule",
        ]);


       try{
                 $attributtion->student_id = $this->student_id;
                 $attributtion->school_year_id = $this->activeYear->id;
                 $attributtion->classe_id = $this->classe_id;
                 $attributtion->comments = $this->comments;
                 if($query->count() <= 0){
                    $attributtion->save();
                    return redirect()->route('inscriptions')->with('success', "Inscription réussi avec success , veillez vérifier si toutes les information sont correctes");
                }{
                    return redirect()->route('inscriptions')->with('error', "Nous sommes désolé mais cet élève est déjà inscrit inscrit pour cette année scolaire");

                }
                 if($attributtion){
                 $this->nom ='';
                 $this->level_id = "";
                 $this->classe_id ='';
                 $this->matricule = "";
                 }
       }catch(Exception $e){
         dd($e);
         return ($e);
       }

     }





    public function render()
    {

        $this->activeYear = SchoolYear::where('active', '1')->first();

        //charger les niveuaux qui appartiennent à l'année en cour
        $getAllLevels = Level::where('school_year_id', $this->activeYear->id)->get();

        if(isset($this->matricule)){
            $currentStudent = Student::where('matricule', 'LIKE' ,  '%' .$this->matricule. "%")->first();
           if($currentStudent){
                $this->nom = $currentStudent->nom. " " .$currentStudent->prenom;
                $this->student_id = $currentStudent->id;
           }else{
            $this->nom = "Ce matricule n'est lié à aucun élève , vérifier votre matricule et rééssayez svp !";
           };
        }else{
            $this->nom ="";
        }

        if(isset($this->level_id)){
            $activeLevelId = $this->level_id;
            $classList = Classe::where('level_id', $activeLevelId)->get();
        }else{
            $classList = [];
        }

        return view('livewire.create-inscription', compact('getAllLevels', 'classList'));
    }
}
