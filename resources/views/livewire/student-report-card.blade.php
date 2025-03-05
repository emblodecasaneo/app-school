<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 md:mb-0">Bulletins de Notes</h2>
            
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 w-full md:w-auto">
                <!-- Filtre par classe -->
                <div class="w-full md:w-64">
                    <label for="selectedClasse" class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                    <select id="selectedClasse" wire:model.live="selectedClasse" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}">{{ $classe->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Recherche d'élève -->
                <div class="w-full md:w-64">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un élève</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" wire:model.live.debounce.300ms="search" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Nom, prénom ou matricule...">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Messages flash -->
        @if (session()->has('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if (session()->has('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6">
            <!-- Liste des élèves -->
            <div class="w-full md:w-1/3 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Liste des élèves</h3>
                
                @if($students->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-gray-500">Aucun élève trouvé</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($students as $student)
                            <div wire:click="selectStudent({{ $student->id }})" class="cursor-pointer p-3 rounded-md hover:bg-indigo-50 transition-colors duration-150 {{ $selectedStudent == $student->id ? 'bg-indigo-100 border-l-4 border-indigo-500' : '' }}">
                                <div class="font-medium">{{ $student->nom }} {{ $student->prenom }}</div>
                                <div class="text-sm text-gray-500">
                                    <span class="inline-block mr-3">
                                        <i class="fas fa-id-card mr-1"></i> {{ $student->matricule }}
                                    </span>
                                    <span class="inline-block">
                                        <i class="fas fa-graduation-cap mr-1"></i> 
                                        @if($student->attributions->isNotEmpty())
                                            {{ $student->attributions->first()->classe->libelle }}
                                        @else
                                            Non inscrit
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
            
            <!-- Bulletin de notes -->
            <div class="w-full md:w-2/3">
                @if($showReportCard)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                        <!-- En-tête du bulletin -->
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-800">{{ $studentData['name'] }}</h3>
                                    <p class="text-gray-600">
                                        <span class="inline-block mr-3">
                                            <i class="fas fa-id-card mr-1"></i> {{ $studentData['matricule'] }}
                                        </span>
                                        <span class="inline-block">
                                            <i class="fas fa-graduation-cap mr-1"></i> {{ $studentData['classe'] }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div class="mt-4 md:mt-0">
                                    <select wire:model.live="selectedPeriod" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        @foreach($periods as $period)
                                            <option value="{{ $period }}">{{ $period }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <button wire:click="calculateAllAverages" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-calculator mr-2"></i> Calculer moyennes
                                    </button>
                                    
                                    <button wire:click="calculateRanks" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <i class="fas fa-sort-numeric-down mr-2"></i> Calculer rangs
                                    </button>
                                    
                                    <button wire:click="printReportCard" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <i class="fas fa-print mr-2"></i> Imprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Corps du bulletin -->
                        <div class="p-4">
                            @if(empty($subjectAverages))
                                <div class="text-center py-8">
                                    <p class="text-gray-500">Aucune note disponible pour cette période</p>
                                </div>
                            @else
                                <!-- Tableau des notes par matière -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    @if($selectedPeriod === 'Annuelle')
                                                        Moyennes trimestrielles
                                                    @else
                                                        Notes
                                                    @endif
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coefficient</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moyenne</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($subjectAverages as $subject => $data)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $subject }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        @if($selectedPeriod === 'Annuelle' && isset($data['trimester_averages']))
                                                            @foreach(['Trimestre 1', 'Trimestre 2', 'Trimestre 3'] as $trimester)
                                                                @if(isset($data['trimester_averages'][$trimester]))
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $data['trimester_averages'][$trimester]['average'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mr-1 mb-1">
                                                                        {{ $trimester }}: {{ $data['trimester_averages'][$trimester]['average'] }}/20
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1 mb-1">
                                                                        {{ $trimester }}: N/A
                                                                    </span>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            @foreach($data['grades'] as $grade)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $grade['value'] >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mr-1 mb-1" title="{{ $grade['type'] }} - {{ $grade['comment'] }}">
                                                                    {{ $grade['value'] }}/20
                                                                    <span class="ml-1 text-xs text-gray-500">({{ $grade['coefficient'] }})</span>
                                                                </span>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['coefficient'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $data['average'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $data['average'] }}/20
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Résumé et statistiques -->
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="text-lg font-medium text-gray-700 mb-3">Résultats</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Moyenne générale:</span>
                                                <span class="font-semibold {{ $generalAverage >= 10 ? 'text-green-600' : 'text-red-600' }}">{{ $generalAverage ?? 'N/A' }}/20</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Rang:</span>
                                                <span class="font-semibold">{{ $rank ?? 'Non classé' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="text-lg font-medium text-gray-700 mb-3">Statistiques de classe</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Moyenne de classe:</span>
                                                <span class="font-semibold">{{ $classAverage ?? 'N/A' }}/20</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Meilleure moyenne:</span>
                                                <span class="font-semibold text-green-600">{{ $highestAverage ?? 'N/A' }}/20</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Moyenne la plus basse:</span>
                                                <span class="font-semibold text-red-600">{{ $lowestAverage ?? 'N/A' }}/20</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Commentaire et décision -->
                                <div class="mt-6">
                                    <h4 class="text-lg font-medium text-gray-700 mb-3">Appréciation et décision</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label for="teacherComment" class="block text-sm font-medium text-gray-700 mb-1">Commentaire du professeur</label>
                                            <textarea id="teacherComment" wire:model="teacherComment" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                        </div>
                                        
                                        <div>
                                            <label for="decision" class="block text-sm font-medium text-gray-700 mb-1">Décision</label>
                                            <select id="decision" wire:model="decision" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <option value="">Sélectionner une décision</option>
                                                <option value="Passage">Passage en classe supérieure</option>
                                                <option value="Redoublement">Redoublement</option>
                                                <option value="Exclusion">Exclusion</option>
                                                <option value="Avertissement">Avertissement</option>
                                            </select>
                                        </div>
                                        
                                        <div class="flex justify-end">
                                            <button wire:click="saveComment" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <i class="fas fa-save mr-2"></i> Enregistrer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-64 bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <i class="fas fa-file-alt text-gray-300 text-5xl mb-3"></i>
                            <p class="text-gray-500">Sélectionnez un élève pour afficher son bulletin</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('print-report-card', () => {
            // Créer une copie du bulletin pour l'impression
            const reportCardContent = document.querySelector('.bg-white.border.border-gray-200.rounded-lg.shadow-sm').cloneNode(true);
            
            // Créer une nouvelle fenêtre pour l'impression
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Bulletin de Notes - LYNCOSC</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <style>
                        body {
                            font-family: 'Poppins', sans-serif;
                            padding: 20px;
                        }
                        .print-header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .print-header h1 {
                            color: #4f46e5;
                            margin-bottom: 5px;
                        }
                        .print-header p {
                            color: #6b7280;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                        }
                        th, td {
                            border: 1px solid #e5e7eb;
                            padding: 8px 12px;
                            text-align: left;
                        }
                        th {
                            background-color: #f9fafb;
                            font-weight: 500;
                        }
                        .text-green-600 {
                            color: #059669;
                        }
                        .text-red-600 {
                            color: #dc2626;
                        }
                        .font-semibold {
                            font-weight: 600;
                        }
                        .hidden-print {
                            display: none;
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h1>LYNCOSC</h1>
                        <p>Système de Gestion Scolaire</p>
                    </div>
                    <div id="report-card-content"></div>
                </body>
                </html>
            `);
            
            // Masquer les boutons dans la version imprimable
            const buttons = reportCardContent.querySelectorAll('button');
            buttons.forEach(button => {
                button.classList.add('hidden-print');
            });
            
            // Ajouter le contenu du bulletin à la fenêtre d'impression
            printWindow.document.getElementById('report-card-content').appendChild(reportCardContent);
            
            // Déclencher l'impression après le chargement de la page
            printWindow.document.addEventListener('DOMContentLoaded', () => {
                printWindow.print();
                printWindow.close();
            });
            
            // Fallback si l'événement DOMContentLoaded ne se déclenche pas
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 1000);
        });
    });
</script> 