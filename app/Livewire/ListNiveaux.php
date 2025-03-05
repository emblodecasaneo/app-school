<?php

namespace App\Livewire;

use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Attributtion;
use App\Models\Classe;
use Livewire\Component;
use Livewire\WithPagination;

class ListNiveaux extends Component
{
    use WithPagination;
    public $search = '';
    public $activeSchoolYear;

    public function mount()
    {
        // Récupérer l'année scolaire active
        $this->activeSchoolYear = SchoolYear::where('active', '1')->first();
        
        // Vérification de débogage
        if (!$this->activeSchoolYear) {
            // Si aucune année active n'est trouvée, on prend la plus récente
            $this->activeSchoolYear = SchoolYear::orderBy('id', 'desc')->first();
        }
    }

    public function render()
    {
        if(!empty($this->search)){
            $levelList = Level::where('libelle', 'like' , '%' .$this->search. "%")->
            orWhere('code', 'like' , '%' .$this->search. "%")->paginate(10);
        } else {
            // Récupérer tous les niveaux sans filtrer par année scolaire
            $levelList = Level::orderBy('libelle')->paginate(10);
        }
        
        // Pour chaque niveau, récupérer le nombre d'élèves inscrits pour l'année active
        if ($this->activeSchoolYear) {
            foreach ($levelList as $level) {
                // Récupérer les classes de ce niveau
                $classes = $level->classes;
                $classeIds = $classes->pluck('id')->toArray();
                
                // Compter les élèves inscrits dans ces classes pour l'année active
                $level->studentCount = Attributtion::whereIn('classe_id', $classeIds)
                    ->where('school_year_id', $this->activeSchoolYear->id)
                    ->count();
                
                // Compter le nombre de classes dans ce niveau
                $level->classCount = count($classeIds);
            }
        }
        
        return view('livewire.list-niveaux', [
            'levelList' => $levelList,
            'activeSchoolYear' => $this->activeSchoolYear
        ]);
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
