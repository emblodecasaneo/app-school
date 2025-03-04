<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">{{ __('Gestion des Notes') }}</h2>
            
            <!-- Messages flash -->
            @if (session()->has('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session()->has('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Filtres -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Rechercher') }}</label>
                        <input type="text" wire:model.debounce.300ms="search" id="search" placeholder="Nom, prénom ou matricule..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="selectedClasse" class="block text-sm font-medium text-gray-700">Classe</label>
                        <select id="selectedClasse" wire:model="selectedClasse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedClasse') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="selectedSubject" class="block text-sm font-medium text-gray-700">{{ __('Matière') }}</label>
                        <select wire:model="selectedSubject" id="selectedSubject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Toutes les matières') }}</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject }}">{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="selectedPeriod" class="block text-sm font-medium text-gray-700">{{ __('Période') }}</label>
                        <select wire:model="selectedPeriod" id="selectedPeriod" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Toutes les périodes') }}</option>
                            @foreach($periods as $period)
                                <option value="{{ $period }}">{{ $period }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Liste des étudiants -->
            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2">{{ __('Étudiants') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Matricule') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nom') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Classe') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Moyenne') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($students as $student)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->matricule }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->nom }} {{ $student->prenom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($student->attributions->where('school_year_id', $activeYear->id)->first())
                                            {{ $student->attributions->where('school_year_id', $activeYear->id)->first()->classe->libelle }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $studentAverages[$student->id] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($studentAverages[$student->id], 2) }}/20
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="openGradeForm({{ $student->id }})" class="text-indigo-600 hover:text-indigo-900">{{ __('Ajouter une note') }}</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">{{ __('Aucun étudiant trouvé') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $students->links() }}
                </div>
            </div>
            
            <!-- Liste des notes -->
            <div>
                <h3 class="text-lg font-medium mb-2">{{ __('Notes récentes') }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Étudiant') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Matière') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Période') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Note') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Coef.') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($grades as $grade)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $grade->student->nom }} {{ $grade->student->prenom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $grade->subject }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $grade->period }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $types[$grade->type] ?? $grade->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $grade->value >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $grade->value }}/20
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $grade->coefficient }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="editGrade({{ $grade->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Modifier') }}</button>
                                        <button wire:click="confirmGradeDeletion({{ $grade->id }})" class="text-red-600 hover:text-red-900">{{ __('Supprimer') }}</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">{{ __('Aucune note trouvée') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $grades->links() }}
                </div>
            </div>
            
            <!-- Formulaire d'ajout/modification de note (modal) -->
            @if($showGradeForm)
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $isEditing ? __('Modifier la note') : __('Ajouter une note') }}
                            </h3>
                            
                            <div class="mt-4 grid grid-cols-1 gap-4">
                                @if(!$isEditing)
                                    <div>
                                        <label for="studentId" class="block text-sm font-medium text-gray-700">{{ __('Étudiant') }}</label>
                                        <select wire:model="studentId" id="studentId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">{{ __('Sélectionner un étudiant') }}</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->nom }} {{ $student->prenom }}</option>
                                            @endforeach
                                        </select>
                                        @error('studentId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700">{{ __('Matière') }}</label>
                                    <select wire:model="subject" id="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('Sélectionner une matière') }}</option>
                                        @foreach($subjects as $subj)
                                            <option value="{{ $subj }}">{{ $subj }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="period" class="block text-sm font-medium text-gray-700">{{ __('Période') }}</label>
                                    <select wire:model="period" id="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('Sélectionner une période') }}</option>
                                        @foreach($periods as $per)
                                            <option value="{{ $per }}">{{ $per }}</option>
                                        @endforeach
                                    </select>
                                    @error('period') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                                    <select wire:model="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach($types as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="value" class="block text-sm font-medium text-gray-700">{{ __('Note') }}</label>
                                        <input type="number" wire:model="value" id="value" step="0.01" min="0" max="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="coefficient" class="block text-sm font-medium text-gray-700">{{ __('Coefficient') }}</label>
                                        <input type="number" wire:model="coefficient" id="coefficient" step="0.5" min="0.5" max="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('coefficient') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700">{{ __('Commentaire') }}</label>
                                    <textarea wire:model="comment" id="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click="saveGrade" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $isEditing ? __('Mettre à jour') : __('Enregistrer') }}
                            </button>
                            <button wire:click="closeGradeForm" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('Annuler') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Modal de confirmation de suppression -->
            @if($confirmingGradeDeletion)
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Confirmer la suppression') }}</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">{{ __('Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click="deleteGrade" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('Supprimer') }}
                            </button>
                            <button wire:click="cancelGradeDeletion" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('Annuler') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
