<div>
    <!-- Recherche d'élève -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rechercher un élève</h3>
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text" wire:model.live="searchMatricule" wire:keydown.enter="searchStudent" 
                    placeholder="Rechercher par matricule, nom ou prénom" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <button wire:click="searchStudent" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Rechercher
            </button>
        </div>

        <!-- Résultats de recherche -->
        @if($showSearchResults && count($searchResults) > 0)
        <div class="mt-4 bg-gray-50 rounded-md p-4">
            <h4 class="text-md font-medium text-gray-700 mb-2">Résultats de recherche</h4>
            <ul class="divide-y divide-gray-200">
                @foreach($searchResults as $result)
                <li class="py-2">
                    <button wire:click="selectStudent({{ $result->id }})" class="w-full text-left px-3 py-2 hover:bg-gray-100 rounded-md">
                        <span class="font-medium">{{ $result->nom }} {{ $result->prenom }}</span> - 
                        <span class="text-gray-600">Matricule: {{ $result->matricule }}</span>
                    </button>
                </li>
                @endforeach
            </ul>
        </div>
        @elseif($showSearchResults && count($searchResults) == 0)
        <div class="mt-4 bg-yellow-50 text-yellow-800 p-4 rounded-md">
            Aucun élève trouvé avec ces critères de recherche.
        </div>
        @endif
    </div>

    @if($student)
    <!-- Informations de l'élève -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $student->nom }} {{ $student->prenom }}</h2>
                <p class="text-gray-600">Matricule: {{ $student->matricule }}</p>
                <p class="text-gray-600">Date de naissance: {{ $student->date_naissance }}</p>
                <p class="text-gray-600">Sexe: {{ $student->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                <p class="text-gray-600">Contact parent: {{ $student->contact_parent }}</p>
            </div>
            <div class="text-right">
                @if($currentAttribution)
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-md">
                    <p class="font-semibold">Classe actuelle: {{ $currentAttribution->classe->libelle ?? 'N/A' }}</p>
                    <p>Niveau: {{ $currentAttribution->classe->level->libelle ?? 'N/A' }}</p>
                    <p>Année: {{ $activeYear->school_year }}</p>
                </div>
                @else
                <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-md">
                    <p>Non inscrit pour l'année scolaire en cours</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statut financier pour l'année en cours -->
    @if($currentAttribution)
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Statut financier ({{ $activeYear->school_year }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">Scolarité totale</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalDue, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">Montant payé</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500">Reste à payer</p>
                <p class="text-2xl font-bold {{ $totalPaid >= $totalDue ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format(max(0, $totalDue - $totalPaid), 0, ',', ' ') }} FCFA
                </p>
            </div>
        </div>
        
        <!-- Barre de progression -->
        <div class="mt-6">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-gray-700">Progression des paiements</span>
                <span class="text-sm font-medium text-gray-700">
                    {{ $totalDue > 0 ? round(($totalPaid / $totalDue) * 100) : 0 }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-green-600 h-4 rounded-full" style="width: {{ $totalDue > 0 ? min(100, ($totalPaid / $totalDue) * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Historique académique -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique académique</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année scolaire</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scolarité</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payé</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($academicHistory as $history)
                    <tr class="{{ $history['is_active'] ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $history['is_active'] ? 'text-blue-800' : 'text-gray-900' }}">
                            {{ $history['year'] }}
                            @if($history['is_active'])
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Actuelle
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history['level'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history['classe'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($history['scolarite'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($history['paid'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($history['status'] == 'Soldé')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Soldé
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Non soldé
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Historique des paiements -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique des paiements</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année scolaire</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reste</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($payment['created_at'])->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment['schoolyear']['school_year'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($payment['montant'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($payment['reste'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment['solvable'] == '1')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Soldé
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                En cours
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
