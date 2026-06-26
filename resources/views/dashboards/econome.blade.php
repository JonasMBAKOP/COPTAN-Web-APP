@extends('layouts.app')

@section('title', 'Tableau de bord — Économe')
@section('page-title', 'Tableau de bord')
@section('page-subtitle'){{ now()->isoFormat('dddd D MMMM YYYY') }}@endsection

@push('styles')
<style>
/* ── Animations globales ────────────────────────────────────────────── */
@keyframes fadeUp   { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
@keyframes barGrow  { from{transform:scaleY(0)} to{transform:scaleY(1)} }
@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(26,92,42,.35); }
    70%  { box-shadow: 0 0 0 8px rgba(26,92,42,0); }
    100% { box-shadow: 0 0 0 0 rgba(26,92,42,0); }
}
.anim-1 { animation: fadeUp .45s ease .05s both; }
.anim-2 { animation: fadeUp .45s ease .12s both; }
.anim-3 { animation: fadeUp .45s ease .19s both; }
.anim-4 { animation: fadeUp .45s ease .26s both; }
.anim-5 { animation: fadeUp .45s ease .33s both; }
.anim-6 { animation: fadeUp .45s ease .40s both; }
.anim-7 { animation: fadeUp .45s ease .47s both; }

.card-hover {
    transition: box-shadow .2s ease, transform .2s ease;
}
.card-hover:hover {
    box-shadow: 0 10px 36px rgba(26,58,107,.14);
    transform: translateY(-3px);
}
.btn-rapide {
    transition: all .18s ease;
    position: relative;
    overflow: hidden;
}
.btn-rapide::after {
    content:'';
    position:absolute;
    inset:0;
    background:rgba(255,255,255,0);
    transition:background .18s;
}
.btn-rapide:hover::after { background:rgba(255,255,255,.08); }
.btn-rapide:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.14); }
.btn-rapide:active { transform:translateY(0); }

