<div>
    <!-- Messages de notification -->
    @if($message)
    <div class="mb-4 p-4 rounded-md {{ $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        {{ $message }}
    </div>
    @endif

    <!-- Gestion des années scolaires -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Gestion des années scolaires</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année scolaire</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schoolYears as $year)
                    <tr class="{{ $year->active == '1' ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $year->active == '1' ? 'text-blue-800' : 'text-gray-900' }}">
                            {{ $year->school_year }}
                            @if($year->active == '1')
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Active
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($year->active == '1')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Actif
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Inactif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($year->active != '1')
                            <button wire:click="confirmActivation({{ $year->id }})" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                Activer
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if(!$isCreatingNewYear)
        <div class="mt-4">
            <button wire:click="toggleCreateYearForm" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                Créer une nouvelle année scolaire
            </button>
        </div>
        @else
        <div class="mt-4 p-4 bg-gray-50 rounded-md">
            <h4 class="text-md font-medium text-gray-700 mb-2">Créer une nouvelle année scolaire</h4>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" wire:model="newYearName" placeholder="Format: 2024-2025" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('newYearName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <button wire:click="createNewYear" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                    Créer
                </button>
                <button wire:click="toggleCreateYearForm" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                    Annuler
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Progression des élèves -->
    <div class="mt-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Progression des élèves</h2>
            
            <div class="bg-blue-50 p-4 rounded-md mb-6">
                <p class="text-blue-800">
                    <strong>Comment ça fonctionne :</strong> La progression des élèves permet de créer automatiquement des inscriptions pour l'année active en sélectionnant des élèves d'une année précédente.
                </p>
                <ul class="list-disc ml-6 mt-2 text-blue-700">
                    <li>Sélectionnez une année source et une classe</li>
                    <li>Choisissez une classe de destination dans l'année active</li>
                    <li>Sélectionnez les élèves à promouvoir</li>
                    <li>Cliquez sur "Promouvoir les élèves sélectionnés" pour créer les inscriptions</li>
                </ul>
            </div>
            
            @if($progressionCompleted)
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <p>{{ $progressionMessage }}</p>
                </div>
            @endif

            @if($message)
                <div class="mb-4 p-4 rounded-md {{ $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $message }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Sélection de l'année source -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Année source</h3>
                    <div class="mb-4">
                        <label for="selectedYear" class="block text-sm font-medium text-gray-700 mb-1">Sélectionner une année scolaire</label>
                        <select id="selectedYear" wire:model.live="selectedYear" class="w-full rounded-md border-gray-300">
                            <option value="">Sélectionner une année</option>
                            @foreach($availableYears as $year)
                                @if($activeYear && $year->id === $activeYear->id)
                                    <option value="{{ $year->id }}" disabled class="bg-gray-200 text-gray-500">{{ $year->libelle }} (Année active - non sélectionnable)</option>
                                @else
                                    <option value="{{ $year->id }}" class="{{ $selectedYear == $year->id ? 'bg-blue-100 font-semibold' : '' }}">{{ $year->libelle }}{{$year->school_year}}</option>
                                @endif
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Sélectionnez une année précédente pour récupérer les élèves à promouvoir.</p>
                    </div>

                    @if($selectedYear)
                        <div class="mb-4">
                            <label for="selectedClass" class="block text-sm font-medium text-gray-700 mb-1">Sélectionner une classe</label>
                            <select id="selectedClass" wire:model.live="selectedClass" class="w-full rounded-md border-gray-300">
                                <option value="">Sélectionner une classe</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- Sélection de la classe cible -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Destination (Année active: {{ $activeYear->libelle ?? 'Non définie' }})</h3>
                    <div class="mb-4">
                        <label for="targetClass" class="block text-sm font-medium text-gray-700 mb-1">Sélectionner une classe de destination</label>
                        <select id="targetClass" wire:model="targetClass" class="w-full rounded-md border-gray-300">
                            <option value="">Sélectionner une classe</option>
                            @foreach($targetClasses as $class)
                                <option value="{{ $class->id }}">{{ $class->libelle }}</option>
                            @endforeach
                        </select>
                        @if(count($targetClasses) === 0)
                            <p class="text-sm text-red-500 mt-1">Aucune classe disponible. Veuillez d'abord créer des classes.</p>
                        @else
                            <p class="text-sm text-gray-500 mt-1">{{ count($targetClasses) }} classes disponibles pour la destination.</p>
                            <p class="text-sm text-blue-500 mt-1">Toutes les classes sont disponibles comme destination, indépendamment de l'année scolaire.</p>
                            <p class="text-sm text-green-500 mt-1">Classes disponibles: {{ implode(', ', $targetClasses->pluck('libelle')->toArray()) }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Liste des élèves -->
            @if(count($students) > 0)
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-semibold">Liste des élèves</h3>
                        <div>
                            <button wire:click="toggleSelectAll" class="bg-blue-100 text-blue-700 px-3 py-1 rounded-md text-sm">
                                {{ count($selectedStudents) === count($students) ? 'Désélectionner tout' : 'Sélectionner tout' }}
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sélection
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Matricule
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nom
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prénom
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($students as $index => $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" 
                                                wire:model="students.{{ $index }}.selected" 
                                                wire:click="toggleStudent({{ $student['id'] }})"
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student['matricule'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student['nom'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $student['prenom'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button 
                            wire:click="progressStudents" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center"
                            {{ empty($selectedStudents) || !$targetClass ? 'disabled' : '' }}
                            {{ empty($selectedStudents) || !$targetClass ? 'opacity-50 cursor-not-allowed' : '' }}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                            </svg>
                            Promouvoir les élèves sélectionnés ({{ count($selectedStudents) }})
                        </button>
                    </div>
                </div>
            @elseif($selectedClass)
                <div class="mt-6 bg-yellow-50 border border-yellow-400 text-yellow-700 p-4 rounded">
                    <p>Aucun élève trouvé dans cette classe pour l'année scolaire sélectionnée.</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal de confirmation pour l'activation d'une année scolaire -->
    @if($showConfirmation)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmation d'activation</h3>
            <p class="mb-4">Vous êtes sur le point d'activer une nouvelle année scolaire. Cette action désactivera l'année scolaire actuelle.</p>
            <p class="mb-4 font-medium">Toutes les opérations (inscriptions, paiements, etc.) concerneront désormais cette nouvelle année scolaire.</p>
            <p class="mb-4 text-red-600">Êtes-vous sûr de vouloir continuer ?</p>
            <div class="flex justify-end space-x-3">
                <button wire:click="cancelActivation" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                    Annuler
                </button>
                <button wire:click="activateSchoolYear({{ $yearToActivate }})" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
