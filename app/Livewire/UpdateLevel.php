<?php

namespace App\Livewire;

use App\Models\Level;
use App\Models\SchoolYear;
use Exception;
use Livewire\Component;

class UpdateLevel extends Component
{

    public $level;
    public $libelle;
    public $code;
    public $activeYear;

    //Etape ou le composant est monté

    public function mount(){
        $this->code = $this->level->code;
        $this->libelle = $this->level->libelle;

    }


    public function store(){
        $this->validate([
         "libelle"=>'required',
         "code"=>"required"
        ]);

        $level = Level::findOrfail($this->level->id);

       try{

                 $level->libelle = $this->libelle;
                 $level->code = $this->code;
                 $level->scolarite = 0;
                 $level->save();
                 if($level){
                 $this->libelle ='';
                 $this->code = "";
                 }
                 return redirect()->route('niveaux')->with('success', 'Niveau modifié avec success !');
       }catch(Exception $e){
        dd($e);
         return ($e);
       }

     }

    public function render()
    {
        return view('livewire.update-level');
    }
}
