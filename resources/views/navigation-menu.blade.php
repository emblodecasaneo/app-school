<nav x-data="{ open: false, academicOpen: false, adminOpen: false, settingsOpen: false }" class="bg-white border-b border-gray-100 w-full">
    <!-- Primary Navigation Menu -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 bg-indigo-400">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">LYNCOSC</h1>
                    @php
                        $activeYear = \App\Models\SchoolYear::where('active', '1')->first();
                    @endphp
                    @if ($activeYear)
                        <span class="ml-4 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            <x-icons name="calendar" class="mr-1 inline" size="xs" /> Année : {{ $activeYear->school_year }}
                        </span>
                    @endif
                </div>

                <!-- Navigation Links - Desktop -->
                <div class="hidden space-x-4 sm:ml-6 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="flex items-center">
                        <x-icons name="dashboard" class="mr-1" size="sm" /> {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Menu déroulant académique pour desktop -->
                    @php
                        $isAcademicActive = request()->routeIs('niveaux') || request()->routeIs('classes') || 
                                           request()->routeIs('grades') || request()->routeIs('report-cards');
                    @endphp
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 {{ $isAcademicActive ? 'bg-indigo-500 font-extrabold text-sm -tracking-tighter text-white' : 'text-black hover:bg-indigo-500' }} focus:outline-none focus:bg-indigo-500 focus:text-white transition duration-150 ease-in-out rounded-md">
                            <x-icons name="class" class="mr-1" size="sm" /> {{ __('Académique') }}
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             @click.outside="open = false"
                             class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                @if (Auth::user()->role !== 'intendant')
                                    <x-dropdown-link href="{{ route('niveaux') }}" :active="request()->routeIs('niveaux')">
                                        <x-icons name="level" class="mr-1" size="sm" /> {{ __('Niveaux') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('classes') }}" :active="request()->routeIs('classes')">
                                        <x-icons name="class" class="mr-1" size="sm" /> {{ __('Classes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('grades') }}" :active="request()->routeIs('grades')">
                                        <x-icons name="edit" class="mr-1" size="sm" /> {{ __('Notes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('report-cards') }}" :active="request()->routeIs('report-cards')">
                                        <x-icons name="document" class="mr-1" size="sm" /> {{ __('Bulletins') }}
                                    </x-dropdown-link>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Menu déroulant administratif pour desktop -->
                    @php
                        $isAdminActive = request()->routeIs('inscriptions') || request()->routeIs('students') || 
                                        request()->routeIs('paiements');
                    @endphp
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 {{ $isAdminActive ? 'bg-indigo-500 font-extrabold -tracking-tighter text-sm text-white' : 'text-black hover:bg-indigo-500' }} focus:outline-none focus:bg-indigo-500 focus:text-white transition duration-150 ease-in-out rounded-md">
                            <x-icons name="student" class="mr-1 font-bold" size="sm" /> {{ __('Administration') }}
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             @click.outside="open = false"
                             class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <x-dropdown-link href="{{ route('inscriptions') }}" :active="request()->routeIs('inscriptions')">
                                    <x-icons name="add" class="mr-1" size="sm" /> {{ __('Inscriptions') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('students') }}" :active="request()->routeIs('students')">
                                    <x-icons name="student" class="mr-1" size="sm" /> {{ __('Élèves') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('paiements') }}" :active="request()->routeIs('paiements')">
                                    <x-icons name="payment" class="mr-1" size="sm" /> {{ __('Paiements') }}
                                </x-dropdown-link>
                            </div>
                        </div>
                    </div>

                    <!-- Menu déroulant paramètres pour desktop -->
                    @php
                        $isSettingsActive = request()->routeIs('users') || request()->routeIs('student-progress');
                    @endphp
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 {{ $isSettingsActive ? 'bg-indigo-500 text-white' : 'text-black hover:bg-indigo-500' }} focus:outline-none focus:bg-indigo-500 focus:text-white transition duration-150 ease-in-out rounded-md">
                            <x-icons name="settings" class="mr-1" size="sm" /> {{ __('Paramètres') }}
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             @click.outside="open = false"
                             class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <x-dropdown-link href="{{ route('users') }}" :active="request()->routeIs('users')">
                                    <x-icons name="users" class="mr-1" size="sm" /> {{ __('Utilisateurs') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('student-progress') }}" :active="request()->routeIs('student-progress')">
                                    <x-icons name="level" class="mr-1" size="sm" /> {{ __('Progression') }}
                                </x-dropdown-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <!-- Settings Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                <div class="flex items-center">
                                    <x-icons name="settings" class="mr-2" size="sm" /> {{ __('Profile') }}
                                </div>
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    <div class="flex items-center">
                                        <x-icons name="logout" class="mr-2" size="sm" /> {{ __('Log Out') }}
                                    </div>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                    <x-icons name="menu" class="h-6 w-6" />
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu - Mobile -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <x-icons name="dashboard" class="mr-2" size="sm" /> {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <!-- Menu académique pour mobile -->
            <div class="border-l-4 border-transparent pl-4 pr-4 py-2 flex flex-col">
                <div @click="academicOpen = !academicOpen" class="flex items-center justify-between cursor-pointer">
                    <div class="flex items-center">
                        <x-icons name="class" class="mr-2" size="sm" /> 
                        <span class="font-medium text-base text-gray-600">{{ __('Académique') }}</span>
                    </div>
                    <svg :class="{'rotate-180': academicOpen}" class="w-5 h-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                
                <div x-show="academicOpen" class="mt-2 space-y-1 pl-4">
                    @if (Auth::user()->role !== 'intendant')
                        <x-responsive-nav-link href="{{ route('niveaux') }}" :active="request()->routeIs('niveaux')">
                            <x-icons name="level" class="mr-2" size="sm" /> {{ __('Niveaux') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('classes') }}" :active="request()->routeIs('classes')">
                            <x-icons name="class" class="mr-2" size="sm" /> {{ __('Classes') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('grades') }}" :active="request()->routeIs('grades')">
                            <x-icons name="edit" class="mr-2" size="sm" /> {{ __('Notes') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('report-cards') }}" :active="request()->routeIs('report-cards')">
                            <x-icons name="document" class="mr-2" size="sm" /> {{ __('Bulletins') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            </div>
            
            <!-- Menu administratif pour mobile -->
            <div class="border-l-4 border-transparent pl-4 pr-4 py-2 flex flex-col">
                <div @click="adminOpen = !adminOpen" class="flex items-center justify-between cursor-pointer">
                    <div class="flex items-center">
                        <x-icons name="student" class="mr-2" size="sm" /> 
                        <span class="font-medium text-base text-gray-600">{{ __('Administration') }}</span>
                    </div>
                    <svg :class="{'rotate-180': adminOpen}" class="w-5 h-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                
                <div x-show="adminOpen" class="mt-2 space-y-1 pl-4">
                    <x-responsive-nav-link href="{{ route('inscriptions') }}" :active="request()->routeIs('inscriptions')">
                        <x-icons name="add" class="mr-2" size="sm" /> {{ __('Inscriptions') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('students') }}" :active="request()->routeIs('students')">
                        <x-icons name="student" class="mr-2" size="sm" /> {{ __('Élèves') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('paiements') }}" :active="request()->routeIs('paiements')">
                        <x-icons name="payment" class="mr-2" size="sm" /> {{ __('Paiements') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            
            <!-- Menu paramètres pour mobile -->
            <div class="border-l-4 border-transparent pl-4 pr-4 py-2 flex flex-col">
                <div @click="settingsOpen = !settingsOpen" class="flex items-center justify-between cursor-pointer">
                    <div class="flex items-center">
                        <x-icons name="settings" class="mr-2" size="sm" /> 
                        <span class="font-medium text-base text-gray-600">{{ __('Paramètres') }}</span>
                    </div>
                    <svg :class="{'rotate-180': settingsOpen}" class="w-5 h-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                
                <div x-show="settingsOpen" class="mt-2 space-y-1 pl-4">
                    <x-responsive-nav-link href="{{ route('users') }}" :active="request()->routeIs('users')">
                        <x-icons name="users" class="mr-2" size="sm" /> {{ __('Utilisateurs') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('student-progress') }}" :active="request()->routeIs('student-progress')">
                        <x-icons name="level" class="mr-2" size="sm" /> {{ __('Progression') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    <x-icons name="settings" class="mr-2" size="sm" /> {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        <x-icons name="logout" class="mr-2" size="sm" /> {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
