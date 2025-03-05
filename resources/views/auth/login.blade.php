<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <div class="text-center">
                    <h1 class="text-3xl font-poppins-semibold text-indigo-600">LYNCOSC</h1>
                    <p class="text-gray-500 text-sm mt-1">Système de Gestion Scolaire</p>
                </div>
            </div>

            <h2 class="text-2xl font-poppins-medium text-center text-gray-800 mb-6">Connexion</h2>

            <x-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div>
                    <x-label for="email" value="{{ __('Email') }}" class="font-poppins-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-input id="email" class="block mt-1 w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Mot de passe') }}" class="font-poppins-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-input id="password" class="block mt-1 w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" type="password" name="password" required autocomplete="current-password" />
                    </div>
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" class="text-indigo-600" />
                        <span class="ms-2 text-sm text-gray-600 font-poppins">{{ __('Rester connecté') }}</span>
                    </label>
                </div>

                <div class="flex flex-col items-center justify-center mt-6 space-y-4">
                    <button type="submit" id="login-button" class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-sign-in-alt mr-2"></i> {{ __('Connexion') }}
                    </button>
                    
                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-800 font-poppins transition-colors duration-200" href="{{ route('password.request') }}">
                            {{ __('Mot de passe oublié ?') }}
                        </a>
                    @endif
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} LYNCOSC - Tous droits réservés
            </div>
            
            <!-- Ajout d'un script de débogage -->
            <div id="debug-info" class="mt-4 p-3 bg-gray-100 rounded-lg text-xs text-gray-700">
                Débogage: Page de connexion chargée
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé');
            const form = document.getElementById('login-form');
            const debugInfo = document.getElementById('debug-info');
            
            if (form) {
                console.log('Formulaire trouvé');
                form.addEventListener('submit', function(e) {
                    console.log('Formulaire soumis');
                    debugInfo.innerHTML += '<br>Formulaire soumis!';
                });
            } else {
                console.error('Formulaire non trouvé');
                debugInfo.innerHTML += '<br>ERREUR: Formulaire non trouvé';
            }
        });
    </script>
</x-guest-layout>
