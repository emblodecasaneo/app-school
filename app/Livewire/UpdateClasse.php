<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use Exception;
use Livewire\Component;

class UpdateClasse extends Component
{



    public $classe;
    public $libelle;
    public $level_id;

    //Etape ou le composant est monté

    public function mount(){
        $this->libelle = $this->classe->libelle;
        $this->level_id = $this->classe->level_id;

    }


    public function store(){
        $this->validate([
         "libelle"=>'required',
        ]);

        $classe = Classe::findOrfail($this->classe->id);

       try{

                $classeExist = Classe::where('libelle', $this->libelle)->where('level_id', $this->level_id);
                if($classeExist->count() <= 0){
                    $classe->libelle = $this->libelle;
                    $classe->level_id = $this->level_id;
                    $classe->save();
                    return redirect()->route('classes')->with('success', 'La classe a été modifié avec success !');
                } else {
                    return redirect()->route('classes')->with('error', 'La classe existe déjà !');
                }
                 if($classe){
                 $this->libelle ='';
                 }
       }catch(Exception $e){
        dd($e);
         return ($e);
       }

     }

    public function render()
    {
        $currentYear = SchoolYear::where('active', '1')->first();
        $allLevelCurrent = Level::where('school_year_id', $currentYear->id)->get();
        return view('livewire.update-classe', compact('allLevelCurrent'));
    }
}
