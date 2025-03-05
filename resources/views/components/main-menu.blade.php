@props(['active' => false])

<div x-data="{ open: false }" class="relative">
    <!-- Bouton du menu -->
    <button @click="open = !open" type="button" class="flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
        <x-icons name="menu" class="h-6 w-6" />
    </button>

    <!-- Menu déroulant -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1">
            <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                <x-icons name="dashboard" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                Tableau de bord
            </a>
            
            <a href="{{ route('classes') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                <x-icons name="class" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                Classes
            </a>
            
            <a href="{{ route('niveaux') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                <x-icons name="level" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                Niveaux
            </a>
            
            <a href="{{ route('students') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                <x-icons name="student" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                Élèves
            </a>
            
            <a href="{{ route('inscriptions') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                <x-icons name="add" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                Inscriptions
            </a>
            
            <a href="{{ route('paiements') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                <x-icons name="payment" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                Paiements
            </a>
            
            @if(auth()->user()->role !== 'intendant')
                <a href="{{ route('grades') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <x-icons name="edit" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                    Notes
                </a>
                
                <a href="{{ route('report-cards') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                    <x-icons name="document" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                    Bulletins
                </a>
            @endif
            
            <div class="border-t border-gray-100 my-1"></div>
            
            <a href="{{ route('profile.show') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                <x-icons name="settings" class="mr-3 text-gray-500 group-hover:text-indigo-600" size="sm" />
                Paramètres
            </a>
            
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a href="{{ route('logout') }}" @click.prevent="$root.submit();" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700">
                    <x-icons name="logout" class="mr-3 text-gray-500 group-hover:text-red-600" size="sm" />
                    Déconnexion
                </a>
            </form>
        </div>
    </div>
</div> 