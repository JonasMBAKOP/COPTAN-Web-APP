<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'COPTAN') — Gestion Scolaire</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Scrollbar sidebar */
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 2px;
        }
    </style>
</head>
<body class="h-full bg-gray-50" x-data>

    {{-- ── OVERLAY MOBILE ───────────────────────────────────────────── --}}
    <div id="sidebar-overlay"
         onclick="toggleSidebar()"
         class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden">
    </div>

    <div class="flex h-full min-h-screen">

        {{-- ── SIDEBAR ──────────────────────────────────────────────── --}}
        @include('layouts.partials.sidebar')

        {{-- ── CONTENU PRINCIPAL ───────────────────────────────────── --}}
        <div class="flex-1 flex flex-col min-w-0 lg:ml-0">

            {{-- Navbar --}}
            @include('layouts.partials.navbar')

            {{-- Fil d'Ariane (optionnel) --}}
            @hasSection('breadcrumb')
            <div class="px-4 lg:px-6 pt-4">
                <nav class="flex items-center gap-2 text-sm text-gray-500">
                    @yield('breadcrumb')
                </nav>
            </div>
            @endif

            {{-- Contenu de la page --}}
            <main class="flex-1 px-4 lg:px-6 py-4 lg:py-6 overflow-auto">
                {{-- Messages flash --}}
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200
                            rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200
                            rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
                @endif

                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="px-4 lg:px-6 py-3 border-t border-gray-200 bg-white">
                <p class="text-xs text-gray-400 text-center">
                    COPTAN © {{ date('Y') }} — Plateforme de Gestion Scolaire
                </p>
            </footer>
            @stack('fixed_footer')

        </div>
    </div>

    {{-- Script sidebar responsive --}}
    <script>
        function toggleSidebar() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebar-overlay');
            const isOpen   = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }
        }

        // Fermer sidebar si on redimensionne vers desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar-overlay').classList.add('hidden');
            }
        });
    </script>

</body>
</html>