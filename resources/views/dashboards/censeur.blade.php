@extends('layouts.app')
@section('title', 'Tableau de bord — Censeur')
@section('page-title', 'Tableau de bord')
@section('page-subtitle')Bonjour, {{ auth()->user()->name }} — {{ now()->isoFormat('dddd D MMMM YYYY') }}@endsection

@push('styles')
<style>
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
@keyframes barW{from{width:0}to{width:var(--w)}}
.anim-1{animation:fadeUp .4s ease .05s both}.anim-2{animation:fadeUp .4s ease .1s both}
.anim-3{animation:fadeUp .4s ease .15s both}.anim-4{animation:fadeUp .4s ease .2s both}
.bar-h{animation:barW .8s cubic-bezier(.22,.68,0,1.2) .2s both}
.tt-panel { background:#fff; border:1px solid #E5EDF5; box-shadow:0 10px 26px rgba(26,58,107,.055); }
.tt-grid-wrap table th { background:#F8FBFE; color:#334155; }
.tt-grid-wrap table tbody tr:hover td { background-color:#FAFCFE; }
.tt-grid-wrap table td, .tt-grid-wrap table th { border-color:#E8EEF5; }
</style>
@endpush

@section('content')

@if(!$activeYear)
<div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
    <p class="text-amber-700 font-semibold inline-flex items-center justify-center gap-2">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"/><path d="M12 16h.01"/><path d="M5 20h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
        Aucune année scolaire active.
    </p>
</div>
@else

{{-- ── KPI ───────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="anim-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Notes en attente</p>
            <svg class="w-5 h-5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M12 4h9"/><path d="M12 12h9"/><path d="M4 6h.01"/><path d="M4 12h.01"/><path d="M4 18h.01"/></svg>
        </div>
        <p class="text-2xl font-black text-amber-600">{{ $kpiNotesPending }}</p>
        <p class="text-xs text-gray-400 mt-1">classes incomplètes</p>
    </div>
    <div class="anim-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bulletins à générer</p>
            <svg class="w-5 h-5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6"/><path d="M9 16h6"/><path d="M9 8h6"/><rect x="3" y="4" width="18" height="16" rx="2" ry="2"/></svg>
        </div>
        <p class="text-2xl font-black" style="color:#1A3A6B;">{{ $kpiBulletinsToGenerate }}</p>
        <p class="text-xs text-gray-400 mt-1">classes</p>
    </div>
    <div class="anim-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Absences aujourd'hui</p>
            <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8.1A6 6 0 1 0 6 8.1"/><path d="M6 18h12"/></svg>
        </div>
        <p class="text-2xl font-black text-red-500">{{ $kpiAbsencesToday }}h</p>
    </div>
    <div class="anim-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Incidents (ce mois)</p>
            <svg class="w-5 h-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M3 7l9-4 9 4v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        </div>
        <p class="text-2xl font-black" style="color:#92400E;">{{ $kpiIncidentsThisMonth }}</p>
    </div>
</div>

<form method="get" class="mb-5">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="flex-1 min-w-0 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <h3 class="text-sm font-black text-gray-800 mb-2">Filtrer par section</h3>
            <div class="flex gap-2 items-center">
                <select name="section_id" class="min-w-0 flex-1 rounded-xl border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 px-4 py-3 text-sm text-gray-700">
                    <option value="0">Toutes les sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" @selected($section->id == $selectedSectionId)>{{ $section->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-sky-600 text-white text-sm font-bold px-4 py-3 hover:bg-sky-700 transition">Appliquer</button>
            </div>
        </div>
    </div>
</form>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    <a href="{{ route('grades.index') }}" class="group rounded-2xl bg-white shadow-sm border border-gray-100 p-4 flex items-start gap-3 hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3 8-8"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">Suivi des notes</p>
            <p class="text-xs text-gray-500">Voir le tableau de bord des notes.</p>
        </div>
    </a>
    <a href="{{ route('absences.index') }}" class="group rounded-2xl bg-white shadow-sm border border-gray-100 p-4 flex items-start gap-3 hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8.1A6 6 0 1 0 6 8.1"/><path d="M6 18h12"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">Absences</p>
            <p class="text-xs text-gray-500">Consulter les absences enregistrées.</p>
        </div>
    </a>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-black text-sm" style="color:#1A3A6B;">Suivi des notes par classe</h3>
                @if($currentSeq)
                <p class="text-xs text-gray-400 mt-1">{{ $currentSeq->label }} · {{ $classProgress->count() }} classes</p>
                @endif
            </div>
            <a href="{{ route('grades.index') }}" class="inline-flex items-center gap-2 text-xs font-bold hover:underline" style="color:#E87722;">
                <span>Voir tout</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
            </a>
        </div>
        @if($classProgress->isEmpty())
        <div class="px-5 py-10 text-center text-sm text-gray-400 italic">Aucune donnée disponible.</div>
        @else
        <table class="w-full">
            <thead>
                <tr style="background:#F8FAFC;border-bottom:1px solid #E5E7EB;">
                    <th class="text-left px-5 py-2.5 text-xs font-bold text-gray-400 uppercase">Classe</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold text-gray-400 uppercase">Séquence</th>
                    <th class="text-center px-4 py-2.5 text-xs font-bold text-gray-400 uppercase">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($classProgress->take(8) as $row)
                @php
                    if ($row['filled'] <= 0) {
                        $status = ['A Saisir', '#FEE2E2', '#991B1B'];
                    } elseif ($row['filled'] >= $row['total']) {
                        $status = ['Saisie', '#D1FAE5', '#065F46'];
                    } else {
                        $status = ['En cours', '#FEF3E2', '#92400E'];
                    }
                @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-sm font-bold text-gray-800">{{ $row['class']->full_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $currentSeq?->label }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background:{{ $status[1] }};color:{{ $status[2] }};">
                            {{ $status[0] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- ── Absences critiques ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-black text-sm" style="color:#1A3A6B;">Absences critiques</h3>
            <p class="text-xs text-gray-400 mt-0.5">≥ 6h injustifiées (30 jours)</p>
        </div>
        @if($criticalAbsences->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-gray-400 italic">Aucune absence critique.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($criticalAbsences as $row)
            <div class="px-5 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center
                            text-white text-xs font-bold flex-shrink-0" style="background:#991B1B;">
                    {{ strtoupper(substr($row['enrollment']->student->last_name,0,1)
                       .substr($row['enrollment']->student->first_name,0,1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">
                        {{ $row['enrollment']->student->full_name }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $row['enrollment']->classGroup->full_name }} · {{ $row['hours'] }}h
                    </p>
                </div>
                <a href="{{ route('absences.student', $row['enrollment']) }}"
                   class="text-xs font-bold px-2.5 py-1.5 rounded-lg border border-gray-200
                          hover:bg-gray-50 transition-colors flex-shrink-0">
                    Voir
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- ── Progression Bulletins (style barre comme la maquette) ─────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
    <div class="flex items-center justify-between mb-5">
        <h3 class="flex items-center gap-2 font-black text-base" style="color:#1A3A6B;">
            <svg class="w-5 h-5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M10 18h10"/><path d="M6 18h.01"/></svg>
            Bulletins — Avancement {{ $currentSeq?->label ?? '' }}
        </h3>
        <span class="text-xs text-gray-400">Mise à jour : {{ now()->diffForHumans() }}</span>
    </div>
    @if($classProgress->isEmpty())
    <p class="text-sm text-gray-400 italic">Aucune donnée disponible.</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
        @foreach($classProgress as $row)
        @php
            $color = $row['pct'] >= 80 ? '#1A5C2A' : ($row['pct'] >= 50 ? '#E87722' : '#EF4444');
            $displayWidth = $row['pct'] > 0 ? max($row['pct'], 5) : 0;
        @endphp
        <div>
            <div class="flex justify-between items-baseline mb-1.5">
                <span class="text-sm font-bold text-gray-800">{{ $row['class']->full_name }}</span>
                <span class="text-base font-black" style="color:{{ $color }}">{{ $row['pct'] }}%</span>
            </div>
            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="bar-h h-full rounded-full" style="--w:{{ $displayWidth }}%;width:var(--w);min-width:{{ $row['pct'] > 0 ? 12 : 0 }}px;background:linear-gradient(90deg,{{ $color }},{{ $color }}cc);"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ $row['filled'] }}/{{ $row['total'] }} notes saisies</p>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ── Mon emploi du temps personnel ──────────────────────────────────── --}}
<div class="tt-panel overflow-hidden rounded-2xl mb-5">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-black text-sm" style="color:#1A3A6B;">Mon emploi du temps</h3>
        <p class="text-xs text-gray-400 mt-1">Vue hebdomadaire structurée comme le module Emploi du temps.</p>
    </div>
    @if($mySlots->isEmpty())
    <div class="px-5 py-8 text-center text-sm text-gray-400 italic">
        Aucun cours assigné personnellement.
    </div>
    @else
    <div class="tt-grid-wrap overflow-x-auto">
        @include('timetable.partials.grid', [
            'mode' => 'teacher',
            'printable' => false,
            'days' => $days,
            'gridRows' => $gridRows,
            'slots' => $mySlots,
            'conflicts' => collect(),
            'teacherSubjectCount' => $mySlots->pluck('classSubject.subject_id')->unique()->count(),
        ])
    </div>
    @endif
</div>

@endif
@endsection