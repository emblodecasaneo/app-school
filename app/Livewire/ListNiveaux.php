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
            orWhere('code', 'like' , '%' .$this->search. "%")->paginate(10);
        } else {
            // Récupérer tous les niveaux sans filtrer par année scolaire
            $levelList = Level::orderBy('libelle')->paginate(10);
        }
        return view('livewire.list-niveaux', compact('levelList'));
    }

    public function delete(Level $level){
        // Vérifier si le niveau a des classes associées
        if ($level->classes()->count() > 0) {
            // Le niveau a des classes associées, ne pas supprimer
            session()->flash('error', 'Impossible de supprimer ce niveau car il est associé à une ou plusieurs classes. Veuillez d\'abord supprimer ou réaffecter ces classes.');
            return;
        }
        
        // Aucune classe associée, on peut supprimer le niveau
        $level->delete();
        session()->flash('success', 'Niveau supprimé avec succès');
    }
}
