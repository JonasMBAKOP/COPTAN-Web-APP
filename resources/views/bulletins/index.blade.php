@extends('layouts.app')

@section('title', 'Bulletins Scolaires')
@section('page-title', 'Bulletins Scolaires')
@section('page-subtitle', 'Sélectionnez une classe et une séquence pour générer ou consulter les bulletins')

@section('content')

{{-- ── EN-TÊTE & STATS RAPIDES ─────────────────────────────────────────────── --}}
@if($activeYear)
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
        $totalClasses = $sections->flatMap(fn($s) => $s->levels->flatMap(fn($l) => $l->classGroups))->count();
        $totalSequences = $sequences->count();
        $totalBulletins = $bulletinCounts->sum('cnt');
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4"
         style="border-left: 4px solid #1A3A6B;">
        <div class="p-2.5 rounded-xl" style="background:rgba(26,58,107,0.07);">
            <svg class="w-5 h-5" style="color:#1A3A6B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#1A3A6B;">{{ $totalBulletins }}</p>
            <p class="text-xs text-gray-400 font-semibold">Bulletins générés</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4"
         style="border-left: 4px solid #1A5C2A;">
        <div class="p-2.5 rounded-xl" style="background:rgba(26,92,42,0.07);">
            <svg class="w-5 h-5" style="color:#1A5C2A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#1A5C2A;">{{ $totalClasses }}</p>
            <p class="text-xs text-gray-400 font-semibold">Classes actives</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4"
         style="border-left: 4px solid #C8A415;">
        <div class="p-2.5 rounded-xl" style="background:rgba(200,164,21,0.07);">
            <svg class="w-5 h-5" style="color:#C8A415;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#C8A415;">{{ $totalSequences }}</p>
            <p class="text-xs text-gray-400 font-semibold">Séquences</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4"
         style="border-left: 4px solid #6366F1;">
        <div class="p-2.5 rounded-xl" style="background:rgba(99,102,241,0.07);">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black text-indigo-600">{{ $activeYear->label }}</p>
            <p class="text-xs text-gray-400 font-semibold">Année active</p>
        </div>
    </div>
</div>
@endif

{{-- ── SECTIONS & CLASSES ─────────────────────────────────────────────────── --}}
@if($activeYear)
    @forelse($sections as $section)
    <div class="mb-8">
        {{-- En-tête section --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-1 h-6 rounded-full" style="background:#1A3A6B;"></div>
            <h2 class="font-black text-base uppercase tracking-wide" style="color:#1A3A6B;">
                {{ $section->name }}
            </h2>
            <span class="text-xs text-gray-400 font-semibold">
                ({{ $section->levels->flatMap->classGroups->count() }} classe(s))
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($section->levels as $level)
                @foreach($level->classGroups as $classGroup)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-all">
                    {{-- Header Classe --}}
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between"
                         style="background:linear-gradient(135deg,rgba(26,58,107,0.03),rgba(26,58,107,0.07));">
                        <div>
                            <h3 class="font-black text-sm" style="color:#1A3A6B;">
                                {{ $classGroup->full_name }}
                            </h3>
                            <p class="text-xs text-gray-400 font-medium mt-0.5">
                                {{ $classGroup->enrolled ?? 0 }} élève(s) inscrit(s)
                            </p>
                        </div>
                        <div class="p-2 rounded-xl" style="background:rgba(26,58,107,0.06);">
                            <svg class="w-5 h-5" style="color:#1A3A6B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Séquences --}}
                    <div class="divide-y divide-gray-50">
                        @forelse($sequences as $seq)
                        @php
                            $key = $classGroup->id . '_' . $seq->id;
                            $generated = $bulletinCounts->get($key);
                            $count     = $generated?->cnt ?? 0;
                            $isComplete = $count > 0 && $count >= ($classGroup->enrolled ?? 0);
                            $isPartial  = $count > 0 && !$isComplete;
                        @endphp
                        <div class="px-5 py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                {{-- Badge statut --}}
                                @if($isComplete)
                                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#1A5C2A;"></span>
                                    <span class="text-xs font-semibold text-gray-600 truncate">
                                        {{ $seq->label }}
                                        <span class="text-green-600 font-bold ml-1">✓ {{ $count }}/{{ $classGroup->enrolled ?? 0 }}</span>
                                    </span>
                                @elseif($isPartial)
                                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#C8A415;"></span>
                                    <span class="text-xs font-semibold text-gray-600 truncate">
                                        {{ $seq->label }}
                                        <span class="text-yellow-600 font-bold ml-1">{{ $count }}/{{ $classGroup->enrolled ?? 0 }}</span>
                                    </span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-gray-200 flex-shrink-0"></span>
                                    <span class="text-xs font-semibold text-gray-500 truncate">{{ $seq->label }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                {{-- Générer / Régénérer --}}
                                @can('generate-bulletins')
                                <form method="POST" action="{{ route('bulletins.generate') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="class_group_id" value="{{ $classGroup->id }}">
                                    <input type="hidden" name="sequence_id" value="{{ $seq->id }}">
                                    <button type="submit"
                                            title="{{ $count > 0 ? 'Régénérer' : 'Générer' }} les bulletins"
                                            onclick="return confirm('{{ $count > 0 ? 'Régénérer et écraser les bulletins existants ?' : 'Générer les bulletins ?' }}')"
                                            class="p-1.5 rounded-lg border transition-all text-xs font-bold
                                                   {{ $count > 0
                                                       ? 'border-yellow-200 text-yellow-600 bg-yellow-50 hover:bg-yellow-100'
                                                       : 'border-blue-200 text-blue-600 bg-blue-50 hover:bg-blue-100' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="{{ $count > 0
                                                      ? 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'
                                                      : 'M12 4v16m8-8H4' }}"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan

                                {{-- Voir la liste --}}
                                @if($count > 0)
                                <a href="{{ route('bulletins.class', ['classGroup' => $classGroup->id, 'sequence' => $seq->id]) }}"
                                   title="Voir les bulletins"
                                   class="p-1.5 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-800 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                {{-- Imprimer tout --}}
                                <a href="{{ route('bulletins.print-all', ['classGroup' => $classGroup->id, 'sequence' => $seq->id]) }}"
                                   title="Imprimer tous les bulletins"
                                   target="_blank"
                                   class="p-1.5 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="px-5 py-4 text-xs text-gray-400 italic">Aucune séquence configurée.</p>
                        @endforelse
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-400 font-semibold">Aucune classe configurée.</p>
        <p class="text-xs text-gray-400 mt-1">Configurez des classes dans la gestion académique.</p>
    </div>
    @endforelse
@else
<div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-12 text-center">
    <svg class="w-16 h-16 mx-auto mb-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <p class="text-amber-700 font-bold">Aucune année scolaire active.</p>
    <p class="text-xs text-amber-600 mt-1">Activez une année scolaire avant de générer des bulletins.</p>
</div>
@endif

@endsection
