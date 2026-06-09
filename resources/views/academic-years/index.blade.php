@extends('layouts.app')

@section('title', 'Années Scolaires')
@section('page-title', 'Années Scolaires')
@section('page-subtitle', 'Gestion du calendrier scolaire')

@section('breadcrumb')
    <a href="{{ route('settings.index') }}" class="hover:text-gray-700">
        Paramètres
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium" style="color: #1A3A6B;">Années Scolaires</span>
@endsection

@section('content')

{{-- ── CAS : AUCUNE ANNÉE ───────────────────────────────────────────────────── --}}
@if($years->isEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
    <div class="w-16 h-16 rounded-full flex items-center justify-center
                mx-auto mb-4" style="background-color: #EBF3FB;">
        <svg class="w-8 h-8" style="color: #1A3A6B;" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0
                     00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
    <p class="text-gray-600 font-medium mb-1">
        Aucune année scolaire créée
    </p>
    <p class="text-sm text-gray-400 mb-6">
        Commencez par créer la première année scolaire de l'établissement.
    </p>
    <a href="{{ route('academic-years.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
              text-white font-semibold text-sm"
       style="background-color: #1A3A6B;">
        Créer la première année
    </a>
</div>

@else

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- ANNÉE ACTIVE                                                               --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@if($activeYear)
<div class="bg-white rounded-2xl shadow-sm border border-gray-200
            overflow-hidden mb-4 relative">

    {{-- Bordure gauche bleue --}}
    <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-2xl"
         style="background-color: #1A3A6B;"></div>

    <div class="pl-6 pr-5 py-5">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row sm:items-center
                    justify-between gap-3 mb-5">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold" style="color: #1A3A6B;">
                    {{ str_replace('-', ' – ', $activeYear->label) }}
                </h2>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold
                             tracking-wider"
                      style="background-color: #D4EDDA; color: #1A5C2A;">
                    ACTIVE
                </span>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('academic-years.show', $activeYear) }}"
                   class="px-4 py-2 rounded-lg border text-sm font-medium
                          text-gray-700 border-gray-300 hover:bg-gray-50
                          transition-colors">
                    Voir les détails
                </a>
                <a href="{{ route('academic-years.edit', $activeYear) }}?tab=info"
                   class="px-4 py-2 rounded-lg border text-sm font-medium
                          transition-colors"
                   style="border-color: #1A3A6B; color: #1A3A6B;">
                    Modifier
                </a>
                <form method="POST"
                      action="{{ route('academic-years.close', $activeYear) }}"
                      onsubmit="return confirm(
                          'Clôturer l\'année {{ $activeYear->label }} ?\n'
                          + 'Cette action est irréversible.')">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-semibold
                                   text-white transition-all hover:shadow-md"
                            style="background-color: #E87722;">
                        Clôturer l'année
                    </button>
                </form>
            </div>
        </div>

        {{-- Grille d'informations --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Effectifs --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center
                            justify-center flex-shrink-0"
                     style="background-color: #EBF3FB;">
                    <svg class="w-5 h-5" style="color: #1A3A6B;" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                                 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3
                                 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356
                                 -1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0
                                 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0
                                 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400
                               uppercase tracking-wider mb-1">
                        Effectifs
                    </p>
                    <p class="text-sm font-semibold text-gray-800 leading-snug">
                        {{ number_format($activeYear->student_enrollments_count) }}
                        élèves
                        <span class="text-gray-400 font-normal">|</span>
                        {{ $activeYear->class_groups_count }} classes
                        <span class="text-gray-400 font-normal">|</span>
                        {{ $sectionsCount }} sections
                    </p>
                </div>
            </div>

            {{-- Période --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center
                            justify-center flex-shrink-0"
                     style="background-color: #EBF3FB;">
                    <svg class="w-5 h-5" style="color: #1A3A6B;" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2
                                 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400
                               uppercase tracking-wider mb-1">
                        Période
                    </p>
                    <p class="text-sm text-gray-800 leading-snug">
                        <span class="font-semibold">Début :</span>
                        {{ $activeYear->start_date->format('d M Y') }}
                    </p>
                    <p class="text-sm text-gray-800">
                        <span class="font-semibold">Fin prévue :</span>
                        {{ $activeYear->end_date->format('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- État actuel --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center
                            justify-center flex-shrink-0"
                     style="background-color: #EBF3FB;">
                    <svg class="w-5 h-5" style="color: #1A3A6B;" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0
                                 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0
                                 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2
                                 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2
                                 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400
                               uppercase tracking-wider mb-1">
                        État actuel
                    </p>
                    @if($currentSequence)
                        <p class="text-sm text-gray-600">
                            Séquence en cours :
                        </p>
                        <p class="text-sm font-bold" style="color: #1A3A6B;">
                            {{ $currentSequence->label }}
                            <span class="font-normal text-gray-500">—</span>
                            {{ $currentSequence->trimester->label }}
                        </p>
                    @else
                        <p class="text-sm font-semibold text-green-600">
                            ✓ Toutes les séquences validées
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Barre de progression des séquences --}}
        @php
            $sequences = $activeYear->trimesters->flatMap->sequences;
            $totalSeq  = $sequences->count();
            $lockedSeq = $sequences->where('is_grades_locked', true)->count();
        @endphp
        @if($totalSeq > 0)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 font-medium">
                    Progression des séquences
                </p>
                <p class="text-xs text-gray-500">
                    {{ $lockedSeq }}/{{ $totalSeq }} validées
                </p>
            </div>
            <div class="flex gap-1.5">
                @foreach($sequences as $seq)
                <div class="flex-1 h-2.5 rounded-full
                            {{ $seq->is_grades_locked
                                ? ''
                                : 'bg-gray-200' }}"
                     style="{{ $seq->is_grades_locked
                                ? 'background-color: #1A5C2A;'
                                : '' }}"
                     title="{{ $seq->label }} — {{ $seq->is_grades_locked ? 'Validée' : 'En cours' }}">
                </div>
                @endforeach
            </div>
            <div class="flex mt-1">
                @foreach($sequences as $seq)
                <div class="flex-1 text-center">
                    <span class="text-xs text-gray-400">S{{ $seq->number }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- AUTRES ANNÉES (clôturées / en préparation)                                 --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@foreach($otherYears as $year)
@php
    $isClosed = $year->end_date < now();
@endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100
            overflow-hidden mb-4
            {{ $isClosed ? 'opacity-90' : '' }}">
    <div class="px-6 py-5">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row sm:items-center
                    justify-between gap-3 mb-4">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-xl font-bold text-gray-500">
                    {{ str_replace('-', ' – ', $year->label) }}
                </h2>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold
                             tracking-wider bg-gray-100 text-gray-500">
                    {{ $isClosed ? 'CLÔTURÉE' : 'EN PRÉPARATION' }}
                </span>
                @if($isClosed)
                <span class="flex items-center gap-1 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2
                                 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Lecture seule
                </span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('academic-years.show', $year) }}"
                class="px-4 py-2 rounded-lg border border-gray-300
                        text-sm font-medium text-gray-600
                        hover:bg-gray-50 transition-colors">
                    Voir les détails
                </a>

                @if(!$isClosed)
                    <a href="{{ route('academic-years.edit', $year) }}"
                    class="px-4 py-2 rounded-lg border text-sm font-medium
                            transition-colors"
                    style="border-color: #1A3A6B; color: #1A3A6B;">
                        Modifier
                    </a>

                    <form method="POST"
                        action="{{ route('academic-years.activate', $year) }}"
                        onsubmit="return confirm('Activer l\'année {{ $year->label }} ?')">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-sm font-semibold
                                    text-white transition-all"
                                style="background-color: #1A5C2A;">
                            Activer
                        </button>
                    </form>

                    {{-- Supprimer — uniquement si pas de données --}}
                    @if($year->class_groups_count === 0)
                        <form method="POST"
                            action="{{ route('academic-years.destroy', $year) }}"
                            onsubmit="return confirm(
                                'Supprimer définitivement l\'année {{ $year->label }} ?\n'
                                + 'Cette action est irréversible.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    title="Supprimer"
                                    class="p-2 rounded-lg text-gray-400 hover:text-red-600
                                        hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                            01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1
                                            -1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        {{-- Infos --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Bilan --}}
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center
                            flex-shrink-0 bg-gray-100">
                    <svg class="w-4 h-4 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                                 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3
                                 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356
                                 -1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0
                                 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400
                               uppercase tracking-wider mb-1">
                        {{ $isClosed ? 'Bilan final' : 'Effectifs actuels' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ number_format($year->student_enrollments_count) }}
                        élèves
                        <span class="text-gray-300">|</span>
                        {{ $year->class_groups_count }} classes
                        <span class="text-gray-300">|</span>
                        {{ $sectionsCount }} sections
                    </p>
                </div>
            </div>

            {{-- Période --}}
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center
                            flex-shrink-0 bg-gray-100">
                    <svg class="w-4 h-4 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400
                               uppercase tracking-wider mb-1">
                        {{ $isClosed ? 'Période réelle' : 'Période prévue' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        Début : {{ $year->start_date->format('d M Y') }}
                        <span class="text-gray-300">|</span>
                        Fin : {{ $year->end_date->format('d M Y') }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endforeach

{{-- ── MESSAGE INFO LECTURE SEULE ──────────────────────────────────────────── --}}
@if($otherYears->where('end_date', '<', now())->isNotEmpty())
<div class="flex items-start gap-3 px-5 py-4 rounded-xl mb-6"
     style="background-color: #EBF3FB;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #1A3A6B;"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm italic" style="color: #1A3A6B;">
        Les années clôturées sont consultables en lecture seule uniquement.
    </p>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- STATISTIQUES — CROISSANCE & PERFORMANCE                                    --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@if($years->count() >= 2)
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-2">

    {{-- ── Historique des croissances ──────────────────────────────────── --}}
    <div class="lg:col-span-2 rounded-2xl p-6 text-white"
         style="background-color: #1A3A6B;">
        <h3 class="text-base font-bold mb-1">
            Historique des Croissances
        </h3>
        <p class="text-sm text-white/70 mb-5">
            @if($growthRate !== null)
                L'institution a connu une croissance de
                <span class="font-semibold text-white">
                    {{ abs($growthRate) }}%
                </span>
                {{ $growthRate >= 0 ? '(hausse)' : '(baisse)' }}
                du nombre d'inscriptions sur les dernières années.
            @else
                Évolution du nombre d'élèves inscrits par année scolaire.
            @endif
        </p>

        {{-- Graphique barres CSS --}}
        @php
            $maxCount = $growthData->max('count') ?: 1;
        @endphp
        <div class="flex items-end gap-3 h-28">
            @foreach($growthData as $data)
            @php
                $height = ($data['count'] / $maxCount) * 100;
                $isLast = $loop->last;
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <span class="text-xs text-white/60 font-medium">
                    {{ $data['count'] > 0 ? $data['count'] : '—' }}
                </span>
                <div class="w-full rounded-t-lg transition-all"
                     style="height: {{ max($height, 5) }}%;
                            background-color: {{ $isLast ? '#E87722' : 'rgba(255,255,255,0.25)' }};">
                </div>
                <span class="text-xs text-white/50 text-center leading-tight">
                    {{ substr($data['label'], 0, 9) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Moyenne générale ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6
                flex flex-col justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase
                       tracking-wider mb-3">
                Moyenne Générale
            </p>
            @if($overallAverage)
                <p class="text-4xl font-black mb-1"
                   style="color: #E87722;">
                    {{ $overallAverage }}<span class="text-2xl">/20</span>
                </p>
                <p class="text-sm text-gray-500">
                    Performance globale de l'année en cours
                    @if($lastLockedSequence)
                        basée sur les évaluations jusqu'à la
                        {{ $lastLockedSequence->label }}.
                    @endif
                </p>

                {{-- Barre de progression --}}
                @php
                    $pct = min(($overallAverage / 20) * 100, 100);
                @endphp
                <div class="mt-4 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all"
                         style="width: {{ $pct }}%;
                                background-color: #E87722;">
                    </div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-gray-400">0</span>
                    <span class="text-xs text-gray-400">20</span>
                </div>
            @else
                <p class="text-3xl font-black text-gray-200 mb-1">—</p>
                <p class="text-sm text-gray-400">
                    Aucune note saisie pour l'instant.
                </p>
            @endif
        </div>

        @if($activeYear)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Année en cours</span>
                <span class="font-semibold" style="color: #1A3A6B;">
                    {{ $activeYear->label }}
                </span>
            </div>
        </div>
        @endif
    </div>

</div>
@endif

@endif {{-- fin du @if($years->isEmpty()) --}}

{{-- ── FLOATING ACTION BUTTON (FAB) — RESPONSIVE ──────────────────────────── --}}
<a href="{{ route('academic-years.create') }}"
   class="group fixed bottom-3 right-3 sm:bottom-4 sm:right-4 md:bottom-5 md:right-5 lg:bottom-6 lg:right-6
          z-50 flex items-center justify-center gap-1.5 sm:gap-2
          w-12 h-12 sm:w-13 sm:h-13 md:w-14 md:h-14 lg:w-16 lg:h-16
          rounded-full shadow-lg transition-all duration-300
          hover:w-auto hover:px-3 sm:hover:px-4 md:hover:px-5 lg:hover:px-6
          hover:pr-3 sm:hover:pr-4 md:hover:pr-5 lg:hover:pr-6
          text-white font-semibold text-xs sm:text-xs md:text-sm lg:text-sm
          hover:shadow-xl hover:scale-105 active:scale-95"
   style="background-color: #E87722;">
    
    <!-- Icône + seule (affichée par défaut) -->
    <svg class="w-5 h-5 sm:w-5 sm:h-5 md:w-6 md:h-6 lg:w-7 lg:h-7
                group-hover:hidden transition-all duration-300" 
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2.5" d="M12 4v16m8-8H4"/>
    </svg>
    
    <!-- Texte + icône (affichée au survol) -->
    <span class="hidden group-hover:flex items-center gap-1 sm:gap-1.5 md:gap-2 lg:gap-2
                 transition-all duration-300 whitespace-nowrap">
        <svg class="w-4 h-4 sm:w-4 sm:h-4 md:w-5 md:h-5 lg:w-6 lg:h-6"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="text-xs sm:text-xs md:text-sm lg:text-sm">
            Nouvelle année scolaire
        </span>
    </span>
</a>

@endsection