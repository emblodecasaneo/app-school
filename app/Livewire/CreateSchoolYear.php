<?php

namespace App\Livewire;

use App\Models\SchoolYear;
use Carbon\Carbon;
use Exception;
use Livewire\Component;

class CreateSchoolYear extends Component
{
    public $libelle;
    public $curent_year;

    public function store(SchoolYear $schoolYear){
       $this->validate([
        "libelle"=>'string|required',
       ]);

      try{
            $this->curent_year =  Carbon::now()->format('Y');
            $check = SchoolYear::where('curent_year', $this->curent_year)->get();

            if($check->count() <= 0){
                $schoolYear->school_year = $this->libelle;
                $schoolYear->curent_year = $this->curent_year;
                $schoolYear->save();
                if($schoolYear){
                $this->libelle ='';
                }
                return redirect()->route('settings')->with('success', 'Année scolaire ajoutée !');
            }else{
                return redirect()->back()->with('error', "Cette anné scolaire existe déjà, allez dans la liste des années pour vérifier et/ou modifier le libellé");
            }
      }catch(Exception $e){
        return ($e);
      }

    }

    public function render()
    {
        return view('livewire.create-school-year');
    }
}
