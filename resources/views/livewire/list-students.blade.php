<div class="mt-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
        {{-- Titre et Bouton créer --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 md:mb-0">
                <i class="fas fa-user-graduate text-blue-500 mr-2"></i> Liste des élèves
            </h2>
            
            <div class="flex space-x-2">
                <a href="{{ route('students.details') }}"
                    class="bg-indigo-500 hover:bg-indigo-600 transition-colors rounded-md p-2 text-sm text-white flex items-center shadow-sm">
                    <i class="fas fa-eye mr-1"></i> Consulter un élève
                </a>
                <a href="{{ route('students.create_student') }}"
                    class="bg-blue-500 hover:bg-blue-600 transition-colors rounded-md p-2 text-sm text-white flex items-center shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Ajouter un(e) élève
                </a>
            </div>
        </div>
        
        {{-- Filtres --}}
        <div class="bg-gray-50 p-4 rounded-lg mb-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" name="search" placeholder="Nom, prénom ou matricule"
                            class="pl-10 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors" 
                            wire:model.live="search" />
                    </div>
                </div>
                
                <div>
                    <label for="genre" class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-venus-mars text-gray-400"></i>
                        </div>
                        <select id="genre" name="genre" 
                            class="pl-10 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors"
                            wire:model.live="genre">
                            <option value="FM">Tous les sexes</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="filterInscription" class="block text-sm font-medium text-gray-700 mb-1">Statut d'inscription</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clipboard-check text-gray-400"></i>
                        </div>
                        <select id="filterInscription" name="filterInscription"
                            class="pl-10 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors"
                            wire:model.live="filterInscription">
                            <option value="all">Tous les élèves</option>
                            <option value="inscribed">Inscrits</option>
                            <option value="not_inscribed">Non inscrits</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="filterClass" class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-graduation-cap text-gray-400"></i>
                        </div>
                        <select id="filterClass" name="filterClass"
                            class="pl-10 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors"
                            wire:model.live="filterClass">
                            <option value="all">Toutes les classes</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="filterSolvability" class="block text-sm font-medium text-gray-700 mb-1">Solvabilité</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-money-bill-wave text-gray-400"></i>
                        </div>
                        <select id="filterSolvability" name="filterSolvability"
                            class="pl-10 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors"
                            wire:model.live="filterSolvability">
                            <option value="all">Tous les statuts</option>
                            <option value="solvable">Solvables</option>
                            <option value="not_solvable">Insolvables</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        @if($activeYear)
        <div class="mt-2 bg-blue-50 p-3 rounded-md text-blue-800 text-sm flex items-center mb-4 shadow-sm">
            <i class="fas fa-calendar-alt mr-2"></i>
            Année scolaire active : <strong class="ml-1">{{ $activeYear->school_year }}</strong>
        </div>
        @endif
        
        <div class="flex flex-col mt-2">
            {{-- Message qui appaitra après opération --}}
            @if (Session::get('success'))
                <div class="flex items-center p-3 bg-green-100 text-green-700 text-md rounded-md shadow-sm mt-2 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ Session::get('success') }}
                </div>
            @endif
            @if (Session::get('error'))
                <div class="flex items-center p-3 bg-red-100 text-red-700 rounded-md shadow-sm mt-2 mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ Session::get('error') }}
                </div>
            @endif

            {{-- Messages Livewire --}}
            @if (isset($message))
                <div class="flex items-center p-3 {{ $messageType == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-md shadow-sm mt-2 mb-2">
                    <i class="fas {{ $messageType == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-2"></i>
                    {{ $message }}
                </div>
            @endif
            
            {{-- Style du tableau --}}
            <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                <div class="inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-left">
                            <thead class="border-b bg-gray-50">
                                <tr class="text-blue-600">
                                    <th class="text-sm font-semibold px-4 py-3">ID</th>
                                    <th class="text-sm font-semibold px-4 py-3">Matricule</th>
                                    <th class="text-sm font-semibold px-4 py-3">Nom</th>
                                    <th class="text-sm font-semibold px-4 py-3">Prénom</th>
                                    <th class="text-sm font-semibold px-4 text-center py-3">Sexe</th>
                                    <th class="text-sm font-semibold px-4 text-center py-3">Parent</th>
                                    <th class="text-sm font-semibold px-4 py-3">Classe</th>
                                    <th class="text-sm font-semibold px-4 py-3">Date d'inscription</th>
                                    <th class="text-sm font-semibold px-4 py-3">Solvabilité</th>
                                    <th class="text-sm font-semibold px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $item)
                                    <tr class="border-b hover:bg-gray-50 transition-colors {{ !$item->is_solvable && $item->is_inscribed ? 'bg-red-50' : '' }}">
                                        <td class="text-sm text-gray-900 px-4 py-3">#{{ $item->id }}</td>
                                        <td class="text-sm text-gray-900 px-4 py-3">
                                            <span class="font-medium">{{ $item->matricule }}</span>
                                        </td>
                                        <td class="text-sm text-gray-900 px-4 py-3 font-medium">
                                            {{ $item->nom }}
                                        </td>
                                        <td class="text-sm text-gray-900 px-4 py-3">
                                            {{ $item->prenom }}
                                        </td>
                                        <td class="text-sm text-center text-gray-900 px-4 py-3">
                                            @if ($item->sexe === 'F')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-pink-100 text-pink-800 text-xs">
                                                    <i class="fas fa-female mr-1"></i> Féminin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs">
                                                    <i class="fas fa-male mr-1"></i> Masculin
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-sm text-center text-gray-900 px-4 py-3">
                                            <a href="tel:{{ $item->contact_parent }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <i class="fas fa-phone-alt text-gray-500 mr-1"></i> {{ $item->contact_parent }}
                                            </a>
                                        </td>
                                        <td class="text-sm text-gray-900 px-4 py-3">
                                            @if ($item->is_inscribed)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs">
                                                    <i class="fas fa-graduation-cap mr-1"></i> {{ $item->current_class }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-xs">
                                                    <i class="fas fa-times-circle mr-1"></i> Non inscrit
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-sm text-gray-900 px-4 py-3">
                                            @if ($item->is_inscribed && isset($item->inscription_date))
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs">
                                                    <i class="fas fa-calendar-check mr-1"></i> {{ $item->inscription_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-xs">
                                                    <i class="fas fa-calendar-times mr-1"></i> Non disponible
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-sm text-gray-900 px-4 py-3">
                                            @if($activeYear && $item->is_inscribed)
                                                @if($item->is_solvable)
                                                    <div class="flex items-center">
                                                        <span class="relative flex h-3 w-3 mr-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                                        </span>
                                                        <span class="px-2 py-1 inline-flex items-center text-xs leading-5 rounded-full bg-green-100 text-green-800">
                                                            <i class="fas fa-money-bill-wave mr-1"></i> Solvable
                                                        </span>
                                                    </div>
                                                    <div class="mt-1 text-xs {{ $item->remaining_amount > 0 ? 'text-orange-500 font-medium' : 'text-gray-500' }}">
                                                    </div>
                                                @else
                                                    <div class="flex items-center">
                                                        <span class="relative flex h-3 w-3 mr-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                                        </span>
                                                        <span class="px-2 py-1 inline-flex items-center text-xs leading-5 rounded-full bg-red-100 text-red-800">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i> Insolvable
                                                        </span>
                                                    </div>
                                                    <div class="mt-1 text-xs text-center {{ $item->remaining_amount > 0 ? 'text-orange-500 font-medium' : 'text-gray-500' }}">
                                                        <i class="fas fa-money-bill-alt mr-1"></i> Reste: {{ number_format($item->remaining_amount, 0, ',', ' ') }} FCFA
                                                    </div>
                                                @endif
                                            @else
                                            <div class="flex items-center">
                                                <span class="relative flex h-3 w-3 mr-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-900 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-900"></span>
                                                </span>
                                                <span class="px-2 py-1 inline-flex items-center text-xs leading-5 rounded-full bg-gray-400 text-white">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> Non inscrit
                                                </span>
                                            </div>
                                            @endif
                                        </td>
                                        <td class="text-sm text-gray-900 text-center px-4 py-3">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" @click.away="open = false" 
                                                    class="text-blue-700 hover:text-blue-900 transition-colors p-1 flex items-center justify-center"
                                                    title="Actions">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                                    </svg>
                                                </button>
                                                
                                                <div x-show="open" 
                                                    x-transition:enter="transition ease-out duration-100" 
                                                    x-transition:enter-start="transform opacity-0 scale-95" 
                                                    x-transition:enter-end="transform opacity-100 scale-100" 
                                                    x-transition:leave="transition ease-in duration-75" 
                                                    x-transition:leave-start="transform opacity-100 scale-100" 
                                                    x-transition:leave-end="transform opacity-0 scale-95" 
                                                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200"
                                                    style="display: none;">
                                                    <div class="py-1">
                                                        <a href="{{ route('students.details', $item->id) }}"
                                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                            <i class="fas fa-eye w-5 mr-2 text-indigo-600 text-lg"></i> Consulter
                                                        </a>
                                                        <a href="{{ route('students.update_student', $item) }}"
                                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                            <i class="fas fa-edit w-5 mr-2 text-blue-600 text-lg"></i> Modifier
                                                        </a>
                                                        <button wire:click="delete({{ $item->id }})" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève?')"
                                                            class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                                            <i class="fas fa-trash-alt w-5 mr-2 text-red-600 text-lg"></i> Supprimer
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <div class="flex flex-col items-center justify-center p-6">
                                                <i class="fas fa-search text-gray-400 text-5xl mb-3"></i>
                                                <p class="text-gray-500 text-lg">Aucun élève trouvé !</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-4 px-4"> {{ $students->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Make sure to include Font Awesome for icons --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Custom styles for inputs */
    input, select {
        transition: all 0.2s ease-in-out;
    }
    input:focus, select:focus {
        transform: translateY(-1px);
    }
    
    /* Hover effect for table rows */
    tbody tr:hover {
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Animated pulse for solvability indicator */
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
        }
        
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(52, 211, 153, 0);
        }
        
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(52, 211, 153, 0);
        }
    }
</style>
@endpush
