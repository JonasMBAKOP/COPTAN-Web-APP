@extends('layouts.app')

@section('title', 'Directeur')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de l\'établissement')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- KPIs — HAUT DE PAGE                                                        --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">

    {{-- KPI 1 : Total Élèves --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Total Élèves
                </p>
                <p class="text-3xl lg:text-4xl font-black mt-2" style="color: #1A3A6B;">
                    847
                </p>
                <p class="text-xs text-green-600 font-semibold mt-1">↑ 3% ce mois</p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background-color: #EBF3FB;">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" style="color: #1A3A6B;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 2 : Classes actives --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Classes actives
                </p>
                <p class="text-3xl lg:text-4xl font-black mt-2 text-emerald-600">
                    24
                </p>
                <p class="text-xs text-gray-500 font-medium mt-1">Toutes sections confondues</p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-emerald-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 3 : Frais collectés --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Frais collectés
                </p>
                <p class="text-2xl lg:text-3xl font-black mt-2" style="color: #C8A415;">
                    3 450 000
                </p>
                <p class="text-xs font-semibold mt-1" style="color: #C8A415;">↑ 60% de l'objectif</p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background-color: #FBF5E6;">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" style="color: #C8A415;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- KPI 4 : Taux de réussite --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs lg:text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Taux de réussite
                </p>
                <p class="text-3xl lg:text-4xl font-black mt-2 text-purple-600">
                    72%
                </p>
                <p class="text-xs text-gray-500 font-medium mt-1">Dernier examen approuvé</p>
            </div>
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 lg:w-7 lg:h-7 text-purple-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SECTION CENTRALE — GRAPHIQUES                                              --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">

    {{-- Graphique : Résultats par classe --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #1A3A6B;">
                Résultats par classe — Trimestre 1
            </h3>
            <select class="px-3 py-1.5 text-xs font-medium border border-gray-200 rounded-lg
                          text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <option>Toutes va classes</option>
            </select>
        </div>
        
        {{-- Graphique barres (CSS) --}}
        <div class="flex items-end justify-between gap-2 h-40 mt-6">
            @foreach(['6ème', '5ème', '4ème', '3ème', '2ndA', '1ère', 'Tle'] as $index => $class)
            @php
                $heights = [65, 72, 58, 68, 62, 75, 70];
                $colors = ['#1A3A6B', '#1A3A6B', '#E87722', '#1A3A6B', '#1A3A6B', '#1A3A6B', '#1A3A6B'];
            @endphp
            <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full rounded-t-xl transition-all"
                     style="height: {{ $heights[$index] }}%; background-color: {{ $colors[$index] }};">
                </div>
                <span class="text-xs text-gray-600 font-medium">{{ $class }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Graphique : Répartition des élèves (Donut) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <h3 class="text-lg font-bold mb-6" style="color: #1A3A6B;">
            Répartition des élèves
        </h3>

        {{-- Donut avec SVG --}}
        <div class="flex justify-center mb-6">
            <svg class="w-32 h-32" viewBox="0 0 120 120">
                {{-- Background circle --}}
                <circle cx="60" cy="60" r="45" fill="none" stroke="#E5E7EB" stroke-width="20"/>
                
                {{-- Francophone Général (50%) - Bleu --}}
                <circle cx="60" cy="60" r="45" fill="none" stroke="#2563EB" stroke-width="20"
                        stroke-dasharray="141.3 282.6" stroke-dashoffset="0"
                        transform="rotate(-90 60 60)"/>
                
                {{-- Technique (30%) - Orange --}}
                <circle cx="60" cy="60" r="45" fill="none" stroke="#EA580C" stroke-width="20"
                        stroke-dasharray="84.78 282.6" stroke-dashoffset="-141.3"
                        transform="rotate(-90 60 60)"/>
                
                {{-- Anglophone (20%) - Dark Blue --}}
                <circle cx="60" cy="60" r="45" fill="none" stroke="#1A3A6B" stroke-width="20"
                        stroke-dasharray="56.52 282.6" stroke-dashoffset="-226.08"
                        transform="rotate(-90 60 60)"/>
                
                {{-- Centre text --}}
                <text x="60" y="60" text-anchor="middle" dy="-8" class="text-lg font-black" fill="#1A3A6B">
                    847
                </text>
                <text x="60" y="60" text-anchor="middle" dy="12" class="text-xs font-semibold fill-gray-600">
                    TOTAL
                </text>
            </svg>
        </div>

        {{-- Légende --}}
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full" style="background-color: #2563EB;"></div>
                <span class="text-sm text-gray-700">Francophone Général</span>
                <span class="text-sm font-bold text-gray-900 ml-auto">50%</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full" style="background-color: #EA580C;"></div>
                <span class="text-sm text-gray-700">Technique</span>
                <span class="text-sm font-bold text-gray-900 ml-auto">30%</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full" style="background-color: #1A3A6B;"></div>
                <span class="text-sm text-gray-700">Anglophone</span>
                <span class="text-sm font-bold text-gray-900 ml-auto">20%</span>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SECTION BAS — ACTIVITÉS & ALERTES                                          --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">

    {{-- Activités récentes --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #1A3A6B;">
                Activités récentes
            </h3>
            <a href="#" class="text-sm font-semibold text-orange-600 hover:text-orange-700">
                Voir tout
            </a>
        </div>

        <div class="space-y-4">
            {{-- Activité 1 --}}
            <div class="flex gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: #E5E7EB;">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Note saisie en 3ème A1</p>
                    <p class="text-xs text-gray-500 mt-0.5">Il y a 10 minutes par Mimé Evanga</p>
                </div>
            </div>

            {{-- Activité 2 --}}
            <div class="flex gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: #DBEAFE;">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Inscription de l'élève K. Marie</p>
                    <p class="text-xs text-gray-500 mt-0.5">Il y a 45 minutes - Dossier valide</p>
                </div>
            </div>

            {{-- Activité 3 --}}
            <div class="flex gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: #FEF3C7;">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Paiement reçu - 2ème tranche</p>
                    <p class="text-xs text-gray-500 mt-0.5">Aujourd'hui, 09:25 - Reçu #4459</p>
                </div>
            </div>

            {{-- Activité 4 --}}
            <div class="flex gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: #DBEAFE;">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">Réunion de département SVT</p>
                    <p class="text-xs text-gray-500 mt-0.5">Trimestre - Clôt pré-réunite disponible</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertes prioritaires --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold" style="color: #1A3A6B;">
                Alertes prioritaires
            </h3>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                  style="background-color: #FEE2E2; color: #991B1B;">
                3 ACTIONS REQUISES
            </span>
        </div>

        <div class="space-y-3">
            {{-- Alerte 1 : Impayés critiques --}}
            <div class="border-l-4 rounded-lg p-4"
                 style="border-color: #DC2626; background-color: #FEF2F2;">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background-color: #DC2626; color: white;">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-red-900">Impayés critiques (15%)</p>
                        <p class="text-xs text-red-700 mt-1">15 élèves dossiers pour 127 élèves au total expirant.</p>
                        <button class="mt-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors"
                                style="background-color: #DC2626;">
                            Gérer
                        </button>
                    </div>
                </div>
            </div>

            {{-- Alerte 2 : Absences excessives --}}
            <div class="border-l-4 rounded-lg p-4"
                 style="border-color: #EA580C; background-color: #FFF7ED;">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background-color: #EA580C; color: white;">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-orange-900">Absences excessives</p>
                        <p class="text-xs text-orange-700 mt-1">Situation en 12% des absences non justifiées cette semaine.</p>
                        <button class="mt-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors"
                                style="background-color: #EA580C;">
                            Vérifier
                        </button>
                    </div>
                </div>
            </div>

            {{-- Alerte 3 : Notes manquantes --}}
            <div class="border-l-4 rounded-lg p-4"
                 style="border-color: #6366F1; background-color: #F0F4FF;">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background-color: #6366F1; color: white;">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zm3 0a1 1 0 11-2 0 1 1 0 012 0zm3 0a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold" style="color: #4F46E5;">Notes manquantes - SVT 4ème</p>
                        <p class="text-xs" style="color: #4F46E5;">Délai de saisie dépassé pour 3 classes.</p>
                        <button class="mt-2 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors"
                                style="background-color: #6366F1;">
                            Relancer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection