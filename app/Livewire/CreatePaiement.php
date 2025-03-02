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
{   public $level_id;
    public $matricule;
    public $currentLevelAmount;
    public $classe_id;
    public $school_year_id;
    public $student_id;
    public $nom;
    public $montant ;
    public $activeYear;


    public function render()
    {

        $this->activeYear = SchoolYear::where('active', '1')->first();

        //charger les niveuaux qui appartiennent à l'année en cour

        if(isset($this->matricule)){
            $currentStudent = Student::where('matricule', 'LIKE' ,  '%' .$this->matricule. "%")->first();

           if($currentStudent){
                $this->nom = $currentStudent->nom. " " .$currentStudent->prenom;
                $this->student_id = $currentStudent->id;
                $currentIns = Attributtion::where('student_id', $this->student_id)->where('school_year_id', $this->activeYear->id)->first();
                
                if($currentIns) {
                    $currentClass = Classe::whereHas('level', function($query) use ($currentIns){
                        $query->where('school_year_id', $currentIns->school_year_id);
                    })->first();
                    
                    if($currentClass) {
                        $currentLevel = Level::where('id', $currentClass->level_id)->first();
                        if($currentLevel) {
                            $this->currentLevelAmount = $currentLevel->scolarite;
                            $this->classe_id = $currentClass->id;
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
                $this->nom = "Ce matricule n'est lié à aucun élève, vérifier votre matricule et réessayez svp !";
           }
        } else {
            $this->nom ="";
        }

        return view('livewire.create-paiement');
    }


    public function store(Payment $payment){

        $this->validate([
            "montant"=>'integer|required|between:1,500000',
           ]);

       try{
                 $payment->student_id = $this->student_id;
                 $payment->school_year_id = $this->activeYear->id;
                 $payment->classe_id = $this->classe_id;
                 $payment->montant = $this->montant;
                 $payment->reste = $this->currentLevelAmount - $this->montant;
                 if($payment->reste < 0){
                 $payment->reste = 0;
                 }
                 if($payment->reste <= 0){
                 $payment->solvable = '1';
                 }else{
                 $payment->solvable = '0';
                 }
                $payment->save();
                return redirect()->route('paiements')->with('success', "paiement  réussi avec success , veillez vérifier si toutes les information sont correctes");
                 if($payment){
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
}
