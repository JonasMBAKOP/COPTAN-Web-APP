@extends('layouts.app')

@section('title', 'Modifier ' . $academicYear->label)
@section('page-title')Modifier l'année @endsection
@section('page-subtitle'){{ $academicYear->label }}@endsection

@section('breadcrumb')
    <a href="{{ route('academic-years.index') }}" class="hover:text-gray-700">
        Années scolaires
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('academic-years.show', $academicYear) }}"
       class="hover:text-gray-700">
        {{ $academicYear->label }}
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium" style="color: #1A3A6B;">Modifier</span>
@endsection

@section('content')

{{-- FORMULAIRE UNIQUE ──────────────────────────────────────────────────────── --}}
<form method="POST"
      action="{{ route('academic-years.update-all', $academicYear) }}">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ── Informations de l'année ──────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Libellé <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label"
                               value="{{ old('label', $academicYear->label) }}"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm
                                      font-mono font-semibold focus:outline-none
                                      focus:ring-2 focus:ring-blue-200
                                      @error('label') border-red-400
                                      @else border-gray-200 @enderror"
                               style="color: #1A3A6B;">
                        @error('label')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date de début <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date',
                                   $academicYear->start_date->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none
                                      focus:ring-2 focus:ring-blue-200
                                      @error('start_date') border-red-400 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date de fin <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date"
                               value="{{ old('end_date',
                                   $academicYear->end_date->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 border border-gray-200
                                      rounded-lg text-sm focus:outline-none
                                      focus:ring-2 focus:ring-blue-200
                                      @error('end_date') border-red-400 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ── Dates des séquences (trimestres auto-calculés) ────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-4 pb-2
                            border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider
                                   text-gray-400">
                            Dates des séquences
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Les dates des trimestres sont calculées automatiquement
                            depuis celles de leurs séquences.
                        </p>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach($academicYear->trimesters->sortBy('number') as $trimester)
                    @php
                        $seqs = $trimester->sequences->sortBy('number');
                    @endphp

                    {{-- Trimestre --}}
                    <div class="border border-gray-100 rounded-xl overflow-hidden">

                        {{-- En-tête trimestre (auto-calculé, lecture seule) --}}
                        <div class="px-4 py-3 flex items-center gap-3"
                             style="background-color: #F0F4F8;">
                            <div class="w-8 h-8 rounded-lg flex items-center
                                        justify-center text-white font-bold text-sm
                                        flex-shrink-0"
                                 style="background-color: #1A3A6B;">
                                T{{ $trimester->number }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold"
                                   style="color: #1A3A6B;">
                                    {{ $trimester->label }}
                                </p>
                            </div>
                            {{-- Dates auto-calculées --}}
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955
                                             11.955 0 0112 2.944a11.955 11.955 0
                                             01-8.618 3.04A12.02 12.02 0 003 9c0
                                             5.591 3.824 10.29 9 11.622 5.176-1.332
                                             9-6.03 9-11.622 0-1.042-.133-2.052
                                             -.382-3.016z"/>
                                </svg>
                                <span>
                                    Auto :
                                    {{ $trimester->start_date
                                        ? $trimester->start_date->format('d/m/Y')
                                        : '—' }}
                                    →
                                    {{ $trimester->end_date
                                        ? $trimester->end_date->format('d/m/Y')
                                        : '—' }}
                                </span>
                            </div>
                        </div>

                        {{-- Séquences --}}
                        <div class="divide-y divide-gray-50">
                            @foreach($seqs as $seq)
                            <div class="px-4 py-3 flex flex-col sm:flex-row
                                        sm:items-center gap-3">
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <div class="w-7 h-7 rounded-full flex items-center
                                                justify-center text-xs font-semibold
                                                {{ $seq->is_grades_locked
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-gray-100 text-gray-600' }}">
                                        {{ $seq->number }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700
                                                 min-w-0 w-24">
                                        {{ $seq->label }}
                                    </span>
                                    @if($seq->is_grades_locked)
                                    <span class="text-xs text-green-600 flex
                                                 items-center gap-0.5">
                                        <svg class="w-3 h-3" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2
                                                     2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0
                                                     002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Verrouillée
                                    </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 flex-1">
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-400 mb-1">
                                            Début
                                        </label>
                                        <input type="date"
                                               name="sequences[{{ $seq->id }}][start]"
                                               value="{{ $seq->start_date?->format('Y-m-d') }}"
                                               class="w-full px-2.5 py-2 border
                                                      border-gray-200 rounded-lg
                                                      text-xs focus:outline-none
                                                      focus:ring-2 focus:ring-blue-200
                                                      {{ $seq->is_grades_locked
                                                          ? 'bg-gray-50 text-gray-400'
                                                          : 'bg-white' }}"
                                               {{ $seq->is_grades_locked ? 'readonly' : '' }}>
                                    </div>
                                    <div class="flex-shrink-0 pt-4 text-gray-300">→</div>
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-400 mb-1">
                                            Fin
                                        </label>
                                        <input type="date"
                                               name="sequences[{{ $seq->id }}][end]"
                                               value="{{ $seq->end_date?->format('Y-m-d') }}"
                                               class="w-full px-2.5 py-2 border
                                                      border-gray-200 rounded-lg
                                                      text-xs focus:outline-none
                                                      focus:ring-2 focus:ring-blue-200
                                                      {{ $seq->is_grades_locked
                                                          ? 'bg-gray-50 text-gray-400'
                                                          : 'bg-white' }}"
                                               {{ $seq->is_grades_locked ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Colonne droite — Info + Bouton unique --}}
        <div class="space-y-4">

            {{-- Info année --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wider
                           text-gray-400 mb-4 pb-2 border-b border-gray-100">
                    Résumé
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Statut</span>
                        <span class="font-medium
                                     {{ $academicYear->is_active
                                         ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $academicYear->is_active ? 'Active' : 'En préparation' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Trimestres</span>
                        <span class="font-medium text-gray-700">
                            {{ $academicYear->trimesters->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Séquences</span>
                        <span class="font-medium text-gray-700">
                            {{ $academicYear->trimesters
                                ->flatMap->sequences->count() }}
                        </span>
                    </div>
                    @php
                        $locked = $academicYear->trimesters
                            ->flatMap->sequences
                            ->where('is_grades_locked', true)->count();
                    @endphp
                    <div class="flex justify-between">
                        <span class="text-gray-500">Séquences validées</span>
                        <span class="font-medium text-green-600">
                            {{ $locked }} / {{ $academicYear->trimesters->flatMap->sequences->count() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Note sur les dates auto --}}
            <div class="p-4 rounded-xl text-sm"
                 style="background-color: #EBF3FB;">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5"
                         style="color: #1A3A6B;"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0
                                 9 9 0 0118 0z"/>
                    </svg>
                    <div style="color: #1A3A6B;">
                        <p class="font-semibold mb-1">Dates automatiques</p>
                        <p>Les dates des trimestres sont calculées automatiquement :
                        début = 1ère séquence, fin = dernière séquence.</p>
                    </div>
                </div>
            </div>

            {{-- NOTE : séquences verrouillées --}}
            @php
                $hasLocked = $academicYear->trimesters
                    ->flatMap->sequences
                    ->where('is_grades_locked', true)->isNotEmpty();
            @endphp
            @if($hasLocked)
            <div class="p-4 rounded-xl text-sm bg-amber-50 border
                        border-amber-200">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502
                                 -1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333
                                 -3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-amber-700">
                        Les séquences verrouillées
                        <strong>ne peuvent plus</strong>
                        avoir leurs dates modifiées.
                    </p>
                </div>
            </div>
            @endif

            {{-- Bouton unique de sauvegarde ─────────────────────────────── --}}
            <button type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-bold
                           text-sm flex items-center justify-center gap-2
                           shadow-sm transition-all hover:shadow-md
                           hover:translate-y-[-1px]"
                    style="background-color: #1A5C2A;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002
                             -2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Enregistrer toutes les modifications
            </button>

            <a href="{{ route('academic-years.show', $academicYear) }}"
               class="block w-full py-2.5 rounded-xl text-center text-sm
                      font-medium text-gray-600 border border-gray-200
                      hover:bg-gray-50 transition-colors">
                Annuler
            </a>
        </div>

    </div>
</form>

@endsection