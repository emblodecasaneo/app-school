<div>
    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Carte: Total des élèves -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total des élèves</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalStudents }}</p>
                    <p class="text-sm text-gray-600">{{ $maleStudents }} garçons / {{ $femaleStudents }} filles</p>
                </div>
            </div>
        </div>

        <!-- Carte: Total des classes -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total des classes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalClasses }}</p>
                    <p class="text-sm text-gray-600">{{ $totalLevels }} niveaux</p>
                </div>
            </div>
        </div>

        <!-- Carte: Revenus -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Revenus totaux</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm text-gray-600">{{ $totalPayments }} paiements</p>
                </div>
            </div>
        </div>

        <!-- Carte: Taux de paiement -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Taux de paiement</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $paymentRate }}%</p>
                    <p class="text-sm text-gray-600">{{ $unpaidStudents }} élèves en retard</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Répartition des élèves par niveau -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition des élèves par niveau</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'élèves</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($studentsPerLevel as $level)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $level['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $level['count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $totalStudents > 0 ? round(($level['count'] / $totalStudents) * 100, 1) : 0 }}%
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalStudents > 0 ? round(($level['count'] / $totalStudents) * 100, 1) : 0 }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Derniers paiements -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Derniers paiements</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $payment->student->nom ?? 'N/A' }} {{ $payment->student->prenom ?? '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($payment->montant, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->solvable == '1')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Soldé
                                </span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Reste: {{ number_format($payment->reste, 0, ',', ' ') }} FCFA
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques financières -->
    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistiques financières</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Revenus perçus</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Revenus attendus</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalExpectedRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Reste à percevoir</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalExpectedRevenue - $totalRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
            
            <!-- Graphique des paiements par mois (représenté par des barres) -->
            <div class="mt-6">
                <h4 class="text-md font-semibold text-gray-700 mb-2">Paiements par mois</h4>
                <div class="flex items-end space-x-2 h-40">
                    @php
                        $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
                        $maxPayment = max($paymentsByMonth) > 0 ? max($paymentsByMonth) : 1;
                    @endphp
                    
                    @foreach($paymentsByMonth as $month => $amount)
                        <div class="flex flex-col items-center">
                            <div class="w-12 bg-blue-500 rounded-t" style="height: {{ ($amount / $maxPayment) * 100 }}%"></div>
                            <div class="text-xs text-gray-500 mt-1">{{ $months[$month-1] }}</div>
                            <div class="text-xs text-gray-700">{{ number_format($amount, 0, ',', ' ') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition par genre -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition par genre</h3>
            <div class="flex items-center justify-center">
                <div class="w-full max-w-md">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-blue-700">Garçons ({{ $maleStudents }})</span>
                        <span class="text-sm font-medium text-pink-700">Filles ({{ $femaleStudents }})</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-6">
                        @php
                            $malePercentage = $totalStudents > 0 ? ($maleStudents / $totalStudents) * 100 : 0;
                        @endphp
                        <div class="bg-blue-600 h-6 rounded-l-full" style="width: {{ $malePercentage }}%; display: inline-block;"></div>
                        <div class="bg-pink-500 h-6 rounded-r-full" style="width: {{ 100 - $malePercentage }}%; display: inline-block;"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-gray-500">{{ round($malePercentage, 1) }}%</span>
                        <span class="text-xs text-gray-500">{{ round(100 - $malePercentage, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Taux de recouvrement</h3>
            <div class="flex items-center justify-center">
                <div class="w-full max-w-md">
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                                    Progression
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-green-600">
                                    {{ $paymentRate }}%
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-6 mb-4 text-xs flex rounded bg-gray-200">
                            <div style="width:{{ $paymentRate }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500">
                                {{ number_format($totalRevenue, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</span> perçus sur 
                            <span class="font-medium">{{ number_format($totalExpectedRevenue, 0, ',', ' ') }} FCFA</span> attendus
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
