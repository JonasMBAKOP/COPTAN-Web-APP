@extends('layouts.app')
@section('title', 'Tableau de bord — Surveillant de Secteur')
@section('page-title', 'Tableau de bord')
@section('page-subtitle')Bonjour, {{ auth()->user()->name }} — {{ now()->isoFormat('dddd D MMMM YYYY') }}@endsection

@push('styles')
<style>
@keyframes fadeUp   { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
@keyframes pulseRed { 0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.3)} 50%{box-shadow:0 0 0 8px rgba(239,68,68,0)} }
.anim-1 { animation: fadeUp .4s ease .05s both; }
.anim-2 { animation: fadeUp .4s ease .12s both; }
.anim-3 { animation: fadeUp .4s ease .19s both; }
.anim-4 { animation: fadeUp .4s ease .26s both; }
.anim-5 { animation: fadeUp .4s ease .33s both; }
.anim-6 { animation: fadeUp .4s ease .40s both; }

.kpi-card {
    background: #fff;
    border-radius: 1.25rem;
    border: 1px solid #E5E7EB;
    padding: 1.25rem 1.5rem;
    transition: box-shadow .2s, transform .2s;
}
.kpi-card:hover {
    box-shadow: 0 10px 32px rgba(67,56,202,.10);
    transform: translateY(-3px);
}
.kpi-card__icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: .85rem;
    flex-shrink: 0;
}
.quick-action {
    display: flex; align-items: center; gap: .85rem;
    background: #fff;
    border-radius: 1.1rem;
    border: 1px solid #E5E7EB;
    padding: .9rem 1.1rem;
    transition: box-shadow .2s, transform .2s, border-color .2s;
    text-decoration: none;
}
.quick-action:hover {
    box-shadow: 0 8px 24px rgba(67,56,202,.12);
    border-color: #A5B4FC;
    transform: translateY(-2px);
}
.quick-action__icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.panel {
    background: #fff;
    border-radius: 1.25rem;
    border: 1px solid #E5E7EB;
    overflow: hidden;
}
.panel__header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #F3F4F6;
    display: flex; align-items: center; justify-content: space-between;
}
.panel__header h3 { font-size: .8rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: #3730A3; }
.badge-open    { background: #FEE2E2; color: #991B1B; }
.badge-closed  { background: #D1FAE5; color: #065F46; }
.badge-pending { background: #FEF3C7; color: #92400E; }
.badge-type    { background: #EDE9FE; color: #5B21B6; }
.pulsing-dot   { width: 8px; height: 8px; border-radius: 50%; background: #EF4444; animation: pulseRed 2s infinite; }
</style>
@endpush

@section('content')

{{-- ── HERO BAR ──────────────────────────────────────────────────────────── --}}
<div class="anim-1 rounded-2xl mb-6 overflow-hidden" style="background: linear-gradient(135deg,#3730A3 0%,#4F46E5 50%,#6D28D9 100%);">
    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="pulsing-dot"></div>
                <span class="text-indigo-200 text-xs font-semibold uppercase tracking-widest">Surveillant de Secteur</span>
            </div>
            <h2 class="text-white text-xl font-black">{{ auth()->user()->name }}</h2>
            <p class="text-indigo-200 text-sm mt-0.5">{{ now()->isoFormat('dddd D MMMM YYYY · HH:mm') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($activeYear)
            <div class="flex items-center gap-2 bg-white/15 backdrop-blur px-4 py-2.5 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-white text-sm font-bold">{{ $activeYear->label }}</span>
            </div>
            @endif
            <div class="bg-white/10 backdrop-blur px-4 py-2.5 rounded-xl text-center">
                <p class="text-indigo-200 text-[10px] uppercase font-bold tracking-wider">Aujourd'hui</p>
                <p class="text-white text-lg font-black leading-none">{{ now()->isoFormat('D MMM') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ── KPI ────────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="anim-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Absences aujourd'hui</p>
            <div class="w-9 h-9 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-red-500">{{ $todayHours }}h</p>
        <p class="text-xs text-gray-400 mt-1">{{ $todayStudents }} élève(s) concerné(s)</p>
    </div>

    <div class="anim-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Absences semaine</p>
            <div class="w-9 h-9 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-[#1A3A6B]">{{ $weekHours }}h</p>
        <p class="text-xs text-red-500 font-semibold mt-1">{{ $weekUnjustified }}h injustifiées</p>
    </div>

    <div class="anim-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Incidents à traiter</p>
            <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 4v5a7 7 0 0 1-14 0V7z"/><path d="M12 14v7"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-amber-600">{{ $disciplinePending }}</p>
        <p class="text-xs text-gray-400 mt-1">Dossiers ouverts</p>
    </div>

    <div class="anim-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Élèves du secteur</p>
            <div class="w-9 h-9 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-purple-700">{{ $totalStudents }}</p>
        <p class="text-xs text-gray-400 mt-1">Année en cours</p>
    </div>

</div>

{{-- ── ACCÈS RAPIDES ──────────────────────────────────────────────────────── --}}
<div class="anim-4 grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('absences.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Absences</p>
            <div class="w-9 h-9 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8.1A6 6 0 1 0 6 8.1"/><path d="M6 18h12"/></svg>
            </div>
        </div>
        <p class="text-sm font-bold text-gray-900 group-hover:text-red-600 transition-colors">Gérer les absences</p>
        <p class="text-xs text-gray-500 mt-0.5">Saisir, justifier et suivre les absences.</p>
    </a>

    <a href="{{ route('discipline.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Discipline</p>
            <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 4v5a7 7 0 0 1-14 0V7z"/></svg>
            </div>
        </div>
        <p class="text-sm font-bold text-gray-900 group-hover:text-amber-700 transition-colors">Registre disciplinaire</p>
        <p class="text-xs text-gray-500 mt-0.5">Incidents, convocation et sanctions.</p>
    </a>

    <a href="{{ route('students.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Élèves</p>
            <div class="w-9 h-9 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5.5 20h13"/><path d="M12 3a4 4 0 0 1 4 4v4a4 4 0 0 1-8 0V7a4 4 0 0 1 4-4z"/></svg>
            </div>
        </div>
        <p class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">Annuaire des élèves</p>
        <p class="text-xs text-gray-500 mt-0.5">Consulter les fiches et contacts.</p>
    </a>
</div>

{{-- ── ABSENTS AUJOURD'HUI ─────────────────────────────────────────────────── --}}
<div class="anim-5 panel mb-5">
    <div class="panel__header">
        <h3>Élèves absents aujourd'hui
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold" style="background:#EDE9FE;color:#5B21B6;">{{ $absentToday->count() }}</span>
        </h3>
        <a href="{{ route('absences.index') }}" class="text-xs font-bold hover:underline" style="color:#4F46E5;">Voir tout →</a>
    </div>

    @if($absentToday->isEmpty())
    <div class="px-5 py-10 text-center flex flex-col items-center gap-3">
        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-sm text-gray-400 italic">Aucun élève absent aujourd'hui. 🎉</p>
    </div>
    @else
    <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
        @foreach($absentToday as $row)
        <a href="{{ route('absences.student', $row['enrollment']) }}"
           class="flex items-center justify-between px-5 py-3 hover:bg-indigo-50/40 transition-colors">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                     style="background:{{ $row['enrollment']->student->gender==='M'?'#4F46E5':'#7C3AED' }};">
                    {{ strtoupper(substr($row['enrollment']->student->last_name,0,1).substr($row['enrollment']->student->first_name,0,1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $row['enrollment']->student->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $row['enrollment']->classGroup->full_name }}</p>
                </div>
            </div>
            <div class="text-right flex-shrink-0 ml-3">
                <p class="text-sm font-black" style="color:{{ $row['is_justified']?'#065F46':'#DC2626' }}">{{ $row['hours'] }}h</p>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                      style="{{ $row['is_justified']?'background:#D1FAE5;color:#065F46;':'background:#FEE2E2;color:#991B1B;' }}">
                    {{ $row['is_justified'] ? 'Justifié' : 'Injustifié' }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>

{{-- ── INCIDENTS + TOP ABSENTÉISTES ──────────────────────────────────────── --}}
<div class="anim-6 grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Incidents récents --}}
    <div class="panel">
        <div class="panel__header">
            <h3>Incidents récents</h3>
            <a href="{{ route('discipline.index') }}" class="text-xs font-bold hover:underline" style="color:#4F46E5;">Voir tout →</a>
        </div>
        @if($recentIncidents->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-gray-400 italic">Aucun incident récent.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($recentIncidents as $inc)
            <a href="{{ route('discipline.show', $inc) }}"
               class="flex items-center justify-between px-5 py-3 hover:bg-indigo-50/40 transition-colors">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $inc->studentEnrollment?->student?->full_name }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $inc->studentEnrollment?->classGroup?->full_name }}
                        · {{ $inc->incident_date->format('d/m/Y') }}
                    </p>
                </div>
                <span class="ml-3 shrink-0 px-2.5 py-1 rounded-full text-xs font-bold badge-open">
                    {{ ucfirst($inc->incident_type) }}
                </span>
            </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Top absentéistes 30j --}}
    <div class="panel">
        <div class="panel__header">
            <h3>Plus absents (30 jours)</h3>
        </div>
        @if($topAbsentees->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-gray-400 italic">Aucune absence sur 30 jours.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($topAbsentees as $i => $row)
            <a href="{{ route('absences.student', $row['enrollment']) }}"
               class="flex items-center justify-between px-5 py-3 hover:bg-indigo-50/40 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shrink-0"
                          style="background:{{ ['#EDE9FE','#F5F3FF','#F3F4F6','#F9FAFB','#F9FAFB'][$i] ?? '#F9FAFB' }};color:#6D28D9;">
                        #{{ $i+1 }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ $row['enrollment']->student->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $row['enrollment']->classGroup->full_name }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0 ml-2">
                    <p class="text-sm font-black text-amber-600">{{ $row['total_hours'] }}h</p>
                    @if($row['unjustified'] > 0)
                    <p class="text-[10px] text-red-500">{{ $row['unjustified'] }}h injust.</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection
