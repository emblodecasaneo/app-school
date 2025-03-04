<div>
    <div class="p-6">
        <!-- En-tête avec filtres -->
        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="w-full md:w-1/4">
                <select wire:model.live="selectedClasse" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Sélectionner une classe</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->libelle}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-1/4">
                <select wire:model.live="selectedPeriod" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Tous les trimestres</option>
                    @foreach($periods as $period)
                        <option value="{{ $period }}">{{ $period }}</option>
                    @endforeach
                </select>
            </div>
            
            @if(!$isBulkMode)
            <div class="w-full md:w-1/3">
                <input type="text" wire:model.live="search" placeholder="Rechercher un élève..." class="w-full px-4 py-2 border rounded-lg">
            </div>
            @endif
            
            <div class="flex space-x-2">
                <button wire:click="toggleBulkMode" class="px-4 py-2 {{ $isBulkMode ? 'bg-indigo-500' : 'bg-gray-500' }} text-white rounded-lg hover:bg-indigo-600">
                    {{ $isBulkMode ? 'Mode individuel' : 'Mode classe' }}
                </button>
                
                @if($selectedClasse && $isBulkMode)
                <button wire:click="calculateAllAnnualAverages" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Calculer moyennes annuelles
                </button>
                @endif
            </div>
        </div>
        
        <!-- Messages de notification -->
        @if($showSuccessMessage)
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded-lg flex justify-between items-center">
                <span>{{ $successMessage }}</span>
                <button wire:click="closeSuccessMessage" class="text-green-800 hover:text-green-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif
        
        <!-- Mode gestion en masse par classe -->
        @if($isBulkMode)
            @if(!$selectedClasse)
                <div class="text-center py-8 text-gray-500">
                    Veuillez sélectionner une classe pour gérer les moyennes en masse.
                </div>
            @elseif(count($classStudents) === 0)
                <div class="text-center py-8 text-gray-500">
                    Aucun élève inscrit dans cette classe pour l'année active.
                </div>
            @else
                <form wire:submit.prevent="saveBulkAverages">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left">Élève</th>
                                    <th class="py-3 px-4 text-left">Moyenne</th>
                                    <th class="py-3 px-4 text-left">Rang</th>
                                    <th class="py-3 px-4 text-left">Commentaire</th>
                                    <th class="py-3 px-4 text-left">Décision</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($classStudents as $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ $student->nom }} {{ $student->prenom }}</td>
                                        <td class="py-3 px-4">
                                            <input type="number" step="0.01" min="0" max="20" 
                                                wire:model="studentAverages.{{ $student->id }}.value" 
                                                class="w-24 px-2 py-1 border rounded">
                                            @error("studentAverages.$student->id.value") 
                                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                                            @enderror
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" min="1" 
                                                wire:model="studentAverages.{{ $student->id }}.rank" 
                                                class="w-16 px-2 py-1 border rounded">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" 
                                                wire:model="studentAverages.{{ $student->id }}.teacher_comment" 
                                                class="w-full px-2 py-1 border rounded">
                                        </td>
                                        <td class="py-3 px-4">
                                            <select wire:model="studentAverages.{{ $student->id }}.decision" 
                                                class="w-full px-2 py-1 border rounded">
                                                <option value="">Sélectionner</option>
                                                <option value="Passage">Passage</option>
                                                <option value="Redoublement">Redoublement</option>
                                                <option value="Exclusion">Exclusion</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Enregistrer toutes les moyennes
                        </button>
                    </div>
                </form>
            @endif
        
        <!-- Mode affichage individuel -->
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Élève</th>
                            <th class="py-3 px-4 text-left">Classe</th>
                            <th class="py-3 px-4 text-left">Trimestre 1</th>
                            <th class="py-3 px-4 text-left">Trimestre 2</th>
                            <th class="py-3 px-4 text-left">Trimestre 3</th>
                            <th class="py-3 px-4 text-left">Moyenne Annuelle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $student->nom }} {{ $student->prenom }}</td>
                                <td class="py-3 px-4">
                                    @if($student->attributtions->isNotEmpty())
                                        {{ $student->attributtions->first()->classe->libelle ?? 'Non assigné' }}
                                    @else
                                        Non assigné
                                    @endif
                                </td>
                                
                                <!-- Trimestre 1 -->
                                <td class="py-3 px-4">
                                    @if(isset($averages[$student->id]['Trimestre 1']))
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averages[$student->id]['Trimestre 1']->value >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averages[$student->id]['Trimestre 1']->value, 2) }}/20
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                
                                <!-- Trimestre 2 -->
                                <td class="py-3 px-4">
                                    @if(isset($averages[$student->id]['Trimestre 2']))
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averages[$student->id]['Trimestre 2']->value >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averages[$student->id]['Trimestre 2']->value, 2) }}/20
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                
                                <!-- Trimestre 3 -->
                                <td class="py-3 px-4">
                                    @if(isset($averages[$student->id]['Trimestre 3']))
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averages[$student->id]['Trimestre 3']->value >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averages[$student->id]['Trimestre 3']->value, 2) }}/20
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                
                                <!-- Moyenne Annuelle -->
                                <td class="py-3 px-4">
                                    @if(isset($averages[$student->id]['Annuelle']))
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averages[$student->id]['Annuelle']->value >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averages[$student->id]['Annuelle']->value, 2) }}/20
                                        </span>
                                        @if(isset($averages[$student->id]['Annuelle']->decision) && !empty($averages[$student->id]['Annuelle']->decision))
                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $averages[$student->id]['Annuelle']->decision }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-3 px-4 text-center text-gray-500">Aucun élève trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(!$isBulkMode && method_exists($students, 'links'))
                <div class="mt-4">
                    {{ $students->links() }}
                </div>
            @endif
        @endif
    </div>
    
    <!-- Modal d'ajout/édition de moyenne -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $isEditing ? 'Modifier la moyenne' : 'Ajouter une moyenne' }}
                        </h3>
                        <div class="mt-4">
                            <form wire:submit.prevent="saveAverage">
                                <div class="mb-4">
                                    <label for="studentId" class="block text-sm font-medium text-gray-700">Élève</label>
                                    <select id="studentId" wire:model="studentId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isEditing ? 'disabled' : '' }}>
                                        <option value="">Sélectionner un élève</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->nom }} {{ $student->prenom }}</option>
                                        @endforeach
                                    </select>
                                    @error('studentId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="classeId" class="block text-sm font-medium text-gray-700">Classe</label>
                                    <select id="classeId" wire:model="classeId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isEditing ? 'disabled' : '' }}>
                                        <option value="">Sélectionner une classe</option>
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}">{{ $classe->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('classeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="period" class="block text-sm font-medium text-gray-700">Période</label>
                                    <select id="period" wire:model="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $isEditing ? 'disabled' : '' }}>
                                        @foreach($periods as $p)
                                            <option value="{{ $p }}">{{ $p }}</option>
                                        @endforeach
                                    </select>
                                    @error('period') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="value" class="block text-sm font-medium text-gray-700">Moyenne</label>
                                    <input type="number" step="0.01" min="0" max="20" id="value" wire:model="value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="rank" class="block text-sm font-medium text-gray-700">Rang</label>
                                    <input type="number" min="1" id="rank" wire:model="rank" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('rank') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="teacherComment" class="block text-sm font-medium text-gray-700">Commentaire</label>
                                    <textarea id="teacherComment" wire:model="teacherComment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @error('teacherComment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="decision" class="block text-sm font-medium text-gray-700">Décision</label>
                                    <input type="text" id="decision" wire:model="decision" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('decision') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="saveAverage" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ $isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Modal de confirmation de suppression -->
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Confirmer la suppression
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Êtes-vous sûr de vouloir supprimer cette moyenne ? Cette action est irréversible.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="deleteAverage" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Supprimer
                        </button>
                        <button wire:click="cancelDelete" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
