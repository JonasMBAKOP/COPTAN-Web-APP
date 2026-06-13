@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord — Économe')
@section('page-subtitle')
    {{ now()->isoFormat('dddd D MMMM YYYY') }}
@endsection

@push('styles')
<style>
@keyframes fadeUp {
    from { opacity:0; transform:translateY(18px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes countUp {
    from { opacity:0; transform:scale(0.85); }
    to   { opacity:1; transform:scale(1); }
}
@keyframes barGrow {
    from { transform:scaleY(0); }
    to   { transform:scaleY(1); }
}
@keyframes shimmer {
    0%   { background-position:-200% 0; }
    100% { background-position: 200% 0; }
}
.card-anim { animation: fadeUp .45s ease both; }
.card-anim:nth-child(1) { animation-delay:.05s; }
.card-anim:nth-child(2) { animation-delay:.12s; }
.card-anim:nth-child(3) { animation-delay:.19s; }
.card-anim:nth-child(4) { animation-delay:.26s; }
.num-anim  { animation: countUp .5s cubic-bezier(.22,.68,0,1.2) both; }
.bar-origin { transform-origin: bottom; }
.card-hover {
    transition: box-shadow .2s, transform .2s;
}
.card-hover:hover {
    box-shadow: 0 8px 32px rgba(26,58,107,.13);
    transform: translateY(-2px);
}
.btn-action {
    transition: all .18s;
}
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,.13);
}
.stat-pulse::before {
    content:'';
    position:absolute;
    inset:0;
    border-radius:inherit;
    opacity:0;
    background:rgba(255,255,255,.18);
    transition:opacity .2s;
}
.stat-pulse:hover::before { opacity:1; }
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- SALUTATION PERSONNALISÉE                                              --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-black" style="color:#1A3A6B;">
            Bonjour, {{ auth()->user()->name }} 👋
        </h2>
        <p class="text-sm text-gray-500 mt-0.5" id="live-time">
            Chargement...
        </p>
    </div>
    @if($activeYear)
    <div class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-xl
                border border-blue-100"
         style="background-color:#EBF3FB;">
        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
        <span class="text-sm font-semibold" style="color:#1A3A6B;">
            {{ $activeYear->label }}
        </span>
    </div>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- KPI — 4 CARTES                                                        --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- 1. Mes paiements aujourd'hui --}}
    <div class="card-anim card-hover relative bg-white rounded-2xl
                shadow-sm border border-gray-100 overflow-hidden p-5
                cursor-default">
        <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl"
             style="background:linear-gradient(to bottom,#1A3A6B,#2D6FD4);">
        </div>
        <div class="flex items-start justify-between mb-3 pl-2">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase
                           tracking-wider">
                    Mes paiements
                </p>
                <p class="text-xs text-gray-400">Aujourd'hui</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center
                        justify-center flex-shrink-0"
                 style="background-color:#EBF3FB;">
                <svg class="w-5 h-5" style="color:#1A3A6B;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0
                             002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2
                             2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2
                             0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="num-anim text-2xl font-black pl-2"
           style="color:#1A3A6B;">
            {{ number_format($todayAmount) }}
            <span class="text-sm font-semibold text-gray-400">FCFA</span>
        </p>
        <div class="pl-2 mt-1.5 flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2 py-0.5
                         rounded-full text-xs font-bold"
                  style="background-color:#EBF3FB; color:#1A3A6B;">
                <svg class="w-3 h-3" fill="currentColor"
                     viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd"
                          d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3
                             2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/>
                </svg>
                {{ $todayCount }} paiement{{ $todayCount > 1 ? 's' : '' }}
            </span>
        </div>
        {{-- Trait + semaine --}}
        <div class="pl-2 mt-3 pt-3 border-t border-gray-100 flex
                    items-center justify-between">
            <span class="text-xs text-gray-400">Cette semaine</span>
            <span class="text-sm font-black" style="color:#1A3A6B;">
                {{ number_format($weekAmount) }}
                <span class="text-xs font-normal text-gray-400">FCFA</span>
            </span>
        </div>
    </div>

    {{-- 2. Élèves inscrits --}}
    <div class="card-anim card-hover relative bg-white rounded-2xl
                shadow-sm border border-gray-100 overflow-hidden p-5
                cursor-default">
        <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl"
             style="background:linear-gradient(to bottom,#1A5C2A,#2ECC71);">
        </div>
        <div class="flex items-start justify-between mb-3 pl-2">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase
                           tracking-wider">
                    Élèves inscrits
                </p>
                <p class="text-xs text-gray-400">
                    {{ $activeYear?->label ?? '—' }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center
                        justify-center flex-shrink-0"
                 style="background-color:#EAF5EA;">
                <svg class="w-5 h-5" style="color:#1A5C2A;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0
                             0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4
                             0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <p class="num-anim text-3xl font-black pl-2 text-green-700">
            {{ number_format($totalEnrolled) }}
        </p>
        <div class="pl-2 mt-3 pt-3 border-t border-gray-100 flex
                    items-center justify-between">
            <span class="text-xs text-gray-400">Nouvelles (semaine)</span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5
                         rounded-full text-xs font-bold
                         bg-green-100 text-green-700">
                +{{ $newEnrollmentsWeek }}
            </span>
        </div>
    </div>

    {{-- 3. Réinscriptions en attente --}}
    <div class="card-anim card-hover relative bg-white rounded-2xl
                shadow-sm border border-gray-100 overflow-hidden p-5
                cursor-default">
        <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl"
             style="background:linear-gradient(to bottom,#E87722,#F59E0B);">
        </div>
        <div class="flex items-start justify-between mb-3 pl-2">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase
                           tracking-wider">
                    Réinscriptions
                </p>
                <p class="text-xs text-gray-400">En attente</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center
                        justify-center flex-shrink-0"
                 style="background-color:#FEF3EA;">
                <svg class="w-5 h-5" style="color:#E87722;" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582
                             9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0
                             01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
        </div>
        <p class="num-anim text-3xl font-black pl-2"
           style="color:#E87722;">
            {{ number_format($pendingReEnrollments) }}
        </p>
        <div class="pl-2 mt-3 pt-3 border-t border-gray-100">
            @if($pendingReEnrollments > 0)
            <a href="{{ route('students.index') }}"
               class="text-xs font-semibold hover:underline"
               style="color:#E87722;">
                Voir les élèves concernés →
            </a>
            @else
            <span class="text-xs text-green-600 font-semibold">
                ✓ Tous réinscrits
            </span>
            @endif
        </div>
    </div>

    {{-- 4. Carte Heure + Statut --}}
    <div class="card-anim stat-pulse relative rounded-2xl overflow-hidden
                p-5 cursor-default text-white"
         style="background:linear-gradient(135deg,#1A3A6B 0%,#0D2040 100%);">
        <div class="absolute top-0 right-0 w-32 h-32 opacity-10"
             style="background:radial-gradient(circle,#fff 0%,transparent 70%);
                    transform:translate(30%,-30%);">
        </div>
        <p class="text-xs font-bold opacity-60 uppercase tracking-wider mb-2">
            Session active
        </p>
        <p class="text-2xl font-black" id="live-clock">--:--</p>
        <p class="text-xs opacity-60 mt-0.5" id="live-date-card">
            --/--/----
        </p>
        <div class="mt-3 pt-3 border-t border-white/20">
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-green-400
                             animate-pulse"></span>
                <span class="text-xs opacity-80 font-medium">
                    Connecté(e) · Actif(ve)
                </span>
            </div>
            <p class="text-xs opacity-50 mt-1">
                {{ auth()->user()->name }}
            </p>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- LIGNE 2 : GRAPHIQUE + SIDEBAR DROITE                                  --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- ── Collecte mensuelle (chart) ─────────────────────────────────── --}}
    <div class="lg:col-span-2 card-anim"
         style="animation-delay:.30s;"
         x-data="monthlyChart({{ json_encode($chartData) }})">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100
                    p-5 h-full">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-black" style="color:#1A3A6B;">
                        Collecte mensuelle
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Mes enregistrements — 6 derniers mois
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Total période</p>
                    <p class="text-sm font-black" style="color:#1A3A6B;"
                       x-text="formatMoney(totalPeriod)">
                    </p>
                </div>
            </div>

            {{-- Graphique --}}
            <div class="flex items-end gap-2 h-44"
                 style="padding-bottom:0;">
                <template x-for="(bar, i) in data" :key="i">
                    <div class="flex-1 flex flex-col items-center gap-1
                                group cursor-pointer"
                         @mouseenter="hovered = i"
                         @mouseleave="hovered = null">

                        {{-- Tooltip --}}
                        <div x-show="hovered === i"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute z-10 -translate-y-16
                                    bg-gray-900 text-white text-xs font-bold
                                    px-3 py-1.5 rounded-lg shadow-xl
                                    whitespace-nowrap pointer-events-none">
                            <span x-text="formatMoney(bar.total)"></span>
                            <span class="text-gray-400 font-normal ml-1"
                                  x-text="' · ' + bar.count + ' paiem.'">
                            </span>
                            <div class="absolute top-full left-1/2 -translate-x-1/2
                                        -mt-px w-0 h-0"
                                 style="border:5px solid transparent;
                                        border-top-color:#111827;">
                            </div>
                        </div>

                        {{-- Valeur au-dessus de la barre --}}
                        <p class="text-xs font-bold transition-colors"
                           :style="hovered === i ? 'color:#1A3A6B' : 'color:#9CA3AF'"
                           x-text="bar.total > 0 ? formatShort(bar.total) : ''">
                        </p>

                        {{-- Barre --}}
                        <div class="w-full rounded-t-lg overflow-hidden relative"
                             style="height:120px; background:#F3F6F9;">
                            <div class="bar-origin absolute bottom-0 left-0
                                        right-0 rounded-t-lg transition-all
                                        duration-300"
                                 :style="{
                                     height: maxVal > 0
                                         ? (bar.total / maxVal * 100) + '%'
                                         : '3%',
                                     background: hovered === i
                                         ? 'linear-gradient(to top,#0D2040,#2D6FD4)'
                                         : bar.total > 0
                                             ? 'linear-gradient(to top,#1A3A6B,#4A90D4)'
                                             : '#E5E7EB',
                                     animation: 'barGrow .6s cubic-bezier(.22,.68,0,1.2)'
                                               + ' ' + (i * 0.08) + 's both'
                                 }">
                            </div>
                        </div>

                        {{-- Label mois --}}
                        <p class="text-xs font-semibold mt-1 transition-colors"
                           :style="hovered === i ? 'color:#1A3A6B' : 'color:#6B7280'"
                           x-text="bar.label">
                        </p>
                    </div>
                </template>
            </div>

            {{-- Légende --}}
            <div class="flex items-center gap-4 mt-4 pt-4 border-t
                        border-gray-100 text-xs text-gray-400">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded"
                          style="background:linear-gradient(to right,#1A3A6B,#4A90D4);">
                    </span>
                    Mes paiements enregistrés
                </div>
                <div class="ml-auto font-medium">
                    Survolez les barres pour voir le détail
                </div>
            </div>
        </div>
    </div>

    {{-- ── Colonne droite ──────────────────────────────────────────────── --}}
    <div class="space-y-4 card-anim" style="animation-delay:.38s;">

        {{-- Actions rapides --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-black mb-4" style="color:#1A3A6B;">
                Actions rapides
            </h3>
            <div class="space-y-2.5">
                {{-- Enregistrer un paiement --}}
                <a href="{{ route('finances.index') }}"
                   class="btn-action flex items-center gap-3 w-full px-4
                          py-3 rounded-xl text-white text-sm font-bold
                          text-left"
                   style="background-color:#E87722;">
                    <span class="w-8 h-8 bg-white/20 rounded-lg flex
                                 items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                    </span>
                    Enregistrer un paiement
                </a>

                {{-- Inscrire un élève --}}
                <a href="{{ route('students.create') }}"
                   class="btn-action flex items-center gap-3 w-full px-4
                          py-3 rounded-xl text-sm font-bold text-left border-2"
                   style="border-color:#1A3A6B; color:#1A3A6B;">
                    <span class="w-8 h-8 rounded-lg flex items-center
                                 justify-center flex-shrink-0"
                          style="background-color:#EBF3FB;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0
                                     11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </span>
                    Inscrire un élève
                </a>

                {{-- Réinscrire un élève --}}
                <a href="{{ route('students.index') }}"
                   class="btn-action flex items-center gap-3 w-full px-4
                          py-3 rounded-xl text-sm font-bold text-left
                          border-2 border-gray-200 text-gray-700
                          hover:border-gray-300">
                    <span class="w-8 h-8 bg-gray-100 rounded-lg flex
                                 items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-600" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0
                                     004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003
                                     8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </span>
                    Réinscrire un élève
                    @if($pendingReEnrollments > 0)
                    <span class="ml-auto text-xs font-black px-2 py-0.5
                                 rounded-full text-white"
                          style="background-color:#E87722;">
                        {{ $pendingReEnrollments }}
                    </span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Derniers paiements (par elle) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-5 py-3.5 border-b border-gray-100 flex
                        items-center justify-between">
                <h3 class="text-sm font-black" style="color:#1A3A6B;">
                    Mes derniers paiements
                </h3>
                <a href="{{ route('finances.payments') }}"
                   class="text-xs font-semibold hover:underline"
                   style="color:#E87722;">
                    Tout voir →
                </a>
            </div>
            @if($recentPayments->isEmpty())
            <div class="px-5 py-6 text-center text-sm text-gray-400 italic">
                Aucun paiement enregistré.
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($recentPayments as $p)
                <div class="px-4 py-3 flex items-center
                            justify-between gap-3 hover:bg-gray-50
                            transition-colors">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">
                            {{ $p->studentEnrollment?->student?->last_name }}
                            {{ $p->studentEnrollment?->student?->first_name }}
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
</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- LIGNE 3 : INSCRIPTIONS RÉCENTES                                       --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="card-anim bg-white rounded-2xl shadow-sm border border-gray-100
            overflow-hidden" style="animation-delay:.44s;">

    <div class="px-5 py-4 border-b border-gray-100 flex items-center
                justify-between">
        <div>
            <h3 class="text-sm font-black" style="color:#1A3A6B;">
                Inscriptions récentes
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">
                Dernières inscriptions de l'année
                {{ $activeYear?->label }}
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
        Aucune inscription enregistrée pour cette année.
    </div>
    @else

    {{-- Desktop --}}
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
                    <th class="text-right px-5 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider">
                        ACTIONS
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentEnrollments as $enr)
                @php
                    $isNew = $enr->student->enrollments()->count() === 1;
                    $statusConf = [
                        'active'      => ['label' => 'VALIDÉ',      'bg' => '#D1FAE5', 'text' => '#065F46'],
                        'transferred' => ['label' => 'TRANSFERT',   'bg' => '#DBEAFE', 'text' => '#1D4ED8'],
                        'withdrawn'   => ['label' => 'RETIRÉ',      'bg' => '#FEE2E2', 'text' => '#991B1B'],
                        'excluded'    => ['label' => 'EXCLU',       'bg' => '#F3F4F6', 'text' => '#374151'],
                    ];
                    $sc = $statusConf[$enr->status] ?? $statusConf['active'];
                @endphp
                <tr class="hover:bg-gray-50/70 transition-colors group">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            @if($enr->student->photo)
                            <img src="{{ $enr->student->photo_url }}"
                                 class="w-8 h-8 rounded-full object-cover
                                        ring-2 ring-gray-100 flex-shrink-0">
                            @else
                            <div class="w-8 h-8 rounded-full flex items-center
                                        justify-center text-white text-xs
                                        font-bold flex-shrink-0"
                                 style="background-color:
                                    {{ $enr->student->gender === 'M'
                                        ? '#1D4ED8' : '#BE185D' }};">
                                {{ strtoupper(substr($enr->student->last_name, 0, 1))
                                   . strtoupper(substr($enr->student->first_name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ strtoupper($enr->student->last_name) }}
                                    {{ $enr->student->first_name }}
                                </p>
                                @if($isNew)
                                <span class="inline-block text-xs font-bold
                                             px-1.5 py-0.5 rounded text-white"
                                      style="background-color:#E87722;
                                             font-size:9px;">
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
                        <p class="text-sm text-gray-700 font-medium">
                            {{ $enr->enrollment_date->format('d M Y') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $enr->created_at->diffForHumans() }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs
                                     font-bold tracking-wide"
                              style="background-color:{{ $sc['bg'] }};
                                     color:{{ $sc['text'] }};">
                            {{ $sc['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('students.show', $enr->student) }}"
                               class="p-1.5 rounded-lg text-gray-400
                                      hover:text-blue-600 hover:bg-blue-50
                                      transition-colors"
                               title="Voir la fiche">
                                <svg class="w-4 h-4" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016
                                             0z M2.458 12C3.732 7.943 7.523
                                             5 12 5c4.478 0 8.268 2.943
                                             9.542 7-1.274 4.057-5.064
                                             7-9.542 7-4.477 0-8.268
                                             -2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('finances.student', $enr) }}"
                               class="p-1.5 rounded-lg text-gray-400
                                      hover:text-green-600 hover:bg-green-50
                                      transition-colors"
                               title="Compte financier">
                                <svg class="w-4 h-4" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0
                                             00-2 2v6a2 2 0 002 2h2m2 4h10a2
                                             2 0 002-2v-6a2 2 0 00-2-2H9a2 2
                                             0 00-2 2v6a2 2 0 002 2zm7-5a2 2
                                             0 11-4 0 2 2 0 014 0z"/>
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
        @php
            $isNew = $enr->student->enrollments()->count() === 1;
        @endphp
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
                        {{ strtoupper($enr->student->last_name) }}
                        {{ $enr->student->first_name }}
                        @if($isNew)
                        <span class="text-white text-xs px-1 py-0.5 rounded
                                     ml-1 font-bold"
                              style="background:#E87722;font-size:9px;">
                            NEW
                        </span>
                        @endif
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

@push('scripts')
<script>
// ── Horloge en direct ──────────────────────────────────────────────────────
(function liveClock() {
    const months  = ['Jan','Fév','Mar','Avr','Mai','Jun',
                     'Jul','Aoû','Sep','Oct','Nov','Déc'];
    const days    = ['Dimanche','Lundi','Mardi','Mercredi',
                     'Jeudi','Vendredi','Samedi'];

    function update() {
        const now = new Date();
        const hh  = String(now.getHours()).padStart(2,'0');
        const mm  = String(now.getMinutes()).padStart(2,'0');
        const ss  = String(now.getSeconds()).padStart(2,'0');
        const dd  = now.getDate();
        const mon = months[now.getMonth()];
        const yy  = now.getFullYear();
        const day = days[now.getDay()];

        const clock = document.getElementById('live-clock');
        const tDesc = document.getElementById('live-time');
        const cDate = document.getElementById('live-date-card');

        if (clock) clock.textContent = `${hh}:${mm}:${ss}`;
        if (tDesc) tDesc.textContent = `${day} ${dd} ${mon} ${yy}`;
        if (cDate) cDate.textContent = `${String(dd).padStart(2,'0')}/${String(now.getMonth()+1).padStart(2,'0')}/${yy}`;
    }
    update();
    setInterval(update, 1000);
})();

// ── Alpine : graphique mensuel ─────────────────────────────────────────────
function monthlyChart(data) {
    return {
        data,
        hovered: null,

        get maxVal() {
            return Math.max(...this.data.map(d => d.total), 1);
        },

        get totalPeriod() {
            return this.data.reduce((s, d) => s + d.total, 0);
        },

        formatMoney(v) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(v)) + ' FCFA';
        },

        formatShort(v) {
            if (v >= 1_000_000) return (v/1_000_000).toFixed(1) + 'M';
            if (v >= 1_000)     return (v/1_000).toFixed(0) + 'k';
            return new Intl.NumberFormat('fr-FR').format(Math.round(v));
        }
    }
}
</script>
@endpush