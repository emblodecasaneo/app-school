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
        if(!empty($this->search)){
           $classList = Classe::where('libelle', 'like' , '%' .$this->search. "%")->paginate(10);
        } else {
            // Récupérer toutes les classes sans filtrer par année scolaire
            $classList = Classe::orderBy('libelle')->paginate(10);
        }
        return view('livewire.list-classes', compact('classList'));
    }

    public function delete(Classe $classe){
        // Vérifier si la classe a des attributions ou des paiements associés
        if ($classe->attributions()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer cette classe car des élèves y sont inscrits. Veuillez d\'abord supprimer ou réaffecter ces inscriptions.');
            return;
        }
        
        // Aucune attribution associée, on peut supprimer la classe
        $classe->delete();
        session()->flash('success', 'Classe supprimée avec succès');
    }

}
