@extends('layouts.app')

@section('title', 'Enseignant')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Espace Enseignant')

@section('content')
{{-- Header avec greeting --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2" style="color: #1A3A6B;">
        Bonsoir, {{ auth()->user()->name }} — Année scolaire 2024–2025
    </h1>
    <p class="text-gray-600">Vous enseignez 3 matières dans 5 classes</p>
</div>

{{-- KPIs (3 cartes) --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 mb-8">
    {{-- KPI 1: Matières --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-2">Matières</p>
        <p class="text-3xl font-bold" style="color: #1A3A6B;">03</p>
    </div>

    {{-- KPI 2: Classes --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-2">Classes</p>
        <p class="text-3xl font-bold" style="color: #1A3A6B;">05</p>
    </div>

    {{-- KPI 3: Élèves Totaux --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-2">Élèves Totaux</p>
        <p class="text-3xl font-bold" style="color: #1A3A6B;">164</p>
    </div>
</div>

{{-- Section: Mes Classes --}}
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold" style="color: #1A3A6B;">Mes Classes</h2>
        <a href="#" class="text-sm font-medium" style="color: #E87722;">Voir tout →</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Classe 1: 4ème A --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-bold" style="color: #1A3A6B;">4ème A</h3>
                    <p class="text-sm text-gray-600">Mathématiques —</p>
                    <p class="text-xs text-gray-500">Coef. 4</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                32 élèves
            </div>
            <div class="mb-4 pb-4 border-t border-gray-100">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium"
                      style="background-color: #FBE8D6; color: #A85A2D;">Séquence 3 : Non saisie</span>
            </div>
            <button class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                Saisir les notes
            </button>
        </div>

        {{-- Classe 2: 3ème B --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-bold" style="color: #1A3A6B;">3ème B</h3>
                    <p class="text-sm text-gray-600">Mathématiques —</p>
                    <p class="text-xs text-gray-500">Coef. 5</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                40 élèves
            </div>
            <div class="mb-4 pb-4 border-t border-gray-100">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium"
                      style="background-color: #D4EDDA; color: #155724;">Séquence 2 : Validée</span>
            </div>
            <button class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                Voir les notes
            </button>
        </div>

        {{-- Classe 3: Terminale C --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-bold" style="color: #1A3A6B;">Terminale C</h3>
                    <p class="text-sm text-gray-600">Mathématiques —</p>
                    <p class="text-xs text-gray-500">Coef. 6</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                28 élèves
            </div>
            <div class="mb-4 pb-4 border-t border-gray-100">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium"
                      style="background-color: #FBE8D6; color: #A85A2D;">Séquence 3 : Non saisie</span>
            </div>
            <button class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                Saisir les notes
            </button>
        </div>

        {{-- Classe 4: 5ème D --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-bold" style="color: #1A3A6B;">5ème D</h3>
                    <p class="text-sm text-gray-600">Géométrie —</p>
                    <p class="text-xs text-gray-500">Coef. 2</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                35 élèves
            </div>
            <div class="mb-4 pb-4 border-t border-gray-100">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium"
                      style="background-color: #D4EDDA; color: #155724;">Séquence 2 : Validée</span>
            </div>
            <button class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                Voir les notes
            </button>
        </div>
    </div>
</div>

{{-- Section: Mon emploi du temps cette semaine --}}
<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold" style="color: #1A3A6B;">Mon emploi du temps cette semaine</h2>
        <div class="flex gap-2">
            <button class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Heure</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Lundi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Mardi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Mercredi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Jeudi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Vendredi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 08:00 - 10:00 --}}
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-6 text-sm text-gray-600 font-medium">08:00 – 10:00</td>
                        <td class="px-4 py-6">
                            <div class="bg-orange-50 rounded-lg p-3 border-l-4" style="border-color: #E87722;">
                                <p class="text-sm font-semibold text-gray-800">4ème A</p>
                                <p class="text-xs text-gray-600">Maths - 5:00</p>
                            </div>
                        </td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6">
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4" style="border-color: #1A3A6B;">
                                <p class="text-sm font-semibold text-gray-800">4ème A</p>
                                <p class="text-xs text-gray-600">Maths - 3:00</p>
                            </div>
                        </td>
                    </tr>

                    {{-- 10:00 - 12:00 --}}
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-6 text-sm text-gray-600 font-medium">10:00 – 12:00</td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6">
                            <div class="bg-green-50 rounded-lg p-3 border-l-4" style="border-color: #22C55E;">
                                <p class="text-sm font-semibold text-gray-800">Terminale C</p>
                                <p class="text-xs text-gray-600">Maths - 3:30</p>
                            </div>
                        </td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6">
                            <div class="bg-gray-100 rounded-lg p-3 border-l-4" style="border-color: #9CA3AF;">
                                <p class="text-sm font-semibold text-gray-800">5ème D</p>
                                <p class="text-xs text-gray-600">Maths - 3:30</p>
                            </div>
                        </td>
                    </tr>

                    {{-- 12:00 - 13:00 --}}
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-6 text-sm text-gray-600 font-medium">12:00 – 13:00</td>
                        <td class="px-4 py-6" colspan="5">
                            <p class="text-center text-sm text-gray-500 py-2">PAUSE DÉJEUNER</p>
                        </td>
                    </tr>

                    {{-- 13:00 - 15:00 --}}
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-6 text-sm text-gray-600 font-medium">13:00 – 15:00</td>
                        <td class="px-4 py-6">
                            <div class="bg-orange-50 rounded-lg p-3 border-l-4" style="border-color: #E87722;">
                                <p class="text-sm font-semibold text-gray-800">3ème B</p>
                                <p class="text-xs text-gray-600">Maths - 5:00</p>
                            </div>
                        </td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6">
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4" style="border-color: #1A3A6B;">
                                <p class="text-sm font-semibold text-gray-800">5ème D</p>
                                <p class="text-xs text-gray-600">Maths - 5:30</p>
                            </div>
                        </td>
                        <td class="px-4 py-6"></td>
                        <td class="px-4 py-6">
                            <div class="bg-green-50 rounded-lg p-3 border-l-4" style="border-color: #22C55E;">
                                <p class="text-sm font-semibold text-gray-800">Terminale C</p>
                                <p class="text-xs text-gray-600">Maths - 4:00</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- FAB (Floating Action Button) --}}
<div class="fixed bottom-3 right-3 sm:bottom-4 sm:right-4 md:bottom-5 md:right-5 lg:bottom-6 lg:right-6 z-40">
    <button class="w-12 h-12 sm:w-13 sm:h-13 md:w-14 md:h-14 lg:w-16 lg:h-16 rounded-full shadow-lg flex items-center justify-center text-white transition-transform hover:scale-110"
            style="background-color: #E87722;">
        <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </button>
</div>

@endsection