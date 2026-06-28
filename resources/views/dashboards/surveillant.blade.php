@extends('layouts.app')

@section('title', 'Surveillant')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Espace Surveillant Général')

@section('content')
{{-- Header avec greeting --}}
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-1" style="color: #1A3A6B;">
        Bonjour, {{ auth()->user()->name }}
    </h1>
    <p class="text-gray-600">Vous l'état de la surveillance pour aujourd'hui.</p>
</div>

{{-- KPIs (4 cartes) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- KPI 1: Appels effectués --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <svg class="w-5 h-5" style="color: #1A3A6B;" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2 3h6v6H2V3zm8 0h6v6h-6V3zm8 0h6v6h-6V3zM2 11h6v6H2v-6zm8 0h6v6h-6v-6zm8 0h6v6h-6v-6zM2 19h6v6H2v-6zm8 0h6v6h-6v-6zm8 0h6v6h-6v-6z"/>
            </svg>
            <p class="text-2xl font-bold" style="color: #1A3A6B;">8/24</p>
        </div>
        <p class="text-sm text-gray-600">Appels effectués aujourd'hui</p>
    </div>

    {{-- KPI 2: Élèves absents --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <svg class="w-5 h-5" style="color: #E87722;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-2xl font-bold" style="color: #E87722;">23</p>
        </div>
        <p class="text-sm text-gray-600">Élèves absents</p>
        <p class="text-xs text-gray-500 mt-1">La priorité aujourd'hui</p>
    </div>

    {{-- KPI 3: Retards --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <svg class="w-5 h-5" style="color: #1A3A6B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-2xl font-bold" style="color: #1A3A6B;">7</p>
        </div>
        <p class="text-sm text-gray-600">Retards</p>
        <p class="text-xs text-gray-500 mt-1">Manquoir visible</p>
    </div>

    {{-- KPI 4: Incidents signalés --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <svg class="w-5 h-5" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0-6a6 6 0 11-6-6 6 6 0 016 6z"/>
            </svg>
            <p class="text-2xl font-bold text-red-600">2</p>
        </div>
        <p class="text-sm text-gray-600">Incidents signalés</p>
        <p class="text-xs text-gray-500 mt-1">Autres mesures</p>
    </div>
</div>

{{-- Main content grid: Tableau + Sidebar --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Tableau des Appels --}}
    <div class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold" style="color: #1A3A6B;">Appels du jour — Avancement</h2>
            <button class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Heure</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Classe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Enseignant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        {{-- Ligne 1 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">07:30</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">6ème A</td>
                            <td class="px-4 py-3 text-sm text-gray-600">M. FOUDA</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-green-700" style="background-color: #D4EDDA;">
                                    <svg class="inline h-4 w-4 mr-1 align-[-2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Fait
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </td>
                        </tr>

                        {{-- Ligne 2 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">08:30</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">Terminale D1</td>
                            <td class="px-4 py-3 text-sm text-gray-600">Mme. NGONO</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #FFF3CD; color: #856404;">
                                    En cours...
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </td>
                        </tr>

                        {{-- Ligne 3 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">09:30</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">4ème Espagnol</td>
                            <td class="px-4 py-3 text-sm text-gray-600">M. ATANGSANA</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-600" style="background-color: #F5F5F5;">
                                    À faire
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #A85A2D; color: white;">FAIRE L'APPEL</button>
                            </td>
                        </tr>

                        {{-- Ligne 4 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">09:30</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">1ère C</td>
                            <td class="px-4 py-3 text-sm text-gray-600">M. MBARGA</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-600" style="background-color: #F5F5F5;">
                                    À faire
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #A85A2D; color: white;">FAIRE L'APPEL</button>
                            </td>
                        </tr>

                        {{-- Ligne 5 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">10:45</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">5ème B</td>
                            <td class="px-4 py-3 text-sm text-gray-600">Mme. CHOTTU</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-600" style="background-color: #F5F5F5;">
                                    À faire
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #A85A2D; color: white;">FAIRE L'APPEL</button>
                            </td>
                        </tr>

                        {{-- Ligne 6 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">11:45</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">2nde C</td>
                            <td class="px-4 py-3 text-sm text-gray-600">M. TCHATCOVIA</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-600" style="background-color: #F5F5F5;">
                                    À faire
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #A85A2D; color: white;">FAIRE L'APPEL</button>
                            </td>
                        </tr>

                        {{-- Ligne 7 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">13:30</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">3ème AA</td>
                            <td class="px-4 py-3 text-sm text-gray-600">Mme. BIYA</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-600" style="background-color: #F5F5F5;">
                                    À faire
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #A85A2D; color: white;">FAIRE L'APPEL</button>
                            </td>
                        </tr>

                        {{-- Ligne 8 --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">14:30</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">Terminale A4</td>
                            <td class="px-4 py-3 text-sm text-gray-600">M. KENFACK</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-600" style="background-color: #F5F5F5;">
                                    À faire
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button class="px-3 py-1 rounded text-xs font-semibold" style="background-color: #A85A2D; color: white;">FAIRE L'APPEL</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar: Actions rapides et Élèves absents --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Actions rapides --}}
        <div>
            <h3 class="text-lg font-bold mb-4" style="color: #1A3A6B;">Actions rapides</h3>
            <div class="space-y-3">
                <button class="w-full py-3 px-4 rounded-lg font-semibold text-white flex items-center justify-center gap-2" style="background-color: #A85A2D;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Enregistrer un appel
                </button>

                <button class="w-full py-3 px-4 rounded-lg font-semibold text-red-600 border-2 border-red-600 flex items-center justify-center gap-2 hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0-6a6 6 0 11-6-6 6 6 0 016 6z"/>
                    </svg>
                    Signaler un incident
                </button>

                <button class="w-full py-3 px-4 rounded-lg font-semibold text-gray-700 border-2 border-gray-300 flex items-center justify-center gap-2 hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948-.684l1.498-4.493a1 1 0 011.502-.684l1.498 4.493a1 1 0 00.948.684H17a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                    </svg>
                    Contacter un parent
                </button>
            </div>
        </div>

        {{-- Élèves absents --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold" style="color: #1A3A6B;">Élèves absents</h3>
                <a href="#" class="text-sm font-medium" style="color: #E87722;">Voir tout</a>
            </div>

            <div class="space-y-3">
                {{-- Absent 1 --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: #1A3A6B;">
                            AB
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">ABENA Boris</p>
                            <p class="text-xs text-gray-500">Depuis 02:15</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                </div>

                {{-- Absent 2 --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: #E87722;">
                            EL
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">EBOLLO Laure</p>
                            <p class="text-xs text-gray-500">Depuis 01:30</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                    </svg>
                </div>

                {{-- Absent 3 --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: #C8A415;">
                            MK
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">MUKAM Mbert</p>
                            <p class="text-xs text-gray-500">Depuis 03:45</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                    </svg>
                </div>

                {{-- Absent 4 --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: #1A3A6B;">
                            NT
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">NTSAMA Thérice</p>
                            <p class="text-xs text-gray-500">Depuis 00:30</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                    </svg>
                </div>

                {{-- Absent 5 --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: #E87722;">
                            ZD
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">ZAMBO Dandaou</p>
                            <p class="text-xs text-gray-500">Depuis 04:15</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                </div>

                {{-- Absent 6 --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: #1A3A6B;">
                            ON
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">ONANA Naniane</p>
                            <p class="text-xs text-gray-500">Depuis 01:10</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection