@extends('layouts.app')
@section('title', 'Tableau de bord — Secrétaire')
@section('page-title', 'Tableau de bord')
@section('page-subtitle')Bonjour, {{ auth()->user()->name }} — {{ now()->isoFormat('dddd D MMMM YYYY') }}@endsection

@push('styles')
<style>
@keyframes fadeUp   { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
.anim-1 { animation: fadeUp .4s ease .05s both; }
.anim-2 { animation: fadeUp .4s ease .12s both; }
.anim-3 { animation: fadeUp .4s ease .19s both; }
.anim-4 { animation: fadeUp .4s ease .26s both; }
.anim-5 { animation: fadeUp .4s ease .33s both; }
.anim-6 { animation: fadeUp .4s ease .40s both; }
.anim-7 { animation: fadeUp .4s ease .47s both; }

.kpi-card {
    background: #fff;
    border-radius: 1.25rem;
    border: 1px solid #F3E8FF;
    padding: 1.25rem 1.5rem;
    transition: box-shadow .2s, transform .2s;
}
.kpi-card:hover {
    box-shadow: 0 10px 30px rgba(168,85,247,.12);
    transform: translateY(-3px);
}
.kpi-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: .85rem;
}
.quick-link {
    display: flex; align-items: center; gap: .85rem;
    background: #fff;
    border-radius: 1.1rem;
    border: 1px solid #F3E8FF;
    padding: 1rem 1.15rem;
    text-decoration: none;
    transition: box-shadow .2s, transform .2s, border-color .2s;
}
.quick-link:hover {
    box-shadow: 0 8px 24px rgba(168,85,247,.15);
    border-color: #D8B4FE;
    transform: translateY(-2px);
}
.quick-link__icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.panel {
    background: #fff;
    border-radius: 1.25rem;
    border: 1px solid #F3E8FF;
    overflow: hidden;
}
.panel__header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #FAF5FF;
    display: flex; align-items: center; justify-content: space-between;
}
.panel__header h3 {
    font-size: .8rem; font-weight: 800;
    letter-spacing: .06em; text-transform: uppercase;
    color: #7E22CE;
}
.cat-badge {
    display: inline-flex; align-items: center;
    padding: .25rem .65rem; border-radius: 999px;
    font-size: .65rem; font-weight: 700; letter-spacing: .04em;
}
.cat-admin  { background:#EDE9FE;color:#5B21B6; }
.cat-peda   { background:#DBEAFE;color:#1E40AF; }
.cat-fin    { background:#FEF3C7;color:#92400E; }
.cat-event  { background:#D1FAE5;color:#065F46; }
.cat-gen    { background:#F3F4F6;color:#374151; }
.pinned-dot { width:7px; height:7px; border-radius:50%; background:#A855F7; display:inline-block; margin-right:5px; }
</style>
@endpush

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────────────── --}}
<div class="anim-1 rounded-2xl mb-6 overflow-hidden" style="background: linear-gradient(135deg,#6B21A8 0%,#9333EA 50%,#EC4899 100%);">
    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2 h-2 rounded-full bg-pink-300 animate-pulse"></span>
                <span class="text-purple-200 text-xs font-semibold uppercase tracking-widest">Secrétariat</span>
            </div>
            <h2 class="text-white text-xl font-black">{{ auth()->user()->name }}</h2>
            <p class="text-purple-200 text-sm mt-0.5">{{ now()->isoFormat('dddd D MMMM YYYY · HH:mm') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($activeYear)
            <div class="flex items-center gap-2 bg-white/15 backdrop-blur px-4 py-2.5 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-white text-sm font-bold">{{ $activeYear->label }}</span>
            </div>
            @endif
            <div class="bg-white/10 backdrop-blur px-4 py-2.5 rounded-xl text-center">
                <p class="text-purple-200 text-[10px] uppercase font-bold tracking-wider">Aujourd'hui</p>
                <p class="text-white text-lg font-black leading-none">{{ now()->isoFormat('D MMM') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ── KPI ────────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="anim-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Élèves inscrits</p>
            <div class="w-9 h-9 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-purple-700">{{ $totalStudents }}</p>
        @if($activeYear)
        <p class="text-xs text-gray-400 mt-1">{{ $activeYear->label }}</p>
        @endif
    </div>

    <div class="anim-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Personnel</p>
            <div class="w-9 h-9 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-pink-600">{{ $totalStaff }}</p>
        <p class="text-xs text-gray-400 mt-1">Membres actifs</p>
    </div>

    <div class="anim-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Annonces épinglées</p>
            <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-amber-600">{{ $pinnedAnnouncements }}</p>
        <p class="text-xs text-gray-400 mt-1">En cours de diffusion</p>
    </div>

    <div class="anim-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Classes ouvertes</p>
            <div class="w-9 h-9 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-[#1A3A6B]">{{ $totalClasses }}</p>
        <p class="text-xs text-gray-400 mt-1">Année en cours</p>
    </div>

</div>

{{-- ── ACCÈS RAPIDES ──────────────────────────────────────────────────────── --}}
<div class="anim-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <a href="{{ route('students.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Élèves</p>
            <div class="w-9 h-9 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5.5 20h13"/><path d="M12 3a4 4 0 0 1 4 4v4a4 4 0 0 1-8 0V7a4 4 0 0 1 4-4z"/></svg>
            </div>
        </div>
        <p class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">Annuaire Élèves</p>
        <p class="text-xs text-gray-500 mt-0.5">Consulter fiches et dossiers.</p>
    </a>

    <a href="{{ route('staff.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Personnel</p>
            <div class="w-9 h-9 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
        </div>
        <p class="text-sm font-bold text-gray-900 group-hover:text-pink-600 transition-colors">Registre Personnel</p>
        <p class="text-xs text-gray-500 mt-0.5">Consulter les dossiers RH.</p>
    </a>

    <a href="{{ route('communication.announcements.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Annonces</p>
            <div class="w-9 h-9 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
        </div>
        <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Annonces & Infos</p>
        <p class="text-xs text-gray-500 mt-0.5">Publier et diffuser des notes.</p>
    </a>

    <a href="{{ route('students.create') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Inscription</p>
            <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            </div>
        </div>
        <p class="text-sm font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">Inscrire un élève</p>
        <p class="text-xs text-gray-500 mt-0.5">Créer un nouveau dossier.</p>
    </a>

</div>

{{-- ── ANNONCES + ÉLÈVES RÉCENTS (côte à côte) ─────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 anim-5">

    {{-- Annonces récentes --}}
    <div class="panel">
        <div class="panel__header">
            <h3>Annonces récentes</h3>
            <a href="{{ route('communication.announcements.index') }}"
               class="text-xs font-bold hover:underline" style="color:#9333EA;">Voir tout →</a>
        </div>

        @if($recentAnnouncements->isEmpty())
        <div class="px-5 py-10 text-center flex flex-col items-center gap-2">
            <div class="w-11 h-11 rounded-full flex items-center justify-center" style="background:#F3E8FF;">
                <svg class="w-5 h-5" style="color:#A855F7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <p class="text-sm text-gray-400 italic">Aucune annonce récente.</p>
        </div>
        @else
        <div class="divide-y divide-purple-50 max-h-80 overflow-y-auto">
            @foreach($recentAnnouncements as $ann)
            <div class="px-5 py-3.5 hover:bg-purple-50/30 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap mb-1">
                            @if($ann->is_pinned)
                            <span class="pinned-dot"></span>
                            @endif
                            <span class="cat-badge
                                @if($ann->category === 'administratif') cat-admin
                                @elseif($ann->category === 'pedagogique') cat-peda
                                @elseif($ann->category === 'financier') cat-fin
                                @elseif($ann->category === 'evenement') cat-event
                                @else cat-gen @endif">
                                {{ $ann->category_label }}
                            </span>
                        </div>
                        <p class="text-sm font-bold text-gray-800 truncate">{{ $ann->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $ann->author?->name ?? '—' }}
                            · {{ $ann->published_at?->isoFormat('D MMM') ?? 'Brouillon' }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Inscriptions récentes --}}
    <div class="panel">
        <div class="panel__header">
            <h3>Inscriptions récentes</h3>
            <a href="{{ route('students.index') }}"
               class="text-xs font-bold hover:underline" style="color:#9333EA;">Voir tout →</a>
        </div>

        @if($recentEnrollments->isEmpty())
        <div class="px-5 py-10 text-center flex flex-col items-center gap-2">
            <div class="w-11 h-11 rounded-full flex items-center justify-center" style="background:#F3E8FF;">
                <svg class="w-5 h-5" style="color:#A855F7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5.5 20h13"/><path d="M12 3a4 4 0 0 1 4 4v4a4 4 0 0 1-8 0V7a4 4 0 0 1 4-4z"/></svg>
            </div>
            <p class="text-sm text-gray-400 italic">Aucune inscription récente.</p>
        </div>
        @else
        <div class="divide-y divide-purple-50 max-h-80 overflow-y-auto">
            @foreach($recentEnrollments as $enr)
            <a href="{{ route('students.show', $enr->student) }}"
               class="flex items-center gap-3 px-5 py-3 hover:bg-purple-50/30 transition-colors">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                     style="background:{{ $enr->student->gender==='M'?'#7C3AED':'#DB2777' }};">
                    {{ strtoupper(substr($enr->student->last_name,0,1).substr($enr->student->first_name,0,1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $enr->student->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $enr->classGroup?->full_name ?? '—' }}</p>
                </div>
                <span class="text-[10px] text-gray-400 shrink-0">{{ $enr->created_at->isoFormat('D MMM') }}</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- ── LISTE DU PERSONNEL ──────────────────────────────────────────────────── --}}
<div class="anim-7 panel mt-5">
    <div class="panel__header">
        <h3>Personnel de l'établissement
            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold" style="background:#F3E8FF;color:#7E22CE;">{{ $staffList->count() }}</span>
        </h3>
        <a href="{{ route('staff.index') }}" class="text-xs font-bold hover:underline" style="color:#9333EA;">Voir tout →</a>
    </div>
    @if($staffList->isEmpty())
    <div class="px-5 py-8 text-center text-sm text-gray-400 italic">Aucun personnel enregistré.</div>
    @else
    <div class="divide-y divide-purple-50 max-h-72 overflow-y-auto">
        @foreach($staffList as $s)
        <a href="{{ route('staff.show', $s) }}"
           class="flex items-center gap-3 px-5 py-3 hover:bg-purple-50/30 transition-colors">
            @if($s->photo)
            <img src="{{ asset('storage/'.$s->photo) }}" alt="" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
            @else
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                 style="background:{{ $s->gender==='M'?'#7C3AED':'#DB2777' }};">
                {{ strtoupper(substr($s->last_name,0,1).substr($s->first_name,0,1)) }}
            </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-gray-800 truncate">{{ $s->full_name }}</p>
                <p class="text-xs text-gray-400">
                    {{ $s->primaryPosition ? (App\Models\Staff::positionLabels()[$s->primaryPosition->position] ?? $s->primaryPosition->position) : 'Personnel' }}
                </p>
            </div>
            @if($s->phone)
            <span class="text-[10px] text-gray-400 shrink-0">{{ $s->phone }}</span>
            @endif
        </a>
        @endforeach
    </div>
    @endif
</div>

@endsection
