@extends('layouts.app')

@section('title', $academicYear->label)
@section('page-title')
    Année Scolaire &rightarrow; {{ $academicYear->label }}
@endsection
@section('page-subtitle')
    Du {{ $academicYear->start_date->format('d/m/Y') }}
    au {{ $academicYear->end_date->format('d/m/Y') }}
@endsection
{{-- 
@section('breadcrumb')
    <a href="{{ route('academic-years.index') }}" class="hover:text-gray-700">
        Années scolaires
    </a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="font-medium" style="color: #1A3A6B;">
        {{ $academicYear->label }}
    </span>
@endsection --}}

@section('content')

{{-- ── EN-TÊTE ───────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden mb-6
            {{ $academicYear->is_active ? 'border-green-200' : 'border-gray-100' }}">
    @if($academicYear->is_active)
    <div class="h-1.5" style="background-color: #1A5C2A;"></div>
    @endif

    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center
                justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap mb-1">
                <h2 class="text-2xl font-bold" style="color: #1A3A6B;">
                    {{ str_replace('-', ' – ', $academicYear->label) }}
                </h2>
                @if($academicYear->is_active)
                    <span class="flex items-center gap-1.5 px-3 py-1 rounded-full
                                 text-xs font-bold bg-green-100 text-green-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500
                                     animate-pulse"></span>
                        ACTIVE
                    </span>
                @elseif($academicYear->isClosed())
                    <span class="flex items-center gap-1.5 px-3 py-1 rounded-full
                                 text-xs font-bold bg-gray-100 text-gray-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0
                                     00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm
                                     10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        CLÔTURÉE — Lecture seule
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                 bg-orange-100 text-orange-700">
                        EN PRÉPARATION
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-500">
                {{ $academicYear->start_date->format('d M Y') }}
                →
                {{ $academicYear->end_date->format('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(!$academicYear->isClosed())
            <a href="{{ route('academic-years.edit', $academicYear) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm
                      font-semibold text-white transition-all"
               style="background-color: #1A3A6B;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5
                             0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Modifier
            </a>
            @endif
            @if(!$academicYear->is_active && !$academicYear->isClosed()
                && $stats['classes'] === 0)
                <form method="POST"
                    action="{{ route('academic-years.destroy', $academicYear) }}"
                    onsubmit="return confirm(
                        'Supprimer définitivement {{ $academicYear->label }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm
                                font-medium text-red-600 border border-red-200
                                hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                    01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1
                                    -1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </form>
            @endif
            <a href="{{ route('academic-years.index') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm
                      font-medium text-gray-600 border border-gray-200
                      hover:bg-gray-50">
                ← Retour
            </a>
        </div>
    </div>
</div>

{{-- ── STATISTIQUES RAPIDES ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['label' => 'Classes',  'value' => $stats['classes'],
         'icon' => '#1A3A6B', 'bg' => '#EBF3FB'],
        ['label' => 'Élèves actifs', 'value' => $stats['students'],
         'icon' => '#1A5C2A', 'bg' => '#EAF5EA'],
        ['label' => 'Notes saisies', 'value' => number_format($stats['grades']),
         'icon' => '#C8A415', 'bg' => '#FBF5E6'],
    ] as $stat)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5
                flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center
                    flex-shrink-0"
             style="background-color: {{ $stat['bg'] }};">
            <span class="text-xl font-black"
                  style="color: {{ $stat['icon'] }};">
                {{ $stat['value'] }}
            </span>
        </div>
        <div>
            <p class="text-2xl font-bold" style="color: {{ $stat['icon'] }};">
                {{ $stat['value'] }}
            </p>
            <p class="text-xs text-gray-400 uppercase tracking-wider">
                {{ $stat['label'] }}
            </p>
        </div>
    </div>
    @endforeach
</div>

{{-- ── CALENDRIER — TRIMESTRES & SÉQUENCES ─────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center
                justify-between">
        <h3 class="font-semibold" style="color: #1A3A6B;">
            Calendrier des séquences
        </h3>
        <span class="text-xs text-gray-400">
            {{ $academicYear->trimesters->flatMap->sequences->count() }}
            séquences au total
        </span>
    </div>

    <div class="divide-y divide-gray-50">
        @foreach($academicYear->trimesters as $trimester)
            @php
                $seqs        = $trimester->sequences->sortBy('number');
                $lockedCount = $seqs->where('is_grades_locked', true)->count();
            @endphp
            <div class="px-6 py-4">
                {{-- En-tête trimestre --}}
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center
                                text-white font-bold text-sm flex-shrink-0"
                        style="background-color: #1A3A6B;">
                        T{{ $trimester->number }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="font-semibold text-sm"
                                style="color: #1A3A6B;">
                                {{ $trimester->label }}
                            </span>
                            @if($trimester->start_date && $trimester->end_date)
                            <span class="text-xs text-gray-400">
                                {{ $trimester->start_date->format('d M Y') }}
                                →
                                {{ $trimester->end_date->format('d M Y') }}
                            </span>
                            @else
                            <span class="text-xs text-gray-300 italic">
                                Dates non définies
                            </span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ $lockedCount === $seqs->count()
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-gray-100 text-gray-500' }}">
                                {{ $lockedCount }}/{{ $seqs->count() }} validées
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Les dates du trimestre correspondent aux dates de ses séquences.
                        </p>
                    </div>
                </div>

                {{-- Séquences --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 ml-12">
                    @foreach($seqs as $seq)
                        <div class="flex items-center gap-3 p-3 rounded-xl
                                    {{ $seq->is_grades_locked
                                        ? 'bg-green-50 border border-green-200'
                                        : 'bg-gray-50 border border-gray-200' }}">
                            <div class="w-7 h-7 rounded-full flex items-center
                                        justify-center text-xs font-bold flex-shrink-0
                                        {{ $seq->is_grades_locked
                                            ? 'bg-green-500 text-white'
                                            : 'bg-gray-300 text-gray-600' }}">
                                {{ $seq->number }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $seq->label }}
                                </p>
                                @if($seq->start_date && $seq->end_date)
                                    <p class="text-xs text-gray-400">
                                        {{ $seq->start_date->format('d M') }}
                                        →
                                        {{ $seq->end_date->format('d M Y') }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-300 italic">Dates non définies</p>
                                @endif
                            </div>

                            {{-- Bouton lock/unlock — uniquement pour l'année active --}}
                            @if($academicYear->is_active)
                                <form method="POST"
                                    action="{{ route('academic-years.sequences.toggle-lock', $seq) }}"
                                    onsubmit="return confirm(
                                        '{{ $seq->is_grades_locked ? 'Déverrouiller' : 'Verrouiller' }}'
                                        + ' {{ $seq->label }} pour toutes les classes ?')">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            title="{{ $seq->is_grades_locked
                                                ? 'Déverrouiller les notes'
                                                : 'Verrouiller les notes' }}"
                                            class="p-1.5 rounded-lg transition-colors flex-shrink-0
                                                {{ $seq->is_grades_locked
                                                    ? 'text-green-600 hover:bg-green-100'
                                                    : 'text-gray-400 hover:bg-gray-200 hover:text-gray-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            @if($seq->is_grades_locked)
                                                {{-- Cadenas fermé --}}
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2
                                                        2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            @else
                                                {{-- Cadenas ouvert --}}
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2
                                                        2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            @endif
                                        </svg>
                                    </button>
                                </form>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── MODULES À VENIR ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="font-semibold text-gray-400 text-sm uppercase tracking-wider mb-4">
        Données associées (disponibles au fur et à mesure)
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Classes & Sections', 'icon' => 'classes'],
            ['label' => 'Élèves & Inscriptions', 'icon' => 'students'],
            ['label' => 'Notes & Bulletins', 'icon' => 'grades'],
            ['label' => 'Finances', 'icon' => 'finances'],
        ] as $item)
        <div class="flex items-center gap-2 p-3 rounded-xl bg-gray-50
                    border border-dashed border-gray-200">
            <span class="text-lg">{{ $item['icon'] }}</span>
            <span class="text-xs text-gray-400">{{ $item['label'] }}</span>
        </div>
        @endforeach
    </div>
</div>

@endsection