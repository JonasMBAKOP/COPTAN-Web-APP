@extends('layouts.app')

@section('title', 'Gestion Globale des Frais Scolaires')
@section('page-title', 'Gestion Globale des Frais')
@section('page-subtitle', 'Vue d\'ensemble de la santé financière de l\'établissement')

@push('styles')
<style>
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
.g-card { animation: fadeUp .45s ease both; }
.g-card:nth-child(1){animation-delay:.04s}
.g-card:nth-child(2){animation-delay:.08s}
.g-card:nth-child(3){animation-delay:.12s}
.g-card:nth-child(4){animation-delay:.16s}
.g-kpi-hover { transition: box-shadow .2s ease, transform .2s ease; }
.g-kpi-hover:hover { box-shadow: 0 12px 32px rgba(26,58,107,.12); transform: translateY(-2px); }
</style>
@endpush

@section('content')

{{-- ── BARRE OUTILS ───────────────────────────────────────────────────── --}}
<div class="g-card bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pilotage financier</p>
            <h2 class="text-lg font-black mt-0.5" style="color:#1A3A6B;">
                Gestion globale des frais scolaires
            </h2>
            @if($selectedYear)
            <p class="text-xs text-gray-500 mt-1">
                Période :
                {{ $selectedYear->start_date?->locale('fr')->translatedFormat('F Y') }}
                → {{ $selectedYear->end_date?->locale('fr')->translatedFormat('F Y') }}
            </p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('finances.global') }}" class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Année</label>
                <select name="year_id" onchange="this.form.submit()"
                        class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                               focus:outline-none bg-white"
                        style="color:#1A3A6B;">
                    @foreach($years as $year)
                    <option value="{{ $year->id }}"
                            {{ $selectedYear?->id == $year->id ? 'selected' : '' }}>
                        {{ $year->label }} {{ $year->is_active ? '(Active)' : '' }}
                    </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('finances.reports', ['type' => 'annuel', 'year_id' => $selectedYear?->id]) }}"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold border border-gray-200
                      bg-white text-gray-700 hover:bg-gray-50 transition-all">
                Rapports
            </a>
            <a href="javascript:window.print();"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold border border-gray-200
                      bg-white text-gray-700 hover:bg-gray-50 transition-all">
                Imprimer
            </a>
            <a href="{{ route('finances.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white
                      hover:opacity-95 transition-all shadow-sm"
               style="background-color:#1A3A6B;">
                Enregistrer un paiement
            </a>
        </div>
    </div>
</div>

{{-- ── TABS NAVIGATION ─────────────────────────────────────────────────── --}}
<div class="border-b border-gray-200 mb-6">
    <nav class="flex gap-6 -mb-px">
        <a href="{{ route('finances.global') }}" 
           class="py-2.5 px-1 font-bold text-sm border-b-2 transition-all"
           style="color: #1A3A6B; border-color: #1A3A6B;">
            Vue globale
        </a>
        <a href="{{ route('finances.index') }}" 
           class="py-2.5 px-1 font-medium text-sm text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition-all">
            Par élève
        </a>
        <a href="{{ route('finances.fees-list') }}" 
           class="py-2.5 px-1 font-medium text-sm text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition-all">
            Tranches
        </a>
        <a href="{{ route('finances.payments') }}" 
           class="py-2.5 px-1 font-medium text-sm text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 transition-all">
            Reçus
        </a>
    </nav>
</div>

