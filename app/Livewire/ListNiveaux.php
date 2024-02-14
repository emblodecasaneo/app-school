<?php

namespace App\Livewire;

use App\Models\Level;
use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\WithPagination;

class ListNiveaux extends Component
{
    use WithPagination;
    public $search = '';


    public function render()
    {

        if(!empty($this->search)){
            $levelList = Level::where('libelle', 'like' , '%' .$this->search. "%")->
            orWhere('code', 'like' , '%' .$this->search. "%")->paginate(4);
           }else{
               $activeYear = SchoolYear::where('active', '1')->first();
               $levelList = Level::where('school_year_id', $activeYear->id)->paginate(4);
           }
        return view('livewire.list-niveaux', compact('levelList'));
    }

    public function delete(Level $level){
        $level->delete();
        return redirect()->route('niveaux')->with('success', 'Niveau supprimé avec success');
    }
}
