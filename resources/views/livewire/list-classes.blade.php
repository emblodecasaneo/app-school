<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
    <!-- En-tête avec titre et barre de recherche -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-poppins-semibold text-gray-800">Gestion des Classes</h2>
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
                <input type="text" wire:model.live="search" placeholder="Rechercher une classe..." 
                    class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            
            <x-button href="{{ route('classes.create_level') }}" icon="add" color="indigo">
                Ajouter une classe
            </x-button>
        </div>
    </div>
    
    <!-- Messages flash -->
    @if (Session::get('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-icons name="check" class="text-green-400" size="sm" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-poppins">{{ Session::get('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if (Session::get('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-icons name="error" class="text-red-400" size="sm" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-poppins">{{ Session::get('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Tableau des classes -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col" class="px-6 py-3 text-left text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Effectif</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Date de création</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-poppins-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($classList as $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-poppins text-gray-500">{{ $item->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-poppins-medium text-gray-900">{{ $item->libelle }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-poppins-medium rounded-full bg-indigo-100 text-indigo-800">
                                {{ $item->level->libelle }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-poppins-medium rounded-full {{ $item->studentCount > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <x-icons name="student" class="mr-1" size="xs" /> {{ $item->studentCount ?? 0 }} élèves
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $item->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <x-action-menu id="class-{{ $item->id }}">
                                <x-action-menu-item href="{{ route('classes.update_classe', $item) }}" icon="edit" color="indigo">
                                    Modifier
                                </x-action-menu-item>
                                <x-action-menu-item wire="delete({{ $item->id }})" icon="delete" color="red" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe?')">
                                    Supprimer
                                </x-action-menu-item>
                            </x-action-menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <x-icons name="folder-empty" class="text-gray-300 mb-4" size="xl" />
                                <p class="text-gray-500 font-poppins-medium">Aucune classe trouvée</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $classList->links() }}
    </div>
</div>
