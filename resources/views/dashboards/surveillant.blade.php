@extends('layouts.app')
@section('title', 'Tableau de bord — Surveillant Général')
@section('page-title', 'Tableau de bord')
@section('page-subtitle')Bonjour, {{ auth()->user()->name }} — {{ now()->isoFormat('dddd D MMMM YYYY') }}@endsection

@section('content')

{{-- ── KPI ───────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Absences aujourd'hui</p>
        <p class="text-2xl font-black text-red-500">{{ $todayHours }}h</p>
        <p class="text-xs text-gray-400 mt-1">{{ $todayStudents }} élève(s)</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Cette semaine</p>
        <p class="text-2xl font-black" style="color:#1A3A6B;">{{ $weekHours }}h</p>
        <p class="text-xs text-red-500 mt-1">{{ $weekUnjustified }}h injustifiées</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Incidents en attente</p>
        <p class="text-2xl font-black text-amber-600">{{ $disciplineStats['pending'] }}</p>
    </div>
    <div class="rounded-2xl p-5 text-white" style="background:#E87722;">
        <p class="text-xs font-bold opacity-80 uppercase tracking-wider mb-2">Renvois (30j)</p>
        <p class="text-2xl font-black">{{ $disciplineStats['suspensions'] }}</p>
    </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <a href="{{ route('absences.index') }}" class="rounded-2xl bg-white shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8.1A6 6 0 1 0 6 8.1"/><path d="M6 18h12"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">Gérer les absences</p>
            <p class="text-xs text-gray-500">Voir toutes les absences.</p>
        </div>
    </a>
    <a href="{{ route('discipline.index') }}" class="rounded-2xl bg-white shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 4v5a7 7 0 0 1-14 0V7z"/><path d="M12 14v7"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">Discipline</p>
            <p class="text-xs text-gray-500">Accéder aux incidents.</p>
        </div>
    </a>
    <a href="{{ route('students.index') }}" class="rounded-2xl bg-white shadow-sm border border-gray-100 p-4 flex items-center gap-3 hover:shadow-md transition">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5.5 20h13"/><path d="M12 3a4 4 0 0 1 4 4v4a4 4 0 0 1-8 0V7a4 4 0 0 1 4-4z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">Annuaire élèves</p>
            <p class="text-xs text-gray-500">Voir les fiches des élèves.</p>
        </div>
    </a>
</div>

{{-- ── ÉLÈVES ABSENTS AUJOURD'HUI ──────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-black text-sm" style="color:#1A3A6B;">
            Élèves absents aujourd'hui
            <span class="text-gray-400 font-normal text-xs ml-1">({{ $absentToday->count() }})</span>
        </h3>
        <a href="{{ route('absences.index') }}" class="text-xs font-bold hover:underline" style="color:#E87722;">
            Voir tout →
        </a>
    </div>
    @if($absentToday->isEmpty())
    <div class="px-5 py-8 text-center text-sm text-gray-400 italic flex flex-col items-center gap-2">
        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
        </div>
        Aucun élève absent aujourd'hui.
    </div>
    @else
    <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
        @foreach($absentToday as $row)
        <a href="{{ route('absences.student', $row['enrollment']) }}"
           class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center
                            text-white text-xs font-bold flex-shrink-0"
                     style="background:{{ $row['enrollment']->student->gender==='M'?'#1D4ED8':'#BE185D' }};">
                    {{ strtoupper(substr($row['enrollment']->student->last_name,0,1)
                       .substr($row['enrollment']->student->first_name,0,1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">
                        {{ $row['enrollment']->student->full_name }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $row['enrollment']->classGroup->full_name }}</p>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-black" style="color:{{ $row['is_justified']?'#1A5C2A':'#EF4444' }}">
                    {{ $row['hours'] }}h
                </p>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                      style="{{ $row['is_justified'] ? 'background:#D1FAE5;color:#065F46;' : 'background:#FEE2E2;color:#991B1B;' }}">
                    {{ $row['is_justified'] ? 'Justifié' : 'Non justifié' }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>

{{-- ── INCIDENTS + TOP ABSENTÉISTES (côte à côte) ─────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Incidents disciplinaires récents --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-black text-sm" style="color:#1A3A6B;">Incidents disciplinaires récents</h3>
            <a href="{{ route('discipline.index') }}" class="text-xs font-bold hover:underline" style="color:#E87722;">Voir tout →</a>
        </div>
        @if($recentIncidents->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-gray-400 italic">Aucun incident récent.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($recentIncidents as $inc)
            <a href="{{ route('discipline.show', $inc) }}"
               class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $inc->studentEnrollment?->student?->full_name }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $inc->studentEnrollment?->classGroup?->full_name }} · {{ $inc->incident_date->format('d/m/Y') }}
                    </p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold" style="background:#FEE2E2;color:#991B1B;">
                    {{ ucfirst($inc->incident_type) }}
                </span>
            </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Top absentéistes 30j --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-black text-sm" style="color:#1A3A6B;">Élèves les plus absents (30j)</h3>
        </div>
        @if($topAbsentees->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-gray-400 italic">Aucune absence récente.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($topAbsentees as $row)
            <a href="{{ route('absences.student', $row['enrollment']) }}"
               class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $row['enrollment']->student->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $row['enrollment']->classGroup->full_name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-black text-amber-600">{{ $row['total_hours'] }}h</p>
                    @if($row['unjustified'] > 0)
                    <p class="text-xs text-red-500">{{ $row['unjustified'] }}h injust.</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection