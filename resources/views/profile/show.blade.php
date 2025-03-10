<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paramètres du profil') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- En-tête du profil -->
            <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
                <div class="flex items-center space-x-8">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0">
                            <img class="h-32 w-32 rounded-full border-4 border-gray-200 object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                        </div>
                    @endif
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                        <p class="text-gray-500">{{ Auth::user()->email }}</p>
                        <div class="mt-4">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille des sections -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations du profil -->
                <div class="bg-white shadow-xl rounded-lg divide-y divide-gray-200">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 flex items-center">
                            <x-icons name="users" class="w-5 h-5 mr-2" />
                            {{ __('Informations personnelles') }}
                        </h2>
                        @livewire('profile.update-profile-information-form')
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="bg-white shadow-xl rounded-lg divide-y divide-gray-200">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 flex items-center">
                            <x-icons name="settings" class="w-5 h-5 mr-2" />
                            {{ __('Sécurité') }}
                        </h2>
                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                            @livewire('profile.update-password-form')
                        @endif
                    </div>
                </div>

                <!-- Sessions du navigateur -->
                <div class="bg-white shadow-xl rounded-lg divide-y divide-gray-200">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 flex items-center">
                            <x-icons name="menu" class="w-5 h-5 mr-2" />
                            {{ __('Sessions du navigateur') }}
                        </h2>
                        @livewire('profile.logout-other-browser-sessions-form')
                    </div>
                </div>

                <!-- Suppression du compte -->
                @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                    <div class="bg-white shadow-xl rounded-lg divide-y divide-gray-200">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                                <x-icons name="error" class="w-5 h-5 mr-2 text-red-500" />
                                {{ __('Suppression du compte') }}
                            </h2>
                            @livewire('profile.delete-user-form')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
