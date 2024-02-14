<?php

namespace App\Livewire;

use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\WithPagination;

class Settings extends Component
{
    use WithPagination;
    public $libelle = '';



    public function render()
    {
        if(!empty($this->libelle)){
         $schoolYearList = SchoolYear::where('school_year', 'like' , '%' .$this->libelle. "%")->paginate(4);
        }else{
            $schoolYearList = SchoolYear::paginate(4);
        }
        return view('livewire.settings', compact('schoolYearList'));
    }

    public function toggleStatus(SchoolYear $schoolYear){
        //Mettre toutes les ligines de la table active à 0
        $query = SchoolYear::where('active', '1')->update(["active"=>'0']);

        //mettre à jour le statut de l'enregistrement grace à son id
        $schoolYear->active = '1';
        $schoolYear->save();
        $this->render();
    }

}
