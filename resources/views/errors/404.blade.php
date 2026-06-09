<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page introuvable — COPTAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gray-50">
    <div class="text-center max-w-md">

        {{-- Icône --}}
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 rounded-full flex items-center justify-center"
                 style="background-color: #EBF3FB;">
                <svg class="w-12 h-12" style="color: #1A3A6B;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- Code --}}
        <h1 class="text-7xl font-black mb-2" style="color: #1A3A6B;">404</h1>

        {{-- Message --}}
        <h2 class="text-xl font-semibold text-gray-800 mb-3">
            Page introuvable
        </h2>
        <p class="text-gray-500 text-sm mb-6">
            La page que vous recherchez n'existe pas ou a été déplacée.
        </p>

        {{-- Bouton --}}
        <a href="{{ auth()->check() ? auth()->user()->getDashboardRoute() : route('login') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-lg text-white
                  font-semibold text-sm transition-colors"
           style="background-color: #1A3A6B;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1
                         1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1
                         1 0 001 1m-6 0h6"/>
            </svg>
            Retour au tableau de bord
        </a>

        {{-- Logo --}}
        <div class="mt-8 flex justify-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="COPTAN"
                 class="h-10 w-10 object-contain opacity-40">
        </div>

    </div>
</body>
</html>