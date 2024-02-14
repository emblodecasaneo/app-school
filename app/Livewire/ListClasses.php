<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use Livewire\Component;

use function Pest\Laravel\get;

class ListClasses extends Component
{
    public $search = "";
    public function render()
    {
        $activeYear = SchoolYear::where('active', '1')->first();
        if(!empty($this->search)){
           $classList = Classe::where('libelle', 'like' , '%' .$this->search. "%")->
           whereHas('level', function($query) use ($activeYear){
            $query->where('school_year_id', $activeYear->id);
        })->paginate(3);

           }else{
            $classList = Classe::whereHas('level', function($query) use ($activeYear){
                $query->where('school_year_id', $activeYear->id);
            })->paginate(3);
           }
        return view('livewire.list-classes', compact('classList'));
    }

    public function delete(Classe $classe){
        $classe->delete();
        return redirect()->route('classes')->with('success', 'classes supprimé avec success');
    }

}
