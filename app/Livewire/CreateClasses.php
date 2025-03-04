<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use Exception;
use Livewire\Component;

class CreateClasses extends Component
{




    public $libelle;
    public $level_id;

    public function store(Classe $classe)
    {
        $this->validate([
            "libelle" => 'required|unique:levels,libelle',
        ]);

        try {

            $classe->libelle = $this->libelle;
            $classe->level_id = $this->level_id;
            $classeExist = Classe::where('libelle', $this->libelle)->where('level_id', $this->level_id);
            if ($classeExist->count() > 0) {
                return redirect()->back()->with('error', "Désolé , cette classe existe déjà pour ce niveau au compte de l'année en cours !");
            } else {
                $classe->save();
                return redirect()->route('classes')->with('success', 'Classe créée avec success !');
            }
            if ($classe) {
                $this->libelle = '';
            }
        } catch (Exception $e) {

            return ($e);
        }
    }
    public function render()
    {
        //recupere l'année encours
        $activeYear = SchoolYear::where('active', '1')->first();

        //charger tous les niveaux sans filtrer par année scolaire
        $getAllLevels = Level::all();
        return view('livewire.create-classes', compact('getAllLevels'));
    }
}
