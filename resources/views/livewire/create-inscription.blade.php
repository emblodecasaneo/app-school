<div class="p-6 bg-white shadow-sm rounded-lg">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        
        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif
        
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Inscription d'un élève pour l'année scolaire {{ $activeYear->school_year ?? 'active' }}</h2>
            <p class="text-gray-600">Veuillez remplir tous les champs obligatoires marqués d'un astérisque (*)</p>
        </div>
        
        <!-- Section de recherche d'élève -->
        <div class="mb-8 bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium text-gray-800 mb-4">1. Rechercher et sélectionner un élève</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Champ de recherche avancée -->
                <div>
                    <label for="searchQuery" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un élève<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="searchQuery" 
                            wire:model.live.debounce.300ms="searchQuery" 
                            placeholder="Rechercher par nom, prénom ou matricule" 
                            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('student_id') border-red-500 @enderror">
                        
                        @if($showSearchResults && count($searchResults) > 0)
                            <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-y-auto border border-gray-200">
                                <ul class="py-1">
                                    @foreach($searchResults as $student)
                                        <li wire:click="selectStudent({{ $student->id }})" 
                                            class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium text-indigo-700">{{ $student->nom }} {{ $student->prenom }}</span>
                                                    <p class="text-sm text-gray-600">Matricule: {{ $student->matricule }}</p>
                                                </div>
                                                <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">Sélectionner</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif($showSearchResults && count($searchResults) === 0)
                            <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200">
                                <div class="px-4 py-3 text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Aucun élève trouvé
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('student_id')
                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Champ pour le matricule (maintenu pour compatibilité) -->
                <div>
                    <label for="matricule" class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                    <input type="text" id="matricule" 
                        wire:model.live="matricule" 
                        placeholder="Entrer le matricule" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>
            
            <!-- Informations de l'élève sélectionné -->
            @if($selectedStudent)
                <div class="mt-4 bg-indigo-50 p-4 rounded-md">
                    <h4 class="font-medium text-indigo-800 mb-2">Élève sélectionné</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nom complet:</p>
                            <p class="font-medium text-gray-900">{{ $nom }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Matricule:</p>
                            <p class="font-medium text-gray-900">{{ $matricule }}</p>
                        </div>
                    </div>
                    
                    @if(isset($studentInfo['alreadyRegistered']) && $studentInfo['alreadyRegistered'])
                        <div class="mt-3 bg-yellow-50 p-3 rounded-md">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-yellow-800 font-medium">Cet élève est déjà inscrit dans la classe {{ $studentInfo['classe'] }} pour cette année scolaire.</p>
                            </div>
                        </div>
                    @else
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Genre:</p>
                                <p class="font-medium text-gray-900">{{ $studentInfo['gender'] == 'M' ? 'Masculin' : 'Féminin' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date de naissance:</p>
                                <p class="font-medium text-gray-900">{{ $studentInfo['birthdate'] ?? 'Non renseignée' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Contact parent:</p>
                                <p class="font-medium text-gray-900">{{ $studentInfo['contact'] ?? 'Non renseigné' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Section de sélection de classe -->
        <div class="mb-8 bg-gray-50 p-4 rounded-lg {{ (!$selectedStudent || (isset($studentInfo['alreadyRegistered']) && $studentInfo['alreadyRegistered'])) ? 'opacity-50 pointer-events-none' : '' }}">
            <h3 class="text-lg font-medium text-gray-800 mb-4">2. Sélectionner une classe</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Champ pour le choix du niveau -->
                <div>
                    <label for="level_id" class="block text-sm font-medium text-gray-700 mb-1">Niveau<span class="text-red-500">*</span></label>
                    <select id="level_id" 
                        wire:model.live="level_id" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('level_id') border-red-500 @enderror">
                        <option value="">Sélectionner un niveau</option>
                        @foreach ($getAllLevels as $item)
                            <option value="{{ $item->id }}">{{ $item->libelle }}</option>
                        @endforeach
                    </select>
                    @error('level_id')
                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Champ pour le choix de la classe -->
                <div>
                    <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-1">Classe<span class="text-red-500">*</span></label>
                    <select id="classe_id" 
                        wire:model.live="classe_id" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('classe_id') border-red-500 @enderror"
                        {{ count($classList) == 0 ? 'disabled' : '' }}>
                        <option value="">{{ count($classList) == 0 ? 'Sélectionnez d\'abord un niveau' : 'Sélectionner une classe' }}</option>
                        @foreach ($classList as $item)
                            <option value="{{ $item->id }}">{{ $item->libelle }}</option>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Informations de la classe sélectionnée -->
            @if($selectedClassInfo)
                <div class="mt-4 bg-blue-50 p-4 rounded-md">
                    <h4 class="font-medium text-blue-800 mb-2">Classe sélectionnée: {{ $selectedClassInfo['libelle'] }}</h4>
                    <p class="text-blue-700">
                        <span class="font-medium">Effectif actuel:</span> {{ $selectedClassInfo['effectif'] }} élève(s)
                    </p>
                </div>
            @endif
        </div>
        
        <!-- Section de commentaire -->
        <div class="mb-8 bg-gray-50 p-4 rounded-lg {{ (!$selectedStudent || (isset($studentInfo['alreadyRegistered']) && $studentInfo['alreadyRegistered'])) ? 'opacity-50 pointer-events-none' : '' }}">
            <h3 class="text-lg font-medium text-gray-800 mb-4">3. Ajouter un commentaire (optionnel)</h3>
            
            <div>
                <label for="comments" class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                <textarea id="comments" 
                    wire:model="comments" 
                    rows="3"
                    placeholder="Ajouter un commentaire concernant cette inscription..." 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            </div>
        </div>
        
        <!-- Section pour les boutons d'action -->
        <div class="flex justify-end items-center space-x-4 mt-8">
            <a href="{{ route('inscriptions') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors font-medium">
                Annuler
            </a>
            <button type="submit" 
                class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                {{ (!$selectedStudent || (isset($studentInfo['alreadyRegistered']) && $studentInfo['alreadyRegistered'])) ? 'disabled' : '' }}>
                Valider l'inscription
            </button>
        </div>
    </form>
</div>
