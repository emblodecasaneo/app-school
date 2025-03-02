<?php

namespace App\Livewire;

use App\Models\Level;
use App\Models\SchoolYear;
use Exception;
use Livewire\Component;

class CreateLevel extends Component
{

    public $libelle;
    public $code;
    public $scolarite;
    public $activeYear;

    public function store(Level $level){
       $this->validate([
        "libelle"=>'required',
        "code"=>"required|unique:levels,code",
        "scolarite"=>"required|numeric|min:0"
       ]);

      try{

                $this->activeYear = SchoolYear::where('active', '1')->first();
                $level->libelle = $this->libelle;
                $level->school_year_id = $this->activeYear->id;
                $level->code = $this->code . "" .$this->activeYear->curent_year;
                $level->scolarite = $this->scolarite;
                $level->save();
                if($level){
                $this->libelle ='';
                $this->code = "";
                $this->scolarite = "";
                }
                return redirect()->route('niveaux')->with('success', 'Niveau ajouté avec success !');
      }catch(Exception $e){

        return ($e);
      }

    }


    public function render()
    {
        return view('livewire.create-level');
    }
}
