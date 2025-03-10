<div class="relative" x-data="{ open: false, activeTab: 'students' }" @click.away="open = false">
    <div class="relative">
        <input type="text" 
               wire:model.live.debounce.300ms="search"
               @focus="open = true"
               class="w-64 pl-10 pr-4 py-2 bg-white/10 border-1 size-9 text-white border-white/40 placeholder-white/50 rounded-full focus:outline-none focus:ring-0 focus:ring-white"
               placeholder="Rechercher...">
        <div class="absolute left-3 top-2">
            <div wire:loading.remove wire:target="search">
                <x-icons name="search" size="sm" class="text-white/75"/>
            </div>
            <div wire:loading wire:target="search">
                <x-icons name="loading" size="sm" class="text-white/75 animate-spin"/>
            </div>
        </div>
    </div>

    <!-- Résultats de la recherche -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        
        <!-- Bouton fermer -->
        <button @click="open = false" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
            <x-icons name="close" size="sm"/>
        </button>
        
        @if(strlen($search) < 2)
            <div class="p-4 text-gray-500 bg-white/10">
                Entrer au moins 2 caractères...
            </div>
        @else
            <!-- Onglets de navigation -->
            <div class="flex border-b">
                <button @click="activeTab = 'students'"
                        :class="{ 'border-b-2 border-indigo-500 text-indigo-600': activeTab === 'students' }"
                        class="flex-1 py-2 px-4 text-sm font-medium text-gray-600 hover:text-indigo-600">
                    ÉLÈVES
                    @if($results['students']->isNotEmpty())
                        <span class="ml-1 text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded-full">{{ $results['students']->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'classes'"
                        :class="{ 'border-b-2 border-indigo-500 text-indigo-600': activeTab === 'classes' }"
                        class="flex-1 py-2 px-4 text-sm font-medium text-gray-600 hover:text-indigo-600">
                    CLASSES
                    @if($results['classes']->isNotEmpty())
                        <span class="ml-1 text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded-full">{{ $results['classes']->count() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'levels'"
                        :class="{ 'border-b-2 border-indigo-500 text-indigo-600': activeTab === 'levels' }"
                        class="flex-1 py-2 px-4 text-sm font-medium text-gray-600 hover:text-indigo-600">
                    NIVEAUX
                    @if($results['levels']->isNotEmpty())
                        <span class="ml-1 text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded-full">{{ $results['levels']->count() }}</span>
                    @endif
                </button>
            </div>

            <!-- Contenu des onglets -->
            <div class="p-2">
                <!-- Élèves -->
                <div x-show="activeTab === 'students'">
                    @if($results['students']->isEmpty())
                        <div class="p-4 text-gray-500 text-center">
                            Aucun élève trouvé pour "{{ $search }}"
                        </div>
                    @else
                        @foreach($results['students'] as $student)
                            <a href="{{ route('students.details', $student) }}" class="block px-4 py-2 hover:bg-gray-50 rounded-md">
                                <div class="flex items-center">
                                    <x-icons name="student" size="sm" class="mr-2 text-gray-400"/>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $student->nom }} {{ $student->prenom }}</div>
                                        <div class="text-xs text-gray-500">Matricule: {{ $student->matricule }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>

                <!-- Classes -->
                <div x-show="activeTab === 'classes'">
                    @if($results['classes']->isEmpty())
                        <div class="p-4 text-gray-500 text-center">
                            Aucune classe trouvée pour "{{ $search }}"
                        </div>
                    @else
                        @foreach($results['classes'] as $class)
                            <a href="{{ route('classes') }}" class="block px-4 py-2 hover:bg-gray-50 rounded-md">
                                <div class="flex items-center">
                                    <x-icons name="class" size="sm" class="mr-2 text-gray-400"/>
                                    <div class="text-sm font-medium text-gray-900">{{ $class->libelle }}</div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>

                <!-- Niveaux -->
                <div x-show="activeTab === 'levels'">
                    @if($results['levels']->isEmpty())
                        <div class="p-4 text-gray-500 text-center">
                            Aucun niveau trouvé pour "{{ $search }}"
                        </div>
                    @else
                        @foreach($results['levels'] as $level)
                            <a href="{{ route('niveaux') }}" class="block px-4 py-2 hover:bg-gray-50 rounded-md">
                                <div class="flex items-center">
                                    <x-icons name="level" size="sm" class="mr-2 text-gray-400"/>
                                    <div class="text-sm font-medium text-gray-900">{{ $level->libelle }}</div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
