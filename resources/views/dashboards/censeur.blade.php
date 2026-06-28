@extends('layouts.app')

@section('title', 'Censeur')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Espace Censeur / Préfet des études')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- KPIs — HAUT DE PAGE                                                        --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">

    {{-- KPI 1 : Notes en attente --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Notes en attente
                </p>
                <p class="text-3xl lg:text-4xl font-black mt-2" style="color: #1A3A6B;">
                    12
                </p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background-color: #FEF3C7;">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" style="color: #D97706;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4v2m0 4v2m0-16V3m0 4V1m0 16v2m0 4v2M8.228 4.228l1.414 1.414M19.556 15.556l1.414 1.414M4.228 19.556l1.414-1.414M15.556 8.228l1.414-1.414"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 2 : Bulletins à générer --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Bulletins à générer
                </p>
                <p class="text-3xl lg:text-4xl font-black mt-2" style="color: #1A3A6B;">
                    3
                </p>
                <p class="text-xs text-gray-500 font-medium mt-1">classes</p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background-color: #E0E7FF;">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" style="color: #4F46E5;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 3 : Absences aujourd'hui --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Absences aujourd'hui
                </p>
                <p class="text-3xl lg:text-4xl font-black mt-2 text-red-600">
                    47
                </p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background-color: #FEE2E2;">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-red-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 4 : Incidents (ce mois) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Incidents (ce mois)
                </p>
                <p class="text-3xl lg:text-4xl font-black mt-2 text-amber-600">
                    5
                </p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background-color: #FEF3C7;">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-amber-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SECTION CENTRALE — SUIVI & ALERTES                                         --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">

    {{-- Suivi des notes par classe --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #1A3A6B;">
                Suivi des notes par classe
            </h3>
            <a href="#" class="text-sm font-semibold text-orange-600 hover:text-orange-700">
                Voir tout →
            </a>
        </div>

        {{-- Tableau --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-bold text-gray-700">CLASSE</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700">MATIÈRE</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700">SÉQUENCE</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700">STATUT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- Row 1 --}}
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-semibold text-gray-900">6ème A</td>
                        <td class="py-3 px-4 text-gray-600">Mathématiques</td>
                        <td class="py-3 px-4 text-gray-600">Séquence 3</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background-color: #DCFCE7; color: #166534;">
                                SAISIE
                            </span>
                        </td>
                    </tr>
                    {{-- Row 2 --}}
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-semibold text-gray-900">Terminale C</td>
                        <td class="py-3 px-4 text-gray-600">Philosophie</td>
                        <td class="py-3 px-4 text-gray-600">Séquence 3</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background-color: #DBEAFE; color: #0C4A6E;">
                                VALIDE
                            </span>
                        </td>
                    </tr>
                    {{-- Row 3 --}}
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-semibold text-gray-900">3ème B</td>
                        <td class="py-3 px-4 text-gray-600">Physique-Chimie</td>
                        <td class="py-3 px-4 text-gray-600">Séquence 3</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background-color: #F3F4F6; color: #4B5563;">
                                NON COMMENCÉ
                            </span>
                        </td>
                    </tr>
                    {{-- Row 4 --}}
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-semibold text-gray-900">1ère D</td>
                        <td class="py-3 px-4 text-gray-600">SVT</td>
                        <td class="py-3 px-4 text-gray-600">Séquence 3</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background-color: #DCFCE7; color: #166534;">
                                SAISIE
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Absences critiques --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <h3 class="text-lg font-bold mb-4" style="color: #1A3A6B;">
            Absences critiques
        </h3>

        <div class="space-y-3">
            {{-- Élève 1 --}}
            <div class="border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                             style="background-color: #3B82F6;">
                            EM
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Marc EBOA</p>
                            <p class="text-xs text-gray-500">1ère C • 12 heures</p>
                        </div>
                    </div>
                    <button class="px-4 py-1.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Contacter
                    </button>
                </div>
            </div>

            {{-- Élève 2 --}}
            <div class="border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                             style="background-color: #F97316;">
                            SN
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">SONA Nina</p>
                            <p class="text-xs text-gray-500">3ème B • 9 heures</p>
                        </div>
                    </div>
                    <button class="px-4 py-1.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Contacter
                    </button>
                </div>
            </div>

            {{-- Élève 3 --}}
            <div class="border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                             style="background-color: #1A3A6B;">
                            BK
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">BEKONO Kevin</p>
                            <p class="text-xs text-gray-500">6ème A • 6 heures</p>
                        </div>
                    </div>
                    <button class="px-4 py-1.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Contacter
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- BULLETINS — AVANCEMENT                                                     --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-bold" style="color: #1A3A6B;">
                Bulletins — Avancement Trimestre 2
            </h3>
        </div>
        <p class="text-xs text-gray-500">Mise à jour: Il y a 10 min</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Classe 1 --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-gray-900">6ème A</span>
                <span class="text-sm font-bold" style="color: #EA580C;">85%</span>
            </div>
            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     style="width: 85%; background-color: #EA580C;"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">29/28 matières saisies</p>
        </div>

        {{-- Classe 2 --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-gray-900">Terminale C</span>
                <span class="text-sm font-bold" style="color: #EA580C;">40%</span>
            </div>
            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     style="width: 40%; background-color: #EA580C;"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">8/15 matières saisies</p>
        </div>

        {{-- Classe 3 --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-gray-900">3ème B</span>
                <span class="text-sm font-bold" style="color: #EA580C;">72%</span>
            </div>
            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     style="width: 72%; background-color: #EA580C;"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">18/25 matières saisies</p>
        </div>

        {{-- Classe 4 --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-gray-900">1ère D</span>
                <span class="text-sm font-bold" style="color: #EA580C;">95%</span>
            </div>
            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     style="width: 95%; background-color: #EA580C;"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">23/24 matières saisies</p>
        </div>
    </div>
</div>

@endsection