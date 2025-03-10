<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\User;
use App\Models\Classe;
use App\Models\Level;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class GlobalSearch extends Component
{
    public $search = '';
    public $results = [];
    public $showResults = false;

    public function mount()
    {
        Log::info('GlobalSearch mounted');
        $this->resetResults();
    }

    public function updating($name, $value)
    {
        Log::info('Updating property', ['name' => $name, 'value' => $value]);
    }

    public function updated($name, $value)
    {
        Log::info('Property updated', ['name' => $name, 'value' => $value]);
        if ($name === 'search') {
            $this->doSearch();
        }
    }

    public function doSearch()
    {
        Log::info('Performing search', ['search' => $this->search]);

        if (strlen($this->search) < 2) {
            $this->resetResults();
            return;
        }

        try {
            $searchTerm = '%' . $this->search . '%';

            // Recherche des élèves
            $this->results['students'] = Student::where('nom', 'like', $searchTerm)
                ->orWhere('prenom', 'like', $searchTerm)
                ->orWhere('matricule', 'like', $searchTerm)
                ->take(5)
                ->get();

            // Recherche des classes
            $this->results['classes'] = Classe::where('libelle', 'like', $searchTerm)
                ->take(5)
                ->get();

            // Recherche des niveaux
            $this->results['levels'] = Level::where('libelle', 'like', $searchTerm)
                ->take(5)
                ->get();

            // Recherche des utilisateurs
            $this->results['users'] = User::where('name', 'like', $searchTerm)
                ->orWhere('email', 'like', $searchTerm)
                ->take(5)
                ->get();

            $this->showResults = true;

            Log::info('Search results', [
                'nb_students' => $this->results['students']->count(),
                'nb_classes' => $this->results['classes']->count(),
                'nb_levels' => $this->results['levels']->count(),
                'nb_users' => $this->results['users']->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Search error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->resetResults();
        }
    }

    private function resetResults()
    {
        $this->results = [
            'students' => collect(),
            'classes' => collect(),
            'levels' => collect(),
            'users' => collect(),
        ];
        $this->showResults = false;
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
