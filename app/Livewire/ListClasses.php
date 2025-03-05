<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Attributtion;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\get;

class ListClasses extends Component
{
    use WithPagination;
    
    public $search = "";
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
           $classList = Classe::where('libelle', 'like' , '%' .$this->search. "%")->paginate(10);
        } else {
            // Récupérer toutes les classes sans filtrer par année scolaire
            $classList = Classe::orderBy('libelle')->paginate(10);
        }
        
        // Pour chaque classe, récupérer le nombre d'élèves inscrits pour l'année active
        if ($this->activeSchoolYear) {
            foreach ($classList as $classe) {
                // Utiliser la relation attributions pour compter les élèves
                $count = $classe->attributions()
                    ->where('school_year_id', $this->activeSchoolYear->id)
                    ->count();
                
                // Stocker le résultat
                $classe->studentCount = $count;
                
                // Log pour débogage
                Log::info("Classe {$classe->libelle} (ID: {$classe->id}): {$count} élèves pour l'année {$this->activeSchoolYear->school_year} (ID: {$this->activeSchoolYear->id})");
            }
        }
        
        return view('livewire.list-classes', [
            'classList' => $classList,
            'activeSchoolYear' => $this->activeSchoolYear
        ]);
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
