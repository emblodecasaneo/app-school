<div class="p-5 bg-white shadow-sm">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        
        @if (Session::get('error'))
            <div class="border-red-500 p-3 rounded-md bg-red-100 mb-4">{{ Session::get('error') }}</div>
        @endif
        
        @if($activeYear)
        <div class="mb-4 bg-blue-50 p-3 rounded-md">
            <p class="text-blue-800">Enregistrement d'un paiement pour l'année scolaire <strong>{{ $activeYear->school_year }}</strong></p>
        </div>
        @else
        <div class="mb-4 bg-red-50 p-3 rounded-md">
            <p class="text-red-800">Attention : Aucune année scolaire active. Veuillez activer une année scolaire avant d'enregistrer un paiement.</p>
        </div>
        @endif

        <!-- Section de recherche d'élève -->
        <div class="mb-6">
            <div class="mb-4">
                <p class="text-gray-700 font-medium text-lg">Rechercher un élève<span class="text-red-500">*</span></p>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        class="pl-10 block rounded-md border-gray-300 w-full py-3 text-lg
                        @error('student_id') border-red-500 bg-red-50 @enderror"
                        placeholder="Rechercher par nom, prénom ou matricule" 
                        type="text" 
                        wire:model.live.debounce.300ms="searchQuery" />
                    
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
        </div>

        @if($student_id)
            <!-- Informations de l'élève sélectionné -->
            <div class="mb-6 bg-indigo-50 p-4 rounded-md">
                <h3 class="text-lg font-medium text-indigo-800 mb-2">Élève sélectionné</h3>
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
            </div>

            @if($classe_id && $currentLevelAmount)
                <!-- Informations de paiement -->
                <div class="mb-6 bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Informations de paiement</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-md">
                            <p class="text-sm text-blue-800 font-medium">Montant total de la scolarité</p>
                            <p class="text-xl font-bold text-blue-900">{{ number_format($currentLevelAmount, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-md">
                            <p class="text-sm text-green-800 font-medium">Montant déjà payé</p>
                            <p class="text-xl font-bold text-green-900">{{ number_format($montantDejaPayé, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-md">
                            <p class="text-sm text-yellow-800 font-medium">Reste à payer</p>
                            <p class="text-xl font-bold {{ $montantRestant > 0 ? 'text-yellow-900' : 'text-green-900' }}">
                                {{ number_format($montantRestant, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    </div>
                </div>

                @if($montantRestant > 0)
                    <!-- Champ pour le montant -->
                    <div class="mb-6">
                        <p class="text-gray-700 font-medium text-lg">Montant du paiement<span class="text-red-500">*</span></p>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">FCFA</span>
                            </div>
                            <input
                                class="pl-16 block rounded-md border-gray-300 w-full py-3 text-lg
                                @error('montant') border-red-500 bg-red-50 @enderror"
                                placeholder="Entrer le montant du paiement" 
                                type="number" 
                                min="1" 
                                max="{{ $montantRestant }}" 
                                wire:model="montant" />
                        </div>
                        @error('montant')
                            <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Montant maximum autorisé: <strong>{{ number_format($montantRestant, 0, ',', ' ') }} FCFA</strong></p>
                    </div>

                    <!-- Section pour les buttons d'action -->
                    <div class="mt-8 flex justify-end items-center space-x-4">
                        <a href="{{ route('paiements') }}" class="bg-gray-200 hover:bg-gray-300 px-6 py-3 rounded-md text-gray-700 font-medium transition-colors">
                            Annuler
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-md text-white font-medium transition-colors">
                            Enregistrer le paiement
                        </button>
                    </div>
                @else
                    <div class="mb-6 bg-green-100 p-4 rounded-md">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-green-800 font-medium">Cet élève a déjà entièrement payé sa scolarité pour l'année en cours.</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="mb-6 bg-red-50 p-4 rounded-md">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-red-800 font-medium">{{ $nom }}</p>
                    </div>
                </div>
            @endif
        @else
            <div class="mt-8 flex justify-end items-center space-x-4">
                <a href="{{ route('paiements') }}" class="bg-gray-200 hover:bg-gray-300 px-6 py-3 rounded-md text-gray-700 font-medium transition-colors">
                    Retour à la liste
                </a>
            </div>
        @endif
    </form>
</div>
