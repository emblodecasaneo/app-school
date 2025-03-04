<div>
    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Carte: Total des élèves -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-blue-100 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Total des élèves</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalStudents }}</p>
                    <p class="text-xs text-gray-600">{{ $maleStudents }} garçons / {{ $femaleStudents }} filles</p>
                </div>
            </div>
        </div>

        <!-- Carte: Total des classes -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-green-100 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Total des classes</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalClasses }}</p>
                    <p class="text-xs text-gray-600">{{ $totalLevels }} niveaux</p>
                </div>
            </div>
        </div>

        <!-- Carte: Revenus -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-yellow-100 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Revenus totaux</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                    <p class="text-xs text-gray-600">{{ $totalPayments }} paiements</p>
                </div>
            </div>
        </div>

        <!-- Carte: Taux de paiement -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-2 rounded-full bg-purple-100 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Taux de paiement</p>
                    <p class="text-xl font-bold text-gray-800">{{ $paymentRate }}%</p>
                    <p class="text-xs text-gray-600">{{ $unpaidStudents }} élèves en retard</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Répartition des élèves par niveau -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Répartition des élèves par niveau</h3>
            
            <!-- Graphique en courbe pour la répartition des élèves -->
            <div class="mb-4 h-56">
                <canvas id="studentsPerLevelChart"></canvas>
            </div>
            
            <!-- Tableau amélioré -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'élèves</th>
                            <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($studentsPerLevel as $level)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-3 w-3 rounded-full mr-2" style="background-color: {{ '#' . substr(md5($level['name']), 0, 6) }}"></div>
                                    <span class="text-xs font-medium text-gray-900">{{ $level['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                <span class="text-xs font-semibold text-gray-700">{{ $level['count'] }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                @php
                                    $percentage = $totalStudents > 0 ? round(($level['count'] / $totalStudents) * 100, 1) : 0;
                                @endphp
                                <div class="flex items-center justify-end">
                                    <span class="text-xs font-semibold text-gray-700 mr-2">{{ $percentage }}%</span>
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background-color: {{ '#' . substr(md5($level['name']), 0, 6) }}"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Derniers paiements -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Derniers paiements</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élève</th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                {{ $payment->student->nom ?? 'N/A' }} {{ $payment->student->prenom ?? '' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-500">
                                {{ number_format($payment->montant, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-500">
                                {{ $payment->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
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
    <div class="grid grid-cols-1 gap-4 mb-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Statistiques financières</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Revenus perçus</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Revenus attendus</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($totalExpectedRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Reste à percevoir</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($totalExpectedRevenue - $totalRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
            
            <!-- Graphique des paiements par mois (représenté par des barres) -->
            <div class="mt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Paiements par mois</h4>
                <div class="h-56">
                    <canvas id="paymentsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition par genre -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Répartition par genre</h3>
            <div class="flex flex-col items-center justify-center">
                <div class="w-full max-w-md mb-4">
                    <div class="flex justify-between mb-1">
                        <div class="flex items-center">
                            <div class="h-3 w-3 rounded-full bg-blue-600 mr-1"></div>
                            <span class="text-xs font-medium text-gray-700">Garçons ({{ $maleStudents }})</span>
                        </div>
                        <div class="flex items-center">
                            <div class="h-3 w-3 rounded-full bg-pink-500 mr-1"></div>
                            <span class="text-xs font-medium text-gray-700">Filles ({{ $femaleStudents }})</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        @php
                            $malePercentage = $totalStudents > 0 ? ($maleStudents / $totalStudents) * 100 : 0;
                        @endphp
                        <div class="flex h-4">
                            <div class="bg-blue-600 h-4 rounded-l-full" style="width: {{ $malePercentage }}%"></div>
                            <div class="bg-pink-500 h-4 rounded-r-full" style="width: {{ 100 - $malePercentage }}%"></div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs font-semibold text-gray-600">{{ round($malePercentage, 1) }}%</span>
                        <span class="text-xs font-semibold text-gray-600">{{ round(100 - $malePercentage, 1) }}%</span>
                    </div>
                </div>
                
                <!-- Graphique en donut pour la répartition par genre -->
                <div class="w-40 h-40">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Taux de recouvrement</h3>
            <div class="flex items-center justify-center">
                <div class="w-full max-w-md">
                    <div class="relative pt-1">
                        <div class="flex mb-1 items-center justify-between">
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
                        <div class="overflow-hidden h-4 mb-2 text-xs flex rounded bg-gray-200">
                            <div style="width:{{ $paymentRate }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500">
                                {{ number_format($totalRevenue, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="text-xs text-gray-600">
                            <span class="font-medium">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</span> perçus sur 
                            <span class="font-medium">{{ number_format($totalExpectedRevenue, 0, ',', ' ') }} FCFA</span> attendus
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nouvelles sections KPIs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        <!-- Taux d'assiduité -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Taux d'assiduité</h3>
            <div class="flex flex-col items-center">
                <!-- Jauge d'assiduité globale -->
                <div class="w-full max-w-md mb-4">
                    <div class="flex justify-between mb-1">
                        <span class="text-xs font-medium text-gray-700">Taux d'assiduité global</span>
                        <span class="text-xs font-semibold text-gray-700">{{ $attendanceRate }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="bg-green-600 h-4 rounded-full" style="width: {{ $attendanceRate }}%"></div>
                    </div>
                </div>
                
                <!-- Tableau des taux d'assiduité par classe -->
                <div class="w-full overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Taux</th>
                                <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attendanceByClass as $id => $class)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="text-xs font-medium text-gray-900">{{ $class['name'] }}</span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-center">
                                    <span class="text-xs font-semibold text-gray-700">{{ $class['rate'] }}%</span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-green-500" style="width: {{ $class['rate'] }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Évolution des inscriptions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Évolution des inscriptions</h3>
            
            <!-- Statistiques de croissance -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Année précédente</p>
                    <p class="text-lg font-bold text-gray-800">{{ $previousYearStudents }} élèves</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Croissance</p>
                    <div class="flex items-center">
                        <p class="text-lg font-bold {{ $enrollmentGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $enrollmentGrowth >= 0 ? '+' : '' }}{{ $enrollmentGrowth }}%
                        </p>
                        @if($enrollmentGrowth >= 0)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Graphique d'évolution des inscriptions -->
            <div class="h-56">
                <canvas id="enrollmentTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Prévisions financières -->
    <div class="grid grid-cols-1 gap-4 mt-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Prévisions financières</h3>
            
            <!-- Statistiques de projection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Revenus perçus</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Reste à percevoir</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($totalExpectedRevenue - $totalRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Projection à 6 mois</p>
                    @php
                        $totalProjection = 0;
                        foreach ($revenueProjection as $projection) {
                            $totalProjection += $projection['amount'];
                        }
                    @endphp
                    <p class="text-lg font-bold text-gray-800">{{ number_format($totalProjection, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
            
            <!-- Graphique de projection des revenus -->
            <div class="h-56">
                <canvas id="revenueProjectionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Statistiques académiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 mb-6">
        <!-- Suivi académique -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Suivi académique</h3>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Moyenne générale</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($academicPerformance['average'], 2) }}/20</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500">Taux de réussite</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($academicPerformance['passRate'], 1) }}%</p>
                </div>
            </div>
            
            <!-- Moyennes par période -->
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Moyennes par période</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Moyenne</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de réussite</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(['Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Annuelle'] as $period)
                            @if(isset($averagesByPeriod[$period]))
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900">{{ $period }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averagesByPeriod[$period]['avg'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averagesByPeriod[$period]['avg'], 2) }}/20
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averagesByPeriod[$period]['pass_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averagesByPeriod[$period]['pass_rate'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center text-xs text-gray-500">
                                        {{ $averagesByPeriod[$period]['count'] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Graphique des moyennes par période -->
            <div class="mt-4 h-56">
                <canvas id="academicPerformanceChart"></canvas>
            </div>
        </div>
        
        <!-- Performance par niveau -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Performance par niveau</h3>
            
            @if(count($performanceByLevel) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Moyenne</th>
                                <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de réussite</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($performanceByLevel as $levelId => $level)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900">{{ $level['name'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $level['average'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($level['average'], 2) }}/20
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <span class="text-xs font-semibold text-gray-700 mr-2">{{ number_format($level['passRate'], 1) }}%</span>
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $level['passRate'] >= 50 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $level['passRate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Graphique des performances par niveau -->
                <div class="mt-4 h-56">
                    <canvas id="levelPerformanceChart"></canvas>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    Aucune donnée de performance par niveau disponible.
                </div>
            @endif
        </div>
    </div>
    
    <!-- Performance par classe -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">Performance par classe</h3>
        
        @if(count($performanceByClass) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Moyenne</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de réussite</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trim. 1</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trim. 2</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Trim. 3</th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Annuelle</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($performanceByClass as $classId => $class)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900">{{ $class['name'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class['average'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ number_format($class['average'], 2) }}/20
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="text-xs font-semibold text-gray-700 mr-2">{{ number_format($class['passRate'], 1) }}%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $class['passRate'] >= 50 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $class['passRate'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                @if(isset($averagesByClass[$classId]))
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averagesByClass[$classId]['Trimestre 1'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averagesByClass[$classId]['Trimestre 1'], 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averagesByClass[$classId]['Trimestre 2'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averagesByClass[$classId]['Trimestre 2'], 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averagesByClass[$classId]['Trimestre 3'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averagesByClass[$classId]['Trimestre 3'], 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $averagesByClass[$classId]['Annuelle'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ number_format($averagesByClass[$classId]['Annuelle'], 2) }}
                                        </span>
                                    </td>
                                @else
                                    <td class="px-4 py-2 whitespace-nowrap text-center text-gray-400">-</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center text-gray-400">-</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center text-gray-400">-</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center text-gray-400">-</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                Aucune donnée de performance par classe disponible.
            </div>
        @endif
    </div>
</div>

<!-- Script pour le graphique Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique de répartition des élèves par niveau
        const levelLabels = [@foreach($studentsPerLevel as $level) '{{ $level['name'] }}', @endforeach];
        const levelData = [@foreach($studentsPerLevel as $level) {{ $level['count'] }}, @endforeach];
        const levelColors = [@foreach($studentsPerLevel as $level) '#' + '{{ substr(md5($level['name']), 0, 6) }}', @endforeach];
        
        // Création du graphique des niveaux
        const ctxLevel = document.getElementById('studentsPerLevelChart').getContext('2d');
        const studentsPerLevelChart = new Chart(ctxLevel, {
            type: 'bar',
            data: {
                labels: levelLabels,
                datasets: [{
                    label: 'Nombre d\'élèves',
                    data: levelData,
                    backgroundColor: levelColors,
                    borderColor: levelColors,
                    borderWidth: 1,
                    borderRadius: 4,
                    barThickness: 20,
                    maxBarThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 11
                        },
                        padding: 10,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return `Nombre d'élèves: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
        
        // Données pour le graphique des paiements par mois
        const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        const paymentData = [
            @foreach($paymentsByMonth as $month => $amount)
                {{ $amount }},
            @endforeach
        ];
        
        // Création du graphique des paiements
        const ctxPayments = document.getElementById('paymentsChart').getContext('2d');
        const paymentsChart = new Chart(ctxPayments, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Montant des paiements',
                    data: paymentData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barThickness: 16,
                    maxBarThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 11
                        },
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return `Montant: ${new Intl.NumberFormat('fr-FR').format(context.raw)} FCFA`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'k';
                                }
                                return value;
                            },
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            }
        });
        
        // Données pour le graphique de répartition par genre
        const genderData = [{{ $maleStudents }}, {{ $femaleStudents }}];
        const genderLabels = ['Garçons', 'Filles'];
        const genderColors = ['#2563eb', '#ec4899'];
        
        // Création du graphique de répartition par genre
        const ctxGender = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderData,
                    backgroundColor: genderColors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 11
                        },
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const percentage = Math.round((context.raw / ({{ $maleStudents }} + {{ $femaleStudents }})) * 100);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique d'évolution des inscriptions
        const enrollmentTrendCtx = document.getElementById('enrollmentTrendChart').getContext('2d');
        const enrollmentTrendData = @json($enrollmentTrend);
        
        const enrollmentTrendChart = new Chart(enrollmentTrendCtx, {
            type: 'line',
            data: {
                labels: enrollmentTrendData.map(item => item.year),
                datasets: [{
                    label: 'Nombre d\'élèves',
                    data: enrollmentTrendData.map(item => item.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' élèves';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique de projection des revenus
        const revenueProjectionCtx = document.getElementById('revenueProjectionChart').getContext('2d');
        const revenueProjectionData = @json($revenueProjection);
        
        const revenueProjectionChart = new Chart(revenueProjectionCtx, {
            type: 'bar',
            data: {
                labels: revenueProjectionData.map(item => item.month),
                datasets: [{
                    label: 'Projection de revenus',
                    data: revenueProjectionData.map(item => item.amount),
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    borderRadius: 5,
                    barThickness: 20,
                    maxBarThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique de tendance des performances académiques
        const performanceTrendCtx = document.getElementById('performanceTrendChart').getContext('2d');
        const performanceTrendData = @json($performanceTrend);
        
        const performanceTrendChart = new Chart(performanceTrendCtx, {
            type: 'line',
            data: {
                labels: performanceTrendData.map(item => item.period),
                datasets: [
                    {
                        label: 'Moyenne générale',
                        data: performanceTrendData.map(item => item.average),
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true
                    },
                    {
                        label: 'Taux de réussite',
                        data: performanceTrendData.map(item => item.passRate / 5), // Diviser par 5 pour l'échelle
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 11
                            },
                            boxWidth: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label === 'Moyenne générale') {
                                    return context.dataset.label + ': ' + context.parsed.y + '/20';
                                } else {
                                    return context.dataset.label + ': ' + (context.parsed.y * 5) + '%';
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 20,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return value + '/20';
                            }
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        max: 20,
                        position: 'right',
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return (value * 5) + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique des moyennes par période
        const academicCtx = document.getElementById('academicPerformanceChart').getContext('2d');
        new Chart(academicCtx, {
            type: 'line',
            data: {
                labels: ['Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Annuelle'],
                datasets: [
                    {
                        label: 'Moyenne',
                        data: [
                            @if(isset($averagesByPeriod['Trimestre 1'])) {{ $averagesByPeriod['Trimestre 1']['avg'] }} @else 0 @endif,
                            @if(isset($averagesByPeriod['Trimestre 2'])) {{ $averagesByPeriod['Trimestre 2']['avg'] }} @else 0 @endif,
                            @if(isset($averagesByPeriod['Trimestre 3'])) {{ $averagesByPeriod['Trimestre 3']['avg'] }} @else 0 @endif,
                            @if(isset($averagesByPeriod['Annuelle'])) {{ $averagesByPeriod['Annuelle']['avg'] }} @else 0 @endif
                        ],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.1
                    },
                    {
                        label: 'Taux de réussite',
                        data: [
                            @if(isset($averagesByPeriod['Trimestre 1'])) {{ $averagesByPeriod['Trimestre 1']['pass_rate'] }} @else 0 @endif,
                            @if(isset($averagesByPeriod['Trimestre 2'])) {{ $averagesByPeriod['Trimestre 2']['pass_rate'] }} @else 0 @endif,
                            @if(isset($averagesByPeriod['Trimestre 3'])) {{ $averagesByPeriod['Trimestre 3']['pass_rate'] }} @else 0 @endif,
                            @if(isset($averagesByPeriod['Annuelle'])) {{ $averagesByPeriod['Annuelle']['pass_rate'] }} @else 0 @endif
                        ],
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        title: {
                            display: true,
                            text: 'Moyenne /20'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        max: 100,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Taux de réussite (%)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
        
        // Graphique des performances par niveau
        @if(count($performanceByLevel) > 0)
        const levelCtx = document.getElementById('levelPerformanceChart').getContext('2d');
        new Chart(levelCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($performanceByLevel as $levelId => $level)
                        '{{ $level['name'] }}',
                    @endforeach
                ],
                datasets: [
                    {
                        label: 'Moyenne',
                        data: [
                            @foreach($performanceByLevel as $levelId => $level)
                                {{ $level['average'] }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    },
                    {
                        label: 'Taux de réussite (%)',
                        data: [
                            @foreach($performanceByLevel as $levelId => $level)
                                {{ $level['passRate'] }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        title: {
                            display: true,
                            text: 'Moyenne /20'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        max: 100,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Taux de réussite (%)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
