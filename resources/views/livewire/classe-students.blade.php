<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
    <!-- En-tête avec titre et barre de recherche -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-poppins-semibold text-gray-800">Élèves de la classe {{ $classe->libelle }}</h2>
            @if($activeSchoolYear)
                <p class="text-sm text-gray-600 mt-1">
                    <x-icons name="calendar" class="text-indigo-500 mr-1" size="sm" /> Année scolaire active: <span class="font-poppins-medium">{{ $activeSchoolYear->name }}</span>
                </p>
            @endif
        </div>
        
        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icons name="search" class="text-gray-400" size="sm" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un élève..." 
                    class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            
            <x-button wire:click="openAddModal" icon="add" color="indigo">
                Ajouter un élève
            </x-button>
        </div>
    </div>
    
    <!-- Messages flash -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-icons name="check" class="text-green-400" size="sm" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-poppins">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-icons name="error" class="text-red-400" size="sm" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-poppins">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Tableau des élèves -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col" class="px-6 py-3 text-left text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Genre</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Date d'inscription</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($students as $attribution)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-poppins text-gray-500">{{ $attribution->student->matricule }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-poppins-medium text-gray-900">{{ $attribution->student->nom }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-poppins-medium text-gray-900">{{ $attribution->student->prenom }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-poppins-medium rounded-full {{ $attribution->student->gender == 'M' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                {{ $attribution->student->gender == 'M' ? 'Masculin' : 'Féminin' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $attribution->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <x-action-menu id="student-{{ $attribution->id }}">
                                <x-action-menu-item href="{{ route('students.details', $attribution->student->id) }}" icon="eye" color="indigo">
                                    Voir détails
                                </x-action-menu-item>
                                <x-action-menu-item wire:click="removeStudent({{ $attribution->id }})" icon="delete" color="red" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir retirer cet élève de la classe?')">
                                    Retirer de la classe
                                </x-action-menu-item>
                            </x-action-menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <x-icons name="folder-empty" class="text-gray-300 mb-4" size="xl" />
                                <p class="text-gray-500 font-poppins-medium">Aucun élève inscrit dans cette classe</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $students->links() }}
    </div>

    <!-- Modal pour ajouter un élève -->
    @if($showAddModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-poppins-medium text-gray-900">Ajouter un élève à la classe {{ $classe->libelle }}</h3>
            </div>
            <div class="p-6">
                @if(count($availableStudents) > 0)
                    <div class="mb-4">
                        <label for="selectedStudent" class="block text-sm font-poppins-medium text-gray-700 mb-2">Sélectionner un élève</label>
                        <select wire:model="selectedStudent" id="selectedStudent" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">-- Choisir un élève --</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->matricule }} - {{ $student->last_name }} {{ $student->first_name }}</option>
                            @endforeach
                        </select>
                        @error('selectedStudent') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <x-button wire:click="closeAddModal" color="gray">
                            Annuler
                        </x-button>
                        <x-button wire:click="addStudent" color="indigo">
                            Ajouter
                        </x-button>
                    </div>
                @else
                    <div class="text-center py-4">
                        <x-icons name="info" class="text-indigo-400 mx-auto mb-4" size="lg" />
                        <p class="text-gray-600 font-poppins-medium">Tous les élèves sont déjà inscrits dans des classes pour cette année scolaire.</p>
                        <div class="mt-6">
                            <x-button wire:click="closeAddModal" color="gray">
                                Fermer
                            </x-button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
