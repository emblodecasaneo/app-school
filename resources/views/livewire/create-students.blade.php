<div class="p-5 bg-white shadow-sm">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        @if (Session::get('error'))
            <div class="border-red-500 p-3 rounded-md bg-red-100 mb-4">{{ Session::get('error') }}</div>
        @endif
        
        @if (Session::get('warning'))
            <div class="border-yellow-500 p-3 rounded-md bg-yellow-100 mb-4">{{ Session::get('warning') }}</div>
        @endif
        
        @if($activeYear)
        <div class="mb-4 bg-blue-50 p-3 rounded-md">
            <p class="text-blue-800">Ajout d'un élève pour l'année scolaire <strong>{{ $activeYear->school_year }}</strong></p>
        </div>
        @else
        <div class="mb-4 bg-red-50 p-3 rounded-md">
            <p class="text-red-800">Attention : Aucune année scolaire active. Veuillez activer une année scolaire avant d'ajouter un élève.</p>
        </div>
        @endif
        
        <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <!-- Champs pour le nom -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Nom<span class="text-red-500">*</span></p>
                        <input
                            class="block rounded-md border-gray-300 w-full
                     @error('nom') border-red-500 bg-red-50  @enderror"
                            placeholder="Entrer le nom" type="text" wire:model="nom" />
                        @error('nom')
                            <div class="text-red-500 mt-1 text-sm">Le nom est requis</div>
                        @enderror
                    </div>

                    <!-- Champs pour le prénom -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Prénom<span class="text-red-500">*</span></p>
                        <input class="block rounded-md border-gray-300 w-full" placeholder="Entrer le prénom" type="text"
                            wire:model="prenom" />
                    </div>

                    <!-- Champs pour la date de naissance -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Date de naissance<span class="text-red-500">*</span></p>
                        <input
                            class="block rounded-md border-gray-300 w-full
                     @error('naissance') border-red-500 bg-red-50  @enderror"
                            placeholder="Entrez la date de nasissance" type="date" wire:model="naissance" />
                        @error('naissance')
                            <div class="text-red-500 mt-1 text-sm">La date de naissance est requise</div>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <!-- champs pour le matricule -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Matricule<span class="text-red-500">*</span></p>
                        <input
                            class="block rounded-md border-gray-300 w-full bg-gray-50 text-gray-500"
                            placeholder="Ce champs n'est pas à remplir, un matricule sera automatiquement généré" type="text" wire:model="matricule" disabled />
                        @error('matricule')
                            <div class="text-red-500 mt-1 text-sm">Le matricule est requis et doit être unique</div>
                        @enderror
                    </div>

                    <!-- Champs pour le sexe -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Sexe<span class="text-red-500">*</span></p>
                        <select
                            class="block rounded-md border-gray-300 w-full
                     @error('sexe') border-red-500 bg-red-50 @enderror"
                            type="text" wire:model="sexe" name="sexe" id="sexe">
                            <option value="">Sélectionner le sexe</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                        @error('sexe')
                            <div class="text-red-500 mt-1 text-sm">Le sexe est requis</div>
                        @enderror
                    </div>

                    <!-- champs pour le contact du parent -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Contact du parent<span class="text-red-500">*</span></p>
                        <input
                            class="block rounded-md border-gray-300 w-full
                     @error('contact_parent') border-red-500 bg-red-50  @enderror"
                            placeholder="Entrez le contact du parent" type="text" wire:model="contact_parent" />
                        @error('contact_parent')
                            <div class="text-red-500 mt-1 text-sm">Le contact du parent est requis</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Section pour l'inscription -->
            <div class="mt-6 mb-4 border-t pt-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3">Inscription à une classe</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Sélection du niveau -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Niveau<span class="text-red-500">*</span></p>
                        <select
                            class="block rounded-md border-gray-300 w-full
                     @error('level_id') border-red-500 bg-red-50 @enderror"
                            wire:model.live="level_id">
                            <option value="">Sélectionner un niveau</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}">{{ $level->libelle }}</option>
                            @endforeach
                        </select>
                        @error('level_id')
                            <div class="text-red-500 mt-1 text-sm">Le niveau est requis</div>
                        @enderror
                    </div>
                    
                    <!-- Sélection de la classe -->
                    <div class="mb-5">
                        <p class="text-gray-700 font-medium">Classe<span class="text-red-500">*</span></p>
                        <select
                            class="block rounded-md border-gray-300 w-full
                     @error('classe_id') border-red-500 bg-red-50 @enderror"
                            wire:model="classe_id" {{ $level_id ? '' : 'disabled' }}>
                            <option value="">{{ $level_id ? (count($classes) ? 'Sélectionner une classe' : 'Aucune classe disponible pour ce niveau') : 'Sélectionnez d\'abord un niveau' }}</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->libelle }}</option>
                            @endforeach
                        </select>
                        @if($level_id && count($classes) == 0)
                            <div class="text-yellow-600 mt-1 text-sm">Aucune classe n'est disponible pour ce niveau. Veuillez d'abord créer une classe.</div>
                        @endif
                        @error('classe_id')
                            <div class="text-red-500 mt-1 text-sm">La classe est requise</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Block des boutons d'actions -->
        <div class="mt-5 flex justify-between items-center">
            <a href="{{ route('students') }}" class="bg-red-600 hover:bg-red-700 transition-colors p-2 rounded-md text-white text-md">Annuler</a>
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 transition-colors p-2 rounded-md text-white text-md" 
                {{ $activeYear ? '' : 'disabled' }}>
                Ajouter l'élève et l'inscrire
            </button>
        </div>
    </form>
</div>
