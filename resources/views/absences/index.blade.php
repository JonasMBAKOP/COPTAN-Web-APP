@extends('layouts.app')

@section('title', 'Absences')
@section('page-title', 'Gestion des Absences')
@section('page-subtitle', 'Suivi des absences, présences et justifications')

@section('content')

{{-- ── KPI CARDS ──────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #1A3A6B;">
        <div class="p-2.5 rounded-xl" style="background:rgba(26,58,107,0.07);">
            <svg class="w-5 h-5" style="color:#1A3A6B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#1A3A6B;">
                {{ number_format($totalAbsenceHours, 1) }}h
            </p>
            <p class="text-xs text-gray-400 font-semibold">Total absences</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #EF4444;">
        <div class="p-2.5 rounded-xl" style="background:rgba(239,68,68,0.07);">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black text-red-500">
                {{ number_format($unjustifiedHours, 1) }}h
            </p>
            <p class="text-xs text-gray-400 font-semibold">Non justifiées</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #1A5C2A;">
        <div class="p-2.5 rounded-xl" style="background:rgba(26,92,42,0.07);">
            <svg class="w-5 h-5" style="color:#1A5C2A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#1A5C2A;">
                {{ number_format($justifiedHours, 1) }}h
            </p>
            <p class="text-xs text-gray-400 font-semibold">Justifiées</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #C8A415;">
        <div class="p-2.5 rounded-xl" style="background:rgba(200,164,21,0.07);">
            <svg class="w-5 h-5" style="color:#C8A415;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#C8A415;">
                {{ $totalAbsenceHours > 0 ? round(($justifiedHours / $totalAbsenceHours) * 100) : 0 }}%
            </p>
            <p class="text-xs text-gray-400 font-semibold">Taux justification</p>
        </div>
    </div>

</div>

{{-- ── GRILLE PRINCIPALE ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Colonne gauche : Liste par classe ─────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Classes par section --}}
        @if($activeYear)
            @forelse($sections as $section)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-3"
                     style="background:linear-gradient(135deg,rgba(26,58,107,0.03),rgba(26,58,107,0.07));">
                    <div class="w-1 h-5 rounded-full" style="background:#1A3A6B;"></div>
                    <h3 class="font-black text-sm" style="color:#1A3A6B;">{{ $section->name }}</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($section->levels as $level)
                        @foreach($level->classGroups as $classGroup)
                        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="font-bold text-sm text-gray-800">{{ $classGroup->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $classGroup->enrolled ?? 0 }} élève(s)</p>
                            </div>
                            <a href="{{ route('absences.class', $classGroup->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white hover:opacity-90 transition-all shadow-sm"
                               style="background:#1A3A6B;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Saisir / Voir
                            </a>
                        </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-400">
                Aucune classe configurée.
            </div>
            @endforelse
        @else
        <div class="bg-white rounded-2xl border border-amber-200 p-10 text-center">
            <p class="text-amber-700 font-bold">Aucune année scolaire active.</p>
        </div>
        @endif

        {{-- Absences récentes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                <h3 class="font-black text-sm" style="color:#1A3A6B;">Absences récentes</h3>
                <span class="text-xs text-gray-400 font-semibold">{{ $recentAbsences->count() }} enregistrement(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 text-3xs uppercase font-black tracking-wider"
                            style="background:#F8FAFC;">
                            <th class="text-left px-5 py-3">Élève</th>
                            <th class="text-left px-4 py-3">Classe</th>
                            <th class="text-center px-4 py-3">Date</th>
                            <th class="text-center px-4 py-3">Durée</th>
                            <th class="text-center px-4 py-3">Statut</th>
                            @can('manage-absences')
                            <th class="text-center px-4 py-3">Action</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentAbsences as $abs)
                        <tr class="hover:bg-gray-50/40 transition-colors font-semibold text-gray-700">
                            <td class="px-5 py-3">
                                <p class="font-bold text-gray-800">
                                    {{ $abs->studentEnrollment->student->full_name }}
                                </p>
                                @if($abs->classSubject)
                                <p class="text-3xs text-gray-400">
                                    {{ $abs->classSubject->subject->name }}
                                </p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $abs->studentEnrollment->classGroup->full_name }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $abs->absence_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold" style="color:#1A3A6B;">
                                {{ number_format($abs->hours, 1) }}h
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($abs->is_justified)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-3xs font-bold"
                                      style="background:rgba(26,92,42,0.1);color:#1A5C2A;">
                                    ✓ Justifiée
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-3xs font-bold bg-red-50 text-red-500">
                                    ✗ Non justifiée
                                </span>
                                @endif
                            </td>
                            @can('manage-absences')
                            <td class="px-4 py-3 text-center">
                                <form method="POST"
                                      action="{{ route('absences.justify', $abs->id) }}"
                                      class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_justified"
                                           value="{{ $abs->is_justified ? '0' : '1' }}">
                                    <button type="submit"
                                            title="{{ $abs->is_justified ? 'Retirer justification' : 'Justifier' }}"
                                            class="p-1.5 rounded-lg border transition-all
                                                   {{ $abs->is_justified
                                                       ? 'border-red-100 text-red-400 hover:bg-red-50'
                                                       : 'border-green-100 text-green-600 hover:bg-green-50' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="{{ $abs->is_justified
                                                      ? 'M6 18L18 6M6 6l12 12'
                                                      : 'M5 13l4 4L19 7' }}"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                            @endcan
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400 italic">
                                Aucune absence enregistrée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Colonne droite : Top absentéistes ─────────────────────────────────── --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50">
                <h3 class="font-black text-sm" style="color:#1A3A6B;">Top absentéistes</h3>
                <p class="text-xs text-gray-400 mt-0.5">Élèves avec le plus d'heures d'absence</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topAbsentees as $index => $e)
                @php
                    $totalH = $e->total_hours ?? 0;
                    $unjH   = $e->unjustified_hours ?? 0;
                    $color  = $unjH > 10 ? '#EF4444' : ($unjH > 5 ? '#C8A415' : '#1A5C2A');
                @endphp
                <div class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-xs font-black text-gray-300 w-4 flex-shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-800 truncate">
                                {{ $e->student->full_name }}
                            </p>
                            <p class="text-3xs text-gray-400">
                                {{ $e->classGroup->full_name }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black" style="color:{{ $color }};">
                            {{ number_format($totalH, 1) }}h
                        </p>
                        @if($unjH > 0)
                        <p class="text-3xs text-red-400 font-bold">
                            {{ number_format($unjH, 1) }}h NJ
                        </p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 italic text-sm">
                    Aucune absence enregistrée.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Note info --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-amber-800">Règlement intérieur</p>
                    <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                        Au-delà de <strong>10h d'absences non justifiées</strong>,
                        l'élève doit être convoqué avec ses parents.
                        Au-delà de <strong>30h</strong>, un conseil de discipline peut être tenu.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
