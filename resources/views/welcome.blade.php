<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="LYNCOSC - Système de Gestion Scolaire Intégré">

        <title>LYNCOSC - Gestion Scolaire</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }
            .login-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 2rem;
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
            }
            .login-card {
                background: white;
                border-radius: 1rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                width: 100%;
                display: flex;
                flex-direction: row;
            }
            .login-image {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 3rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: white;
                width: 50%;
            }
            .login-form {
                padding: 3rem;
                width: 50%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .logo {
                font-size: 2rem;
                font-weight: 700;
                margin-bottom: 1rem;
                color: white;
            }
            .tagline {
                font-size: 1.2rem;
                margin-bottom: 2rem;
                text-align: center;
            }
            .auth-buttons {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                margin-top: 2rem;
            }
            .auth-button {
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                font-weight: 500;
                transition: all 0.3s ease;
                text-align: center;
            }
            .login-button {
                background-color: #4f46e5;
                color: white;
            }
            .login-button:hover {
                background-color: #4338ca;
            }
            .register-button {
                border: 1px solid #4f46e5;
                color: #4f46e5;
            }
            .register-button:hover {
                background-color: #f9fafb;
            }
            
            @media (max-width: 768px) {
                .login-card {
                    flex-direction: column;
                }
                .login-image, .login-form {
                    width: 100%;
                    padding: 2rem;
                }
            }
        </style>
    </head>
    <body class="font-poppins antialiased">
        <div class="login-container">
            <div class="login-card">
                <div class="login-image">
                    <div class="logo">LYNCOSC</div>
                    <div class="tagline">Système de Gestion Scolaire Intégré</div>
                    <img src="https://via.placeholder.com/300x200?text=LYNCOSC" alt="LYNCOSC" class="w-full max-w-xs mt-4">
                    <p class="mt-8 text-center text-white text-opacity-90">
                        Une solution complète pour la gestion de votre établissement scolaire
                    </p>
                </div>
                
                <div class="login-form">
                    <h1 class="text-3xl font-poppins-semibold text-gray-800 mb-6">Bienvenue</h1>
                    <p class="text-gray-600 mb-8">
                        Connectez-vous pour accéder à votre tableau de bord et gérer votre établissement.
                    </p>
                    
                    <div class="auth-buttons">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="auth-button login-button">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Accéder au tableau de bord
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="auth-button login-button">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Connexion
                                </a>
                            @endauth
                        @endif
                    </div>
                    
                    <div class="mt-12 text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} LYNCOSC - Tous droits réservés
                    </div>
                </div>
            </div>
        </div>
    </body>
</html> 