.bar-col { transform-origin: bottom; }
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- BONJOUR                                                               --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="anim-1 flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-black" style="color:#1A3A6B;">
            Bonjour, {{ auth()->user()->name }} 👋
        </h2>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ now()->isoFormat('dddd D MMMM YYYY · HH:mm') }}
        </p>
    </div>
    @if($activeYear)
    <div class="hidden sm:flex items-center gap-2 px-4 py-2.5 rounded-xl
                border border-blue-100" style="background-color:#EBF3FB;">
        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
        <span class="text-sm font-bold" style="color:#1A3A6B;">
            {{ $activeYear->label }}
        </span>
    </div>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- KPI — 4 CARTES                                                        --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- 1. Total frais attendus --}}
    <div class="anim-1 card-hover bg-white rounded-2xl shadow-sm
                border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-5"
             style="background:radial-gradient(circle at 80% 20%,#1A3A6B,transparent 60%);">
        </div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl flex items-center
                        justify-center" style="background-color:#EBF3FB;">
                <svg class="w-5 h-5" style="color:#1A3A6B;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12
                             11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0
                             00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
            Total attendu
        </p>
        <p class="text-2xl font-black"
           style="color:#1A3A6B;">
            <span class="counter" data-target="{{ (int)$totalExpected }}"
                  data-format="money">0</span>
            <span class="text-sm font-semibold text-gray-400 ml-1">FCFA</span>
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Collecte : {{ number_format($totalCollected) }} FCFA · {{ $collectionRate }}%
        </p>
    </div>

    {{-- 2. Mes paiements aujourd'hui --}}
    <div class="anim-2 card-hover bg-white rounded-2xl shadow-sm
                border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-5"
             style="background:radial-gradient(circle at 80% 20%,#E87722,transparent 60%);">
        </div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl flex items-center
                        justify-center" style="background-color:#FEF3EA;">
                <svg class="w-5 h-5" style="color:#E87722;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0
                             002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2
                             2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2
                             2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 rounded-full
                         bg-orange-100 text-orange-700">
                +{{ $todayCount }} paiem.
            </span>
        </div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
            Mes paiements aujourd'hui
        </p>
        <p class="text-2xl font-black" style="color:#E87722;">
            <span class="counter" data-target="{{ (int)$todayAmount }}"
                  data-format="money">0</span>
            <span class="text-sm font-semibold text-gray-400 ml-1">FCFA</span>
        </p>
        <div class="mt-3 pt-3 border-t border-gray-100 flex
                    items-center justify-between">
            <span class="text-xs text-gray-400">Cette semaine</span>
            <span class="text-xs font-black" style="color:#E87722;">
                <span class="counter" data-target="{{ (int)$weekAmount }}"
                      data-format="money">0</span>
                FCFA
            </span>
        </div>
    </div>

    {{-- 3. Élèves inscrits --}}
    <div class="anim-3 card-hover bg-white rounded-2xl shadow-sm
                border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-5"
             style="background:radial-gradient(circle at 80% 20%,#1A5C2A,transparent 60%);">
        </div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl flex items-center
                        justify-center" style="background-color:#EAF5EA;">
                <svg class="w-5 h-5" style="color:#1A5C2A;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0
                             0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4
                             4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
            Élèves inscrits
        </p>
        <p class="text-3xl font-black text-green-700">
            <span class="counter" data-target="{{ $totalEnrolled }}"
                  data-format="number">0</span>
        </p>
        <div class="mt-3 pt-3 border-t border-gray-100 flex
                    items-center justify-between">
            <span class="text-xs text-gray-400">Nouveaux (semaine)</span>
            <span class="text-xs font-black px-2 py-0.5 rounded-full
                         bg-green-100 text-green-700">
                +{{ $newEnrollmentsWeek }}
            </span>
        </div>
    </div>

    {{-- 4. Réinscriptions en attente --}}
    <div class="anim-4 card-hover bg-white rounded-2xl shadow-sm
                border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-5"
             style="background:radial-gradient(circle at 80% 20%,#C8A415,transparent 60%);">
        </div>
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl flex items-center
                        justify-center" style="background-color:#FBF5E6;">
                <svg class="w-5 h-5" style="color:#C8A415;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0
                             0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357
                             -2m15.357 2H15"/>
                </svg>
            </div>
            @if($pendingReEnrollments > 0)
            <span class="w-6 h-6 rounded-full text-xs font-black text-white
                         flex items-center justify-center"
                  style="background-color:#E87722;">
                {{ $pendingReEnrollments }}
            </span>
            @endif
        </div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
            Réinscriptions en attente
        </p>
        <p class="text-3xl font-black" style="color:#C8A415;">
            <span class="counter" data-target="{{ $pendingReEnrollments }}"
                  data-format="number">0</span>
        </p>
        <div class="mt-3 pt-3 border-t border-gray-100">
            @if($pendingReEnrollments > 0)
            <a href="{{ route('students.index') }}"
               class="text-xs font-bold hover:underline"
               style="color:#E87722;">
                Voir les élèves concernés →
            </a>
            @else
            <span class="text-xs font-semibold text-green-600">
                ✓ Tous réinscrits
            </span>
            @endif
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- ACTIONS RAPIDES — pleine largeur, horizontal                          --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="anim-5 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
    <h3 class="text-sm font-black mb-3" style="color:#1A3A6B;">
        Actions rapides
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

        <a href="{{ route('finances.index') }}"
           class="btn-rapide flex items-center gap-3 px-4 py-3 rounded-xl text-white text-sm font-bold"
           style="background-color:#E87722;">
            <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </span>
            Enregistrer un paiement
        </a>

        <a href="{{ route('students.create') }}"
           class="btn-rapide flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold border-2"
           style="border-color:#1A3A6B; color:#1A3A6B;">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                  style="background-color:#EBF3FB;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </span>
            Inscrire un élève
        </a>

        <a href="{{ route('students.index') }}"
           class="btn-rapide flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-700 hover:border-gray-300">
            <span class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </span>
            <span class="flex-1">Réinscrire un élève</span>
            @if($pendingReEnrollments > 0)
            <span class="text-xs font-black text-white px-2 py-0.5 rounded-full"
                  style="background-color:#E87722;">
                {{ $pendingReEnrollments }}
            </span>
            @endif
        </a>

        <a href="{{ route('finances.reports') }}"
           class="btn-rapide flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-700 hover:border-gray-300">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                  style="background-color:#F3E8FF;">
                <svg class="w-4 h-4" style="color:#7C3AED;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </span>
            Rapports financiers
        </a>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- COLLECTE MENSUELLE + DERNIERS PAIEMENTS                               --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- ── Collecte mensuelle ──────────────────────────────────────────── --}}
    <div class="anim-5 lg:col-span-2 bg-white rounded-2xl shadow-sm
                border border-gray-100 p-5 overflow-hidden relative">

        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-black" style="color:#1A3A6B;">
                    Collecte mensuelle
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Mes enregistrements · 6 derniers mois
                </p>
            </div>
            @php
                $periodTotal = collect($chartData)->sum('total');
                $periodCount = collect($chartData)->sum('count');
                $chartMax    = collect($chartData)->max('total') ?: 1;
            @endphp
            <div class="text-right">
                <p class="text-xs text-gray-400">Total période</p>
                <p class="text-sm font-black" style="color:#1A3A6B;">
                    <span class="counter" data-target="{{ (int)$periodTotal }}"
                        data-format="money">0</span>
                    <span class="text-xs font-normal text-gray-400">FCFA</span>
                </p>
                <p class="text-xs text-gray-400">
                    {{ $periodCount }} paiement{{ $periodCount != 1 ? 's' : '' }}
                </p>
            </div>
        </div>

        @if($periodTotal == 0)
        {{-- État vide --}}
        <div class="flex flex-col items-center justify-center py-10 rounded-xl"
            style="background:#F8FAFC;">
            <svg class="w-10 h-10 text-gray-300 mb-2" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2
                        2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0
                        002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2
                        2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="text-sm text-gray-400">Aucun paiement enregistré</p>
            <p class="text-xs text-gray-300 mt-0.5">sur les 6 derniers mois</p>
        </div>

        @else
        {{-- Graphique barres (même pattern que finances/global) --}}
        <div id="eco-chart-bars" class="flex items-end gap-1.5" style="height:168px;">
            @foreach($chartData as $i => $bar)
            @php
                $pct = $chartMax > 0 ? round(($bar['total'] / $chartMax) * 100) : 0;
                if ($bar['total'] > 0 && $pct < 3) $pct = 3;
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group min-w-0"
                 title="{{ $bar['label'] }} : {{ number_format($bar['total']) }} FCFA · {{ $bar['count'] }} paiement{{ $bar['count'] != 1 ? 's' : '' }}">
                <span class="text-gray-400 font-bold truncate w-full text-center"
                      style="font-size:8.5px; min-height:14px;">
                    @if($bar['total'] > 0)
                        @if($bar['total'] >= 1000000)
                            {{ number_format($bar['total'] / 1000000, 1) }}M
                        @elseif($bar['total'] >= 1000)
                            {{ number_format($bar['total'] / 1000, 0) }}k
                        @else
                            {{ number_format($bar['total'], 0) }}
                        @endif
                    @endif
                </span>
                <div class="w-full relative rounded-t-lg overflow-hidden flex-1"
                     style="background:#EBF3FB; min-height:110px;">
                    <div class="eco-bar absolute bottom-0 left-0 right-0 rounded-t-lg"
                         data-pct="{{ $pct }}"
                         data-delay="{{ $i * 65 }}"
                         style="height:0;
                                background:{{ $bar['total'] > 0
                                    ? 'linear-gradient(to top,#0B2040,#2D6FD4)'
                                    : '#E5E7EB' }};
                                transition:height .65s cubic-bezier(.22,.68,0,1.2);">
                    </div>
                </div>
                <span class="text-gray-500 group-hover:text-blue-700 transition-colors truncate w-full text-center"
                      style="font-size:9px; font-weight:700;">
                    {{ $bar['label'] }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Footer du graphique --}}
        <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100">
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <span class="w-3 h-3 rounded"
                    style="background:linear-gradient(to right,#0B2040,#2D6FD4);">
                </span>
                Mes enregistrements uniquement
            </div>
            <a href="{{ route('finances.reports') }}"
            class="ml-auto text-xs font-bold hover:underline"
            style="color:#1A3A6B;">
                Rapport complet →
            </a>
        </div>
    </div>

    {{-- ── Mes derniers paiements ──────────────────────────────────────── --}}
    <div class="anim-6 bg-white rounded-2xl shadow-sm border border-gray-100 lg:col-span-1">
            <div class="px-5 py-3.5 border-b border-gray-100 flex
                        items-center justify-between">
                <h3 class="text-sm font-black" style="color:#1A3A6B;">
                    Mes derniers paiements
                </h3>
                <a href="{{ route('finances.payments') }}"
                   class="text-xs font-semibold hover:underline"
                   style="color:#E87722;">Tout voir →</a>
            </div>
            @if($recentPayments->isEmpty())
            <div class="px-5 py-6 text-center text-sm text-gray-400 italic">
                Aucun paiement enregistré.
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($recentPayments as $p)
                <div class="px-4 py-3 flex items-center justify-between
                            gap-3 hover:bg-gray-50 transition-colors">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">
                            {{ $p->studentEnrollment?->student?->full_name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $p->studentEnrollment?->classGroup?->full_name }}
                            · {{ $p->feeInstallment?->label }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black text-green-600">
                            {{ number_format($p->amount_paid) }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $p->payment_date->format('d/m H:i') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- INSCRIPTIONS RÉCENTES                                                 --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="anim-7 bg-white rounded-2xl shadow-sm border border-gray-100
            overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center
                justify-between">
        <div>
            <h3 class="text-sm font-black" style="color:#1A3A6B;">
                Inscriptions récentes
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">
                Dernières inscriptions · {{ $activeYear?->label ?? '—' }}
            </p>
        </div>
        <a href="{{ route('students.index') }}"
           class="text-xs font-semibold hover:underline"
           style="color:#E87722;">
            Voir tout →
        </a>
    </div>

    @if($recentEnrollments->isEmpty())
    <div class="px-5 py-10 text-center text-sm text-gray-400 italic">
        Aucune inscription enregistrée.
    </div>
    @else
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background-color:#F8FAFC;"
                    class="border-b border-gray-100">
                    <th class="text-left px-5 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider">
                        NOM
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider">
                        CLASSE
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider">
                        DATE
                    </th>
                    <th class="text-center px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider">
                        STATUT
                    </th>
                    <th class="text-right px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentEnrollments as $enr)
                @php
                    $isNew = $enr->student->enrollments()->count() === 1;
                    $sc    = [
                        'active'      => ['label' => 'VALIDÉ',    'bg' => '#D1FAE5', 'text' => '#065F46'],
                        'transferred' => ['label' => 'TRANSFERT', 'bg' => '#DBEAFE', 'text' => '#1D4ED8'],
                        'withdrawn'   => ['label' => 'RETIRÉ',    'bg' => '#FEE2E2', 'text' => '#991B1B'],
                        'excluded'    => ['label' => 'EXCLU',     'bg' => '#F3F4F6', 'text' => '#374151'],
                    ][$enr->status] ?? ['label' => $enr->status, 'bg' => '#F3F4F6', 'text' => '#374151'];
                @endphp
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center
                                        justify-center text-white text-xs
                                        font-bold flex-shrink-0"
                                 style="background-color:
                                    {{ $enr->student->gender === 'M'
                                        ? '#1D4ED8' : '#BE185D' }};">
                                {{ strtoupper(substr($enr->student->last_name, 0, 1))
                                   . strtoupper(substr($enr->student->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ strtoupper($enr->student->last_name) }}
                                    {{ $enr->student->first_name }}
                                </p>
                                @if($isNew)
                                <span class="text-white text-xs font-bold px-1.5
                                             py-0.5 rounded"
                                      style="background:#E87722;font-size:9px;">
                                    NOUVEAU
                                </span>
                                @else
                                <span class="text-xs text-gray-400">
                                    {{ $enr->student->matricule }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $enr->classGroup->full_name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $enr->classGroup->level->section->name }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="text-sm text-gray-700">
                            {{ $enr->enrollment_date->format('d M Y') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $enr->created_at->diffForHumans() }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background-color:{{ $sc['bg'] }};
                                     color:{{ $sc['text'] }};">
                            {{ $sc['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('students.show', $enr->student) }}"
                               class="p-1.5 rounded-lg text-gray-400
                                      hover:text-blue-600 hover:bg-blue-50">
                                <svg class="w-4 h-4" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z
                                             M2.458 12C3.732 7.943 7.523 5 12
                                             5c4.478 0 8.268 2.943 9.542
                                             7-1.274 4.057-5.064 7-9.542
                                             7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('finances.student', $enr) }}"
                               class="p-1.5 rounded-lg text-gray-400
                                      hover:text-green-600 hover:bg-green-50">
                                <svg class="w-4 h-4" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round" stroke-width="2"
                                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2
                                             2v6a2 2 0 002 2h2m2 4h10a2 2 0
                                             002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2
                                             2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0
                                             2 2 0 014 0z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile --}}
    <div class="sm:hidden divide-y divide-gray-100">
        @foreach($recentEnrollments as $enr)
        <div class="p-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full flex items-center
                            justify-center text-white text-xs font-bold
                            flex-shrink-0"
                     style="background-color:
                        {{ $enr->student->gender === 'M'
                            ? '#1D4ED8' : '#BE185D' }};">
                    {{ strtoupper(substr($enr->student->last_name, 0, 1))
                       . strtoupper(substr($enr->student->first_name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">
                        {{ $enr->student->full_name }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $enr->classGroup->full_name }}
                        · {{ $enr->enrollment_date->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('students.show', $enr->student) }}"
               class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600
                      hover:bg-blue-50 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection

{{-- @push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════════════════
   ANIMATIONS COMPTEURS
   ═══════════════════════════════════════════════════════════════════════ */
(function initCounters() {
    const els = document.querySelectorAll('.counter[data-target]');
    if (!els.length) return;

    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el  = entry.target;
            const end = parseFloat(el.dataset.target) || 0;
            const fmt = el.dataset.format || 'number';
            const dur = 1300;
            const t0  = performance.now();

            const tick = (t) => {
                const p = Math.min((t - t0) / dur, 1);
                const e = 1 - Math.pow(1 - p, 3); // cubic ease-out
                const v = Math.round(e * end);

                el.textContent = fmt === 'money'
                    ? new Intl.NumberFormat('fr-FR').format(v)
                    : new Intl.NumberFormat('fr-FR').format(v);

                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            io.unobserve(el);
        });
    }, { threshold: 0.3 });

    els.forEach(el => io.observe(el));
})();

/* ═══════════════════════════════════════════════════════════════════════
   GRAPHIQUE BARRES — ANIMATION + TOOLTIP
   ═══════════════════════════════════════════════════════════════════════ */
(function initChart() {
    const bars    = document.querySelectorAll('.chart-bar-wrap');
    const tooltip = document.getElementById('chart-tooltip');
    if (!bars.length || !tooltip) return;

    const tAmount = document.getElementById('tooltip-amount');
    const tCount  = document.getElementById('tooltip-count');
    const fmt     = v => new Intl.NumberFormat('fr-FR').format(Math.round(v)) + ' FCFA';

    // Animer les barres au chargement
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.querySelectorAll('.bar-col[data-pct]').forEach((bar, i) => {
                const pct = parseInt(bar.dataset.pct) || 0;
                setTimeout(() => {
                    bar.style.height = pct + '%';
                }, i * 70);
            });
            io.unobserve(entry.target);
        });
    }, { threshold: 0.3 });

    const container = document.getElementById('chart-container');
    if (container) io.observe(container);

    // Tooltip au survol
    bars.forEach(wrap => {
        const total = parseFloat(wrap.dataset.total) || 0;
        const count = parseInt(wrap.dataset.count)  || 0;

        wrap.addEventListener('mouseenter', (e) => {
            tAmount.textContent = fmt(total);
            tCount.textContent  = count + ' paiement' + (count > 1 ? 's' : '');
            tooltip.style.opacity = '1';

            const rect = wrap.getBoundingClientRect();
            const cRect = container.getBoundingClientRect();
            tooltip.style.left = (rect.left - cRect.left + rect.width/2) + 'px';
        });

        wrap.addEventListener('mouseleave', () => {
            tooltip.style.opacity = '0';
        });
    });

    // Appliquer hauteur initiale des barres via data-pct
    document.querySelectorAll('.bar-col[data-pct]').forEach(bar => {
        bar.style.height = '0%'; // reset avant animation
    });
})();
</script>
@endpush --}}

@push('scripts')
<script>
/* ═══ COMPTEURS ANIMÉS ═══════════════════════════════════════════════════ */
(function() {
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el  = entry.target;
            const end = parseFloat(el.dataset.target) || 0;
            const dur = 1400;
            const t0  = performance.now();

            const fmt = v => new Intl.NumberFormat('fr-FR').format(Math.round(v));

            function tick(now) {
                const p   = Math.min((now - t0) / dur, 1);
                const eas = 1 - Math.pow(1 - p, 3);
                el.textContent = fmt(eas * end);
                if (p < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
            io.unobserve(el);
        });
    }, { threshold: 0.4 });

    document.querySelectorAll('.counter[data-target]')
        .forEach(el => io.observe(el));
})();

/* ═══ GRAPHIQUE BARRES (collecte mensuelle) ═════════════════════════════ */
(function() {
    const bars = document.querySelectorAll('.eco-bar[data-pct]');
    const wrap = document.getElementById('eco-chart-bars');
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
})();
</script>
@endpush
