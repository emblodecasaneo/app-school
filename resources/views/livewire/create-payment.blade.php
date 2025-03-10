<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Enregistrer un nouveau paiement</h2>
    
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Recherche et sélection d'élève -->
        <div>
            <label for="searchStudent" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un élève</label>
            <div class="relative">
                <input type="text" id="searchStudent" wire:model.live.debounce.300ms="searchStudent" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Nom, prénom ou matricule de l'élève">
                
                @if(count($students) > 0)
                    <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-y-auto">
                        <ul class="py-1">
                            @foreach($students as $student)
                                <li wire:click="selectStudent({{ $student->id }})" 
                                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center">
                                    <span class="font-medium">{{ $student->nom }} {{ $student->prenom }}</span>
                                    <span class="ml-2 text-sm text-gray-500">({{ $student->matricule }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            
            @if($selectedStudent)
                <div class="mt-2 p-3 bg-blue-50 rounded-md">
                    <h3 class="font-medium text-blue-800">Élève sélectionné:</h3>
                    <p class="text-blue-700">{{ $selectedStudent->nom }} {{ $selectedStudent->prenom }} - {{ $selectedStudent->matricule }}</p>
                </div>
            @endif
            
            @error('student_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        
        <!-- Sélection de classe -->
        <div>
            <label for="classe_id" class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
            <select id="classe_id" wire:model="classe_id" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Sélectionner une classe</option>
                @foreach($classes as $classe)
                    <option value="{{ $classe->id }}">{{ $classe->libelle }}</option>
                @endforeach
            </select>
            @error('classe_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        
        <!-- Montant du paiement -->
        <div>
            <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Montant payé (FCFA)</label>
            <input type="number" id="montant" wire:model="montant" wire:change="calculateReste"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @error('montant') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        
        <!-- Reste à payer -->
        <div>
            <label for="reste" class="block text-sm font-medium text-gray-700 mb-1">Reste à payer (FCFA)</label>
            <input type="number" id="reste" wire:model="reste"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @error('reste') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        
        <!-- Statut de paiement -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Statut de paiement</label>
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="radio" wire:model="solvable" value="0" class="form-radio text-indigo-600">
                    <span class="ml-2">Partiel</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" wire:model="solvable" value="1" class="form-radio text-indigo-600">
                    <span class="ml-2">Solvable (payé intégralement)</span>
                </label>
            </div>
            @error('solvable') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        
        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-3 pt-4">
            <a href="{{ route('paiements') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                Enregistrer le paiement
            </button>
        </div>
    </form>
    
    <!-- Modal de succès avec option d'impression -->
    @if($showSuccessModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <svg class="h-16 w-16 text-green-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Paiement enregistré avec succès!</h3>
                <p class="text-gray-600 mb-6">Voulez-vous imprimer le reçu de paiement maintenant?</p>
                
                <div class="flex justify-center space-x-4">
                    <button wire:click="closeSuccessModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                        Plus tard
                    </button>
                    <button wire:click="printReceipt" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                        </svg>
                        Imprimer maintenant
                    </button>
                    <button wire:click="downloadPdf" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Télécharger PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
