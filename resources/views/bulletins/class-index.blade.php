@extends('layouts.app')

@section('title', 'Bulletins — ' . $classGroup->full_name . ' — ' . $sequence->label)
@section('page-title', 'Bulletins ' . $sequence->label)
@section('page-subtitle', $classGroup->full_name . ' · ' . $bulletins->count() . ' bulletin(s) généré(s)')

@section('content')

{{-- ── BARRE D'ACTIONS ──────────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <a href="{{ route('bulletins.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour aux bulletins
    </a>

    <div class="flex items-center gap-2">
        @can('generate-bulletins')
        <form method="POST" action="{{ route('bulletins.generate') }}" class="inline">
            @csrf
            <input type="hidden" name="class_group_id" value="{{ $classGroup->id }}">
            <input type="hidden" name="sequence_id" value="{{ $sequence->id }}">
            <button type="submit"
                    onclick="return confirm('Régénérer et écraser les bulletins existants ?')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border border-yellow-300 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Régénérer
            </button>
        </form>
        @endcan

        <a href="{{ route('bulletins.print-all', ['classGroup' => $classGroup->id, 'sequence' => $sequence->id]) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all"
           style="background:#1A3A6B;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimer tous
        </a>
    </div>
</div>

{{-- ── TABLEAU DES BULLETINS ────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
        <div>
            <h2 class="font-black text-sm" style="color:#1A3A6B;">
                {{ $classGroup->full_name }} — {{ $sequence->label }}
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">
                Trimestre {{ $sequence->trimester?->number ?? '—' }} ·
                {{ $classGroup->level?->section?->name }}
            </p>
        </div>
        <span class="text-xs font-bold px-3 py-1 rounded-full"
              style="background:rgba(26,58,107,0.07); color:#1A3A6B;">
            {{ $bulletins->count() }} bulletin(s)
        </span>
    </div>

    @if($bulletins->isEmpty())
    <div class="py-16 text-center">
        <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-400 font-semibold">Aucun bulletin généré pour cette séquence.</p>
        @can('generate-bulletins')
        <form method="POST" action="{{ route('bulletins.generate') }}" class="mt-4 inline">
            @csrf
            <input type="hidden" name="class_group_id" value="{{ $classGroup->id }}">
            <input type="hidden" name="sequence_id" value="{{ $sequence->id }}">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all"
                    style="background:#1A3A6B;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Générer les bulletins
            </button>
        </form>
        @endcan
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-gray-100 text-gray-400 uppercase text-3xs font-black tracking-wider"
                    style="background:#F8FAFC;">
                    <th class="text-center px-4 py-3.5 w-12">Rang</th>
                    <th class="text-left px-5 py-3.5">Élève</th>
                    <th class="text-center px-4 py-3.5">Moy. Générale</th>
                    <th class="text-center px-4 py-3.5">Appréciation</th>
                    <th class="text-center px-4 py-3.5">Absences (h)</th>
                    <th class="text-center px-4 py-3.5">Statut</th>
                    <th class="text-center px-6 py-3.5">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($bulletins as $b)
                @php
                    $avg  = (float) $b->average_general;
                    $bgColor = $avg >= 10 ? 'rgba(26,92,42,0.07)' : 'rgba(239,68,68,0.07)';
                    $txtColor= $avg >= 10 ? '#1A5C2A' : '#EF4444';
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors font-semibold text-gray-700">
                    {{-- Rang --}}
                    <td class="px-4 py-4 text-center">
                        @if($b->rank <= 3)
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-black text-white"
                              style="background: {{ $b->rank == 1 ? '#C8A415' : ($b->rank == 2 ? '#9CA3AF' : '#CD7C32') }};">
                            {{ $b->rank }}
                        </span>
                        @else
                        <span class="text-sm font-black text-gray-500">{{ $b->rank ?? '—' }}</span>
                        @endif
                    </td>

                    {{-- Élève --}}
                    <td class="px-5 py-4">
                        <p class="font-bold text-gray-800">
                            {{ $b->studentEnrollment->student->full_name }}
                        </p>
                        <p class="text-3xs text-gray-400">
                            {{ $b->studentEnrollment->student->matricule }}
                        </p>
                    </td>

                    {{-- Moyenne --}}
                    <td class="px-4 py-4 text-center">
                        @if($b->average_general !== null)
                        <span class="inline-block px-3 py-1 rounded-lg text-sm font-black"
                              style="background: {{ $bgColor }}; color: {{ $txtColor }};">
                            {{ number_format($b->average_general, 2) }}/20
                        </span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>

                    {{-- Appréciation --}}
                    <td class="px-4 py-4 text-center">
                        <span class="text-xs font-bold text-gray-600">
                            {{ $b->general_observation ?? '—' }}
                        </span>
                    </td>

                    {{-- Absences --}}
                    <td class="px-4 py-4 text-center">
                        @php $abs = ($b->unjustified_absences ?? 0) + ($b->justified_absences ?? 0); @endphp
                        <span class="{{ $abs > 0 ? 'text-red-500 font-bold' : 'text-gray-400' }}">
                            {{ $abs > 0 ? number_format($abs, 1) . 'h' : '0' }}
                        </span>
                    </td>

                    {{-- Statut (publié / brouillon) --}}
                    <td class="px-4 py-4 text-center">
                        @if($b->is_published)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-3xs font-bold"
                              style="background:rgba(26,92,42,0.1); color:#1A5C2A;">
                            <span class="w-1.5 h-1.5 rounded-full" style="background:#1A5C2A;"></span>
                            Publié
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-3xs font-bold bg-gray-100 text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Brouillon
                        </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('bulletins.show', $b->id) }}"
                               target="_blank"
                               title="Aperçu / Imprimer"
                               class="p-1.5 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            {{-- Footer stats --}}
            <tfoot>
                <tr class="border-t-2 border-gray-100 text-xs font-bold text-gray-600" style="background:#F8FAFC;">
                    <td colspan="2" class="px-5 py-3.5">Statistiques de la classe</td>
                    <td class="px-4 py-3.5 text-center" style="color:#1A3A6B;">
                        Moy. classe : {{ $bulletins->whereNotNull('average_general')->avg('average_general') ? number_format($bulletins->whereNotNull('average_general')->avg('average_general'), 2) : '—' }}/20
                    </td>
                    <td colspan="4" class="px-4 py-3.5 text-gray-400 font-medium">
                        Effectif : {{ $bulletins->count() }} ·
                        Admis (≥10) : {{ $bulletins->where('average_general', '>=', 10)->count() }} ·
                        En difficulté (<10) : {{ $bulletins->where('average_general', '<', 10)->count() }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

@endsection
