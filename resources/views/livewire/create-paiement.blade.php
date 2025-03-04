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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Champ pour le matricule -->
            <div class="mb-4">
                <p class="text-gray-700 font-medium">Matricule de l'élève<span class="text-red-500">*</span></p>
                <div class="flex">
                    <input
                        class="block rounded-md border-gray-300 w-full
                        @error('matricule') border-red-500 bg-red-50 @enderror"
                        placeholder="Entrer le matricule" type="text" wire:model.live="matricule" />
                </div>
                @error('matricule')
                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <!-- Champ pour le nom de l'élève -->
            <div class="mb-4">
                <p class="text-gray-700 font-medium">Nom de l'élève</p>
                <input
                    class="block rounded-md border-gray-300 w-full
                    @if(strpos($nom, 'n\'est pas inscrit') !== false || strpos($nom, 'Aucun') !== false || strpos($nom, 'déjà entièrement payée') !== false) 
                        border-red-500 bg-red-50 text-red-700 font-medium
                    @endif"
                    readonly type="text" wire:model="nom" />
                @error('student_id')
                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @if($student_id && $classe_id && $currentLevelAmount)
            <div class="mb-4 bg-gray-50 p-4 rounded-md">
                <h3 class="text-lg font-medium text-gray-800 mb-2">Informations de paiement</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-3 rounded-md">
                        <p class="text-sm text-blue-800 font-medium">Montant total de la scolarité</p>
                        <p class="text-xl font-bold text-blue-900">{{ number_format($currentLevelAmount, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-md">
                        <p class="text-sm text-green-800 font-medium">Montant déjà payé</p>
                        <p class="text-xl font-bold text-green-900">{{ number_format($montantDejaPayé, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-md">
                        <p class="text-sm text-yellow-800 font-medium">Reste à payer</p>
                        <p class="text-xl font-bold {{ $montantRestant > 0 ? 'text-yellow-900' : 'text-green-900' }}">
                            {{ number_format($montantRestant, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                </div>
            </div>

            @if($montantRestant > 0)
                <!-- Champ pour le montant -->
                <div class="mb-4">
                    <p class="text-gray-700 font-medium">Montant du paiement<span class="text-red-500">*</span></p>
                    <input
                        class="block rounded-md border-gray-300 w-full
                        @error('montant') border-red-500 bg-red-50 @enderror"
                        placeholder="Entrer le montant du paiement" 
                        type="number" 
                        min="1" 
                        max="{{ $montantRestant }}" 
                        wire:model="montant" />
                    @error('montant')
                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Montant maximum autorisé: <strong>{{ number_format($montantRestant, 0, ',', ' ') }} FCFA</strong></p>
                </div>

                <!-- Section pour les buttons d'action -->
                <div class="mt-6 flex justify-between items-center">
                    <a href="{{ route('paiements') }}" class="bg-red-500 p-2 rounded-md text-white text-md">Annuler</a>
                    <button type="submit" class="bg-blue-600 p-2 rounded-md text-white text-md">
                        Enregistrer le paiement
                    </button>
                </div>
            @else
                <div class="mb-4 bg-green-100 p-4 rounded-md">
                    <p class="text-green-800 font-medium">Cet élève a déjà entièrement payé sa scolarité pour l'année en cours.</p>
                </div>
                <div class="mt-6 flex justify-between items-center">
                    <a href="{{ route('paiements') }}" class="bg-red-500 p-2 rounded-md text-white text-md">Annuler</a>
                    <button type="button" disabled class="bg-gray-400 p-2 rounded-md text-white text-md">
                        Enregistrer le paiement
                    </button>
                </div>
            @endif
        @else
            <div class="mt-6 flex justify-between items-center">
                <a href="{{ route('paiements') }}" class="bg-red-500 p-2 rounded-md text-white text-md">Annuler</a>
                <button type="button" disabled class="bg-gray-400 p-2 rounded-md text-white text-md">
                    Enregistrer le paiement
                </button>
            </div>
        @endif
    </form>
</div>