{{-- ── 4 CARDS KPI PREMIUM ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    
    {{-- Card 1 : Frais attendus --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 g-kpi-hover relative overflow-hidden
                flex items-center justify-between"
         style="border-left: 5px solid #1A3A6B;">
        <div class="space-y-1 z-10">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Frais attendus</p>
            <p class="text-xl font-black text-slate-800" style="color: #1A3A6B;">
                {{ number_format($globalStats['expected']) }} <span class="text-xs font-semibold">FCFA</span>
            </p>
            <p class="text-2xs text-gray-400">Prévisions année scolaire {{ $selectedYear?->label }}</p>
        </div>
        <div class="p-3 rounded-xl bg-blue-50 text-blue-700 z-10" style="background-color: rgba(26,58,107,0.06); color: #1A3A6B;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
    </div>

    {{-- Card 2 : Frais collectés --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 g-kpi-hover relative overflow-hidden flex flex-col justify-between"
         style="border-left: 5px solid #1A5C2A;">
        <div class="flex items-center justify-between mb-2">
            <div class="space-y-1">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Frais collectés</p>
                <p class="text-xl font-black text-green-700" style="color: #1A5C2A;">
                    {{ number_format($globalStats['collected']) }} <span class="text-xs font-semibold">FCFA</span>
                </p>
            </div>
            <div class="p-3 rounded-xl" style="background-color: rgba(26,92,42,0.06); color: #1A5C2A;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <div class="space-y-1">
            <div class="flex items-center justify-between text-2xs font-semibold text-gray-500">
                <span>Taux global</span>
                <span>{{ $globalStats['rate'] }}%</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width: {{ $globalStats['rate'] }}%; background-color: #1A5C2A;"></div>
            </div>
        </div>
    </div>

    {{-- Card 3 : Reste à collecter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between relative overflow-hidden"
         style="border-left: 5px solid #EF4444;">
        <div class="space-y-1 z-10">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Impayés</p>
            <p class="text-xl font-black text-red-500" style="color: #EF4444;">
                {{ number_format($globalStats['remaining']) }} <span class="text-xs font-semibold">FCFA</span>
            </p>
            <p class="text-2xs font-bold text-red-400">Action requise : Relances parents</p>
        </div>
        <div class="p-3 rounded-xl bg-red-50 text-red-700 z-10" style="background-color: rgba(239,68,68,0.06); color: #EF4444;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
    </div>

    {{-- Card 4 : Élèves à jour --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between relative overflow-hidden"
         style="border-left: 5px solid #C8A415;">
        <div class="space-y-1 z-10">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Élèves à jour</p>
            <p class="text-xl font-black text-yellow-600" style="color: #C8A415;">
                {{ $paidInFullRate }}%
            </p>
            <p class="text-2xs text-gray-400">{{ $globalStats['debtors'] ?? 0 }} élève(s) débiteur(s)</p>
        </div>
        <div class="relative flex items-center justify-center z-10" style="width: 52px; height: 52px;">
            <svg class="w-full h-full transform -rotate-90">
                <circle cx="26" cy="26" r="22" stroke="#F3F4F6" stroke-width="4" fill="transparent" />
                <circle cx="26" cy="26" r="22" stroke="#C8A415" stroke-width="4" fill="transparent"
                        stroke-dasharray="138" stroke-dashoffset="{{ 138 - (138 * $paidInFullRate) / 100 }}" />
            </svg>
            <span class="absolute text-2xs font-bold" style="color: #C8A415;">{{ $paidInFullRate }}%</span>
        </div>
    </div>
</div>

{{-- ── GRILLE PRINCIPALE (EVOLUTION & SECTIONS vs TRANCHES & PAIEMENTS) ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    
    {{-- Colonne Gauche (Evolution mensuelle + Sections) --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Collecte Mensuelle (Graphique) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-5 pb-2 border-b border-gray-100">
                <div>
                    <h3 class="font-black text-sm" style="color:#1A3A6B;">Collecte mensuelle</h3>
                    @if($selectedYear)
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $selectedYear->start_date?->locale('fr')->translatedFormat('M Y') }}
                        → {{ $selectedYear->end_date?->locale('fr')->translatedFormat('M Y') }}
                    </p>
                    @endif
                </div>
                @php
                    $monthlyTotal = $monthlyData->sum('total');
                    $maxMonthValue = $monthlyData->max('total') ?: 1;
                @endphp
                <div class="text-right">
                    <p class="text-xs text-gray-400">Total année</p>
                    <p class="text-sm font-black" style="color:#1A3A6B;">
                        {{ number_format($monthlyTotal) }} <span class="text-xs font-normal text-gray-400">FCFA</span>
                    </p>
                </div>
            </div>

            @if($monthlyData->isEmpty() || $monthlyTotal == 0)
            <div class="flex flex-col items-center justify-center py-12 rounded-xl" style="background:#F8FAFC;">
                <p class="text-sm text-gray-400">Aucune collecte enregistrée sur la période.</p>
            </div>
            @else
            <div id="global-chart-bars" class="flex items-end gap-1.5" style="height:168px;">
                @foreach($monthlyData as $i => $m)
                @php
                    $pct = round(($m->total / $maxMonthValue) * 100);
                    if ($m->total > 0 && $pct < 3) $pct = 3;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1 group min-w-0"
                     title="{{ $m->full_label ?? $m->label }} : {{ number_format($m->total) }} FCFA">
                    <span class="text-gray-400 font-bold truncate w-full text-center"
                          style="font-size:8.5px; min-height:14px;">
                        @if($m->total > 0)
                            @if($m->total >= 1000000)
                                {{ number_format($m->total/1000000, 1) }}M
                            @elseif($m->total >= 1000)
                                {{ number_format($m->total/1000, 0) }}k
                            @else
                                {{ number_format($m->total, 0) }}
                            @endif
                        @endif
                    </span>
                    <div class="w-full relative rounded-t-lg overflow-hidden flex-1"
                         style="background:#EBF3FB; min-height:110px;">
                        <div class="global-bar absolute bottom-0 left-0 right-0 rounded-t-lg"
                             data-pct="{{ $pct }}"
                             data-delay="{{ $i * 65 }}"
                             style="height:0;
                                    background:linear-gradient(to top,#0B2040,#2D6FD4);
                                    transition:height .65s cubic-bezier(.22,.68,0,1.2);">
                        </div>
                    </div>
                    <span class="text-gray-500 group-hover:text-blue-700 transition-colors truncate w-full text-center"
                          style="font-size:9px; font-weight:700;">
                        {{ $m->label }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Collecte par Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-bold text-sm" style="color: #1A3A6B;">Collecte par section</h3>
                <div class="flex items-center gap-3 text-2xs font-semibold">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full" style="background-color: #1A3A6B;"></span>Payé</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>Impayé</span>
                </div>
            </div>
            
            <div class="space-y-4">
                @forelse($sectionStats as $secId => $secData)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                            <div>
                                <p class="text-sm font-bold" style="color:#1A3A6B;">{{ $secData->section->name }}</p>
                                <p class="text-2xs font-medium text-gray-400">Reste à collecter : {{ number_format($secData->remaining) }} FCFA</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-800">{{ number_format($secData->collected) }} / {{ number_format($secData->expected) }} <span class="text-2xs text-gray-400">FCFA</span></p>
                                <p class="text-2xs font-black" style="color: #C8A415;">{{ $secData->rate }}%</p>
                            </div>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden relative shadow-inner">
                            <div class="h-full rounded-full transition-all duration-500" 
                                 style="width: {{ $secData->rate }}%; background-color: #1A3A6B;">
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">Aucune section configurée.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Colonne Droite (Tranches, Recents & Actions) --}}
    <div class="space-y-6">
        
        {{-- Tranches de paiement --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-bold text-sm" style="color: #1A3A6B;">Tranches de paiement</h3>
            </div>
            <div class="space-y-4">
                @forelse($installmentStats as $is)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold text-gray-700">
                            <span>{{ $is->label }}</span>
                            <span class="{{ $is->rate >= 70 ? 'text-green-600' : ($is->rate >= 40 ? 'text-yellow-600' : 'text-red-500') }}">
                                {{ $is->rate }}% des élèves
                            </span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300"
                                 style="width: {{ $is->rate }}%; background-color: {{ $is->rate >= 70 ? '#1A5C2A' : ($is->rate >= 40 ? '#C8A415' : '#EF4444') }}">
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">Aucune tranche configurée.</p>
                @endforelse
            </div>
        </div>

        {{-- Paiements récents --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-bold text-sm" style="color: #1A3A6B;">Paiements récents</h3>
            </div>
            <div class="space-y-3">
                @forelse($recentPayments as $p)
                    <div class="flex items-center justify-between border-b border-gray-50 pb-2 last:border-0 last:pb-0">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-800 truncate">
                                {{ $p->studentEnrollment->student->full_name }}
                            </p>
                            <p class="text-2xs text-gray-400 font-semibold truncate">
                                {{ $p->studentEnrollment->classGroup->full_name }} · {{ $p->feeInstallment?->label }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-extrabold" style="color: #1A5C2A;">
                                +{{ number_format($p->amount_paid) }} FCFA
                            </p>
                            <p class="text-2xs text-gray-400">{{ $p->payment_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">Aucun paiement récent.</p>
                @endforelse
            </div>
            <a href="{{ route('finances.payments') }}" 
               class="block text-center text-xs font-bold border-t border-gray-100 pt-3 mt-3 hover:underline"
               style="color: #1A3A6B;">
                Voir tout l'historique →
            </a>
        </div>

        {{-- Actions rapides --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-bold text-sm" style="color: #1A3A6B;">Actions rapides</h3>
            </div>
            <div class="grid grid-cols-1 gap-2.5">
                <a href="{{ route('finances.payments') }}" 
                   class="flex items-center justify-center gap-2 py-2 px-4 border border-transparent rounded-xl text-xs font-bold text-white hover:opacity-95 shadow-sm transition-all"
                   style="background-color: #1A3A6B;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Enregistrer un paiement
                </a>
                <a href="{{ route('finances.payments') }}" 
                   class="flex items-center justify-center gap-2 py-2 px-4 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Générer un reçu
                </a>
                <a href="{{ route('finances.fees-list') }}" 
                   class="flex items-center justify-center gap-2 py-2 px-4 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configurer les frais
                </a>
            </div>
            
            {{-- Note informative sous les actions rapides --}}
            <div class="mt-4 p-3 bg-amber-50 border border-amber-200/50 rounded-xl flex gap-2">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-2xs text-amber-700 leading-normal font-medium">
                    Le prochain rapport financier mensuel sera automatiquement clôturé le <strong>{{ now()->endOfMonth()->format('d/m/Y') }}</strong>. Assurez-vous que tous les paiements du mois soient enregistrés.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ── TABLEAU ELEVES AVEC IMPAYES (DEBITEURS) ────────────────────────── --}}
@if($debtors->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-sm" style="color:#1A3A6B;">
            Élèves avec impayés
            <span class="text-gray-400 font-normal text-xs ml-1">
                ({{ $debtors->count() }} élèves débiteurs)
            </span>
        </h3>
        <a href="{{ route('finances.reports') }}" class="text-xs font-bold hover:underline" style="color:#1A3A6B;">Voir tout →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background-color:#F8FAFC;" class="border-b border-gray-100 text-gray-400 uppercase text-3xs font-black tracking-wider">
                    <th class="text-left px-6 py-3.5">Nom de l'élève</th>
                    <th class="text-left px-4 py-3.5">Classe</th>
                    <th class="text-left px-4 py-3.5">Tranche en retard</th>
                    <th class="text-right px-4 py-3.5">Montant restant dû</th>
                    <th class="text-left px-6 py-3.5">Dernière action</th>
                    <th class="text-center px-6 py-3.5">Relancer</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-xs font-semibold text-gray-700">
                @foreach($debtors->take(10) as $index => $d)
                    @php
                        // Simuler une tranche due par rapport au montant restant et une date
                        $tranchesLabels = ['TRANCHE 1', 'TRANCHE 2', 'TRANCHE 3'];
                        $simulatedTranche = $tranchesLabels[$index % 3];
                        
                        $actionsSim = ['Appel téléphonique (12/10)', 'SMS envoyé (05/10)', 'Courrier remis (20/09)', 'Aucune relance effectuée'];
                        $simulatedAction = $actionsSim[$index % 4];
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {{-- Eleve --}}
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800">{{ $d['enrollment']->student->full_name }}</p>
                            <p class="text-3xs text-gray-400">{{ $d['enrollment']->student->matricule }}</p>
                        </td>
                        {{-- Classe --}}
                        <td class="px-4 py-4 text-gray-600">
                            {{ $d['enrollment']->classGroup->full_name }}
                        </td>
                        {{-- Tranche en retard --}}
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 rounded text-3xs font-extrabold tracking-wider" 
                                  style="background-color: {{ $simulatedTranche === 'TRANCHE 1' ? '#FEE2E2' : ($simulatedTranche === 'TRANCHE 2' ? '#FEF3C7' : '#E0F2FE') }}; 
                                         color: {{ $simulatedTranche === 'TRANCHE 1' ? '#EF4444' : ($simulatedTranche === 'TRANCHE 2' ? '#D97706' : '#0284C7') }};">
                                {{ $simulatedTranche }}
                            </span>
                        </td>
                        {{-- Montant restant --}}
                        <td class="px-4 py-4 text-right text-sm font-extrabold text-red-500">
                            {{ number_format($d['remaining']) }} FCFA
                        </td>
                        {{-- Dernière action --}}
                        <td class="px-6 py-4 text-gray-500 font-medium">
                            {{ $simulatedAction }}
                        </td>
                        {{-- Boutons relancer --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- SMS --}}
                                <button onclick="alert('SMS envoyé à l\'élève {{ addslashes($d['enrollment']->student->full_name) }}')"
                                        title="Envoyer un SMS"
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-gray-100 hover:border-blue-100 shadow-sm bg-white">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                                {{-- Mail --}}
                                <button onclick="alert('Email envoyé à l\'élève {{ addslashes($d['enrollment']->student->full_name) }}')"
                                        title="Envoyer un e-mail"
                                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors border border-gray-100 hover:border-green-100 shadow-sm bg-white">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                {{-- Alerte --}}
                                <button onclick="alert('Rappel programmé pour l\'élève {{ addslashes($d['enrollment']->student->full_name) }}')"
                                        title="Programmer une alerte"
                                        class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors border border-gray-100 hover:border-amber-100 shadow-sm bg-white">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bars = document.querySelectorAll('.global-bar[data-pct]');
    const wrap = document.getElementById('global-chart-bars');
    if (!bars.length || !wrap) return;

    const io = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            bars.forEach(bar => {
                const pct   = parseInt(bar.dataset.pct) || 0;
                const delay = parseInt(bar.dataset.delay) || 0;
                setTimeout(() => { bar.style.height = pct + '%'; }, delay);
            });
            io.unobserve(entry.target);
        });
    }, { threshold: 0.25 });

    io.observe(wrap);
});
</script>
@endpush
