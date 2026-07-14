@extends('layouts.app')

@section('title', 'Gestion Globale des Frais Scolaires')
@section('page-title', 'Gestion Globale des Frais')
@section('page-subtitle', "Vue d'ensemble de la santé financière de l'établissement")

@push('styles')
<style>
@keyframes financeFadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
@keyframes financeGrow { from { width: 0; } to { width: var(--target-width); } }
@keyframes financeBarRise { from { height: 0; } to { height: var(--target-height); } }
.finance-shell { color: #243142; }
.finance-card { animation: financeFadeUp .42s ease both; }
.finance-card:nth-of-type(2) { animation-delay: .04s; }
.finance-card:nth-of-type(3) { animation-delay: .08s; }
.finance-card:nth-of-type(4) { animation-delay: .12s; }
.finance-soft-card { background: #ffffff; border: 1px solid #E5EDF5; box-shadow: 0 10px 30px rgba(15, 23, 42, .06); }
.finance-toolbar { background: #ffffff; border: 1px solid #E5EDF5; box-shadow: 0 14px 40px rgba(15, 23, 42, .05); }
.finance-action { transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, background .16s ease; }
.finance-action:hover { transform: translateY(-1px); box-shadow: 0 10px 22px rgba(31, 78, 121, .08); }
.finance-kpi { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
.finance-kpi:hover { transform: translateY(-2px); box-shadow: 0 14px 34px rgba(31, 78, 121, .10); border-color: #CFE0EE; }
.finance-progress { width: var(--target-width); animation: financeGrow .8s cubic-bezier(.2,.75,.25,1) both; }
.finance-track { background: #E5E7EB; box-shadow: inset 0 1px 2px rgba(15, 23, 42, .08); }
.finance-track .finance-progress { min-width: 3px; }
.finance-chart-bar { height: 0; animation: financeBarRise .72s cubic-bezier(.2,.75,.25,1.12) forwards; animation-delay: var(--delay); }
.finance-tab-active { color: #1F4E79; border-color: #1F4E79; background: #EEF6FC; }
@media print {
    .no-print { display: none !important; }
    .finance-soft-card { box-shadow: none !important; border-color: #CBD5E1 !important; }
    body { background: #fff !important; }
}
</style>
@endpush

@section('content')
@php
    $expected = (float) ($globalStats['expected'] ?? 0);
    $collected = (float) ($globalStats['collected'] ?? 0);
    $remaining = (float) ($globalStats['remaining'] ?? 0);
    $collectionRate = min(100, max(0, (int) ($globalStats['rate'] ?? 0)));
    $paidRate = min(100, max(0, (int) $paidInFullRate));
    $monthlyTotal = $monthlyData->sum('total');
    $maxMonthValue = $monthlyData->max('total') ?: 1;
    $debtorsCount = (int) ($globalStats['debtors'] ?? $debtors->count());
@endphp

<div class="finance-shell space-y-6">
    <section class="finance-toolbar finance-card rounded-[28px] p-4 lg:p-5">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div class="no-print flex w-full flex-col gap-3 lg:flex-row lg:items-center lg:gap-4">
                <form method="GET" action="{{ route('finances.global') }}" class="flex min-w-[240px] items-center gap-3 rounded-[26px] border border-slate-200 bg-slate-50 px-4 py-3">
                    <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div class="min-w-0 flex-1">
                        <label class="block text-[10px] font-black uppercase tracking-wide text-slate-400">Année scolaire</label>
                        <select name="year_id" onchange="this.form.submit()" class="mt-0.5 w-full border-0 bg-transparent p-0 text-sm font-black text-slate-900 outline-none focus:ring-0">
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" {{ $selectedYear?->id == $year->id ? 'selected' : '' }}>
                                    {{ $year->label }}{{ $year->is_active ? ' (Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="grid flex-1 grid-cols-2 gap-2 md:grid-cols-4">
                    <a href="{{ route('finances.global') }}" class="finance-action finance-tab-active inline-flex items-center justify-center gap-2 rounded-[26px] border px-4 py-3 text-xs font-black">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h7"/></svg>
                        Vue globale
                    </a>
                    <a href="{{ route('finances.index') }}" class="finance-action inline-flex items-center justify-center gap-2 rounded-[26px] border border-slate-200 bg-white px-4 py-3 text-xs font-black text-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m0-4a4 4 0 100-8 4 4 0 000 8zm8 0a4 4 0 100-8 4 4 0 000 8z"/></svg>
                        Par élève
                    </a>
                    <a href="{{ route('finances.fees-list') }}" class="finance-action inline-flex items-center justify-center gap-2 rounded-[26px] border border-slate-200 bg-white px-4 py-3 text-xs font-black text-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 9h6M5 3h14a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V5a2 2 0 012-2z"/></svg>
                        Tranches
                    </a>
                    <a href="{{ route('finances.payments') }}" class="finance-action inline-flex items-center justify-center gap-2 rounded-[26px] border border-slate-200 bg-white px-4 py-3 text-xs font-black text-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4M7 4h10a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 012-2z"/></svg>
                        Reçus
                    </a>
                </div>
            </div>

            <div class="no-print flex flex-col gap-2 sm:flex-row xl:shrink-0">
                <a href="{{ route('finances.reports', ['type' => 'annuel', 'year_id' => $selectedYear?->id]) }}" class="finance-action inline-flex items-center justify-center gap-2 rounded-[26px] border border-slate-200 bg-white px-4 py-3 text-xs font-black text-slate-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Rapports
                </a>
                <a href="{{ route('finances.fees-list') }}" class="finance-action inline-flex items-center justify-center gap-2 rounded-[26px] border border-slate-200 bg-white px-4 py-3 text-xs font-black text-slate-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Configurer les frais
                </a>
                <a href="{{ route('finances.index') }}" class="finance-action inline-flex items-center justify-center gap-2 rounded-[26px] bg-[#1F4E79] px-4 py-3 text-xs font-black text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                    Paiement
                </a>
            </div>
        </div>
    </section>
    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="finance-soft-card finance-kpi finance-card rounded-[28px] p-5 ring-1 ring-transparent">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-wide text-slate-400">Frais attendus</p>
                    <p class="mt-2 text-2xl font-black text-slate-900" data-count-up="{{ (int) $expected }}" data-count-suffix=" FCFA">{{ number_format($expected) }} <span class="text-xs text-slate-400">FCFA</span></p>
                    <p class="mt-1 text-xs font-semibold text-slate-400">Prévisions de l'année</p>
                </div>
                <div class="rounded-2xl bg-slate-100 p-3 text-slate-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-9 4h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            </div>
        </div>

        <div class="finance-soft-card finance-kpi finance-card rounded-[28px] p-5 ring-1 ring-transparent">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-wide text-slate-400">Frais collectés</p>
                    <p class="mt-2 text-2xl font-black text-emerald-700" data-count-up="{{ (int) $collected }}" data-count-suffix=" FCFA">{{ number_format($collected) }} <span class="text-xs text-slate-400">FCFA</span></p>
                    <p class="mt-1 text-xs font-semibold text-slate-400">{{ $collectionRate }}% du prévisionnel</p>
                </div>
                <div class="rounded-2xl bg-emerald-50 p-3 text-emerald-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg></div>
            </div>
            <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100"><div class="finance-progress h-full rounded-full bg-emerald-700" style="--target-width: {{ $collectionRate }}%;"></div></div>
        </div>

        <div class="finance-soft-card finance-kpi finance-card rounded-[28px] p-5 ring-1 ring-transparent">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-wide text-slate-400">Reste à collecter</p>
                    <p class="mt-2 text-2xl font-black text-rose-700" data-count-up="{{ (int) $remaining }}" data-count-suffix=" FCFA">{{ number_format($remaining) }} <span class="text-xs text-slate-400">FCFA</span></p>
                    <p class="mt-1 text-xs font-semibold text-slate-400">{{ $debtorsCount }} élève(s) débiteur(s)</p>
                </div>
                <div class="rounded-2xl bg-rose-50 p-3 text-rose-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></div>
            </div>
        </div>

        <div class="finance-soft-card finance-kpi finance-card rounded-[28px] p-5 ring-1 ring-transparent">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-wide text-slate-400">Élèves à jour</p>
                    <p class="mt-2 text-2xl font-black text-amber-700">{{ $paidRate }}%</p>
                    <p class="mt-1 text-xs font-semibold text-slate-400">{{ max(0, $totalEnrolled - $debtorsCount) }} / {{ $totalEnrolled }} élèves</p>
                </div>
                <div class="relative h-16 w-16">
                    <svg class="h-16 w-16 -rotate-90" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="25" stroke="#EEF2F7" stroke-width="7" fill="none" />
                        <circle cx="32" cy="32" r="25" stroke="#D8A84A" stroke-width="7" fill="none" stroke-linecap="round" stroke-dasharray="157" stroke-dashoffset="{{ 157 - (157 * $paidRate) / 100 }}" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-xs font-black text-amber-700">{{ $paidRate }}%</span>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[2fr_1fr]">
        <div class="space-y-6">
            <div class="finance-soft-card finance-card rounded-[28px] p-5 lg:p-6">
                <div class="mb-5 flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-black text-slate-900">Collecte mensuelle</h3>
                        <p class="mt-1 text-xs font-semibold text-slate-400">Montants encaissés sur la période scolaire</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-2 text-right">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Total période</p>
                        <p class="text-sm font-black text-slate-900" data-count-up="{{ (int) $monthlyTotal }}" data-count-suffix=" FCFA">{{ number_format($monthlyTotal) }} FCFA</p>
                    </div>
                </div>

                @if($monthlyData->isEmpty() || $monthlyTotal == 0)
                    <div class="flex min-h-[220px] items-center justify-center rounded-[26px] border border-dashed border-slate-200 bg-slate-50 text-sm font-semibold text-slate-400">Aucune collecte enregistrée sur la période.</div>
                @else
                    <div id="global-monthly-chart" class="flex h-[238px] items-end gap-2 rounded-[26px] bg-slate-50 px-3 pb-3 pt-4 sm:gap-3">
                        @foreach($monthlyData as $i => $m)
                            @php
                                $pct = $maxMonthValue > 0 ? round(($m->total / $maxMonthValue) * 100) : 0;
                                if ($m->total > 0 && $pct < 4) $pct = 4;
                                $shortValue = $m->total >= 1000000 ? number_format($m->total / 1000000, 1).'M' : ($m->total >= 1000 ? number_format($m->total / 1000, 0).'k' : number_format($m->total, 0));
                            @endphp
                            <div class="group flex min-w-0 flex-1 flex-col items-center gap-2" title="{{ $m->full_label ?? $m->label }} : {{ number_format($m->total) }} FCFA">
                                <span class="h-4 w-full truncate text-center text-[9px] font-black text-slate-500">
                                    @if($m->total > 0) {{ $shortValue }} @endif
                                </span>
                                <div class="flex h-[168px] w-full items-end overflow-hidden rounded-t-[24px] bg-slate-100">
                                    <div class="global-monthly-bar w-full rounded-t-[24px] bg-gradient-to-t from-[#1A3A6B] to-[#2D6FD4]" data-pct="{{ $pct }}" data-delay="{{ $i * 65 }}" style="height:0; transition:height .72s cubic-bezier(.22,.68,0,1.16);"></div>
                                </div>
                                <span class="w-full truncate text-center text-[10px] font-black uppercase text-slate-500">{{ $m->label }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="finance-soft-card finance-card rounded-[28px] p-5 lg:p-6">
                <div class="mb-5 flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h3 class="text-base font-black text-slate-900">Collecte par section</h3>
                        <p class="mt-1 text-xs font-semibold text-slate-400">Progression par grand pôle pédagogique</p>
                    </div>
                </div>
                <div class="space-y-4">
                    @forelse($sectionStats as $secData)
                        @php $rate = min(100, max(0, (int) $secData->rate)); @endphp
                        <div class="rounded-[24px] border border-slate-200 bg-white p-4">
                            <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-black text-slate-900">{{ $secData->section->name }}</p>
                                    <p class="text-xs font-semibold text-slate-400">Reste : {{ number_format($secData->remaining) }} FCFA</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-sm font-black text-slate-700">{{ number_format($secData->collected) }} / {{ number_format($secData->expected) }} FCFA</p>
                                    <p class="text-xs font-black text-[#1F4E79]">{{ $rate }}%</p>
                                </div>
                            </div>
                            <div class="finance-track h-2.5 overflow-hidden rounded-full"><div class="finance-progress h-full rounded-full bg-[#1F4E79]" style="--target-width: {{ $rate }}%;"></div></div>
                        </div>
                    @empty
                        <p class="rounded-[26px] border border-dashed border-slate-200 bg-slate-50 py-8 text-center text-sm font-semibold text-slate-400">Aucune section configurée.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="finance-soft-card finance-card rounded-[28px] p-5">
                <h3 class="border-b border-slate-200 pb-4 text-base font-black text-slate-900">Tranches de paiement</h3>
                <div class="mt-4 space-y-4">
                    @forelse($installmentStats as $is)
                        @php
                            $rate = min(100, max(0, (int) $is->rate));
                            $label = strtolower((string) $is->label);
                            $tone = str_contains($label, 'carnet') || str_contains($label, 'medical') || str_contains($label, 'médical')
                                ? '#0EA5A4'
                                : ($rate >= 70 ? '#1A5C2A' : ($rate >= 40 ? '#C8A415' : '#EF4444'));
                        @endphp
                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-4">
                            <div class="mb-1.5 flex items-center justify-between gap-3 text-xs font-black">
                                <span class="truncate text-slate-700">{{ $is->label }}</span>
                                <span style="color: {{ $tone }};">{{ $rate }}%</span>
                            </div>
                            <div class="finance-track h-2.5 overflow-hidden rounded-full"><div class="finance-progress h-full rounded-full" style="--target-width: {{ $rate }}%; background: {{ $tone }};"></div></div>
                            <p class="mt-1 text-[11px] font-semibold text-slate-500">{{ number_format($is->collected) }} / {{ number_format($is->expected) }} FCFA · {{ $is->payers }} payeur(s)</p>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm font-semibold text-slate-400">Aucune tranche configurée.</p>
                    @endforelse
                </div>
            </div>

            <div class="finance-soft-card finance-card rounded-[28px] p-5">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <h3 class="text-base font-black text-slate-900">Paiements récents</h3>
                    <a href="{{ route('finances.payments') }}" class="text-xs font-black text-slate-900 hover:underline">Tout voir</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse($recentPayments as $p)
                        <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-black text-slate-900">{{ $p->studentEnrollment->student->full_name }}</p>
                                    <p class="mt-0.5 truncate text-[11px] font-semibold text-slate-500">{{ $p->studentEnrollment->classGroup->full_name }} · {{ $p->feeInstallment?->label }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="whitespace-nowrap text-xs font-black text-emerald-700">+{{ number_format($p->amount_paid) }}</p>
                                    <p class="text-[11px] font-semibold text-slate-400">{{ $p->payment_date->format('d/m') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm font-semibold text-slate-400">Aucun paiement récent.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>

    @if($debtors->isNotEmpty())
        <section class="finance-soft-card finance-card overflow-hidden rounded-[28px]">
            <div class="flex flex-col gap-2 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-black text-slate-900">Élèves avec impayés</h3>
                    <p class="text-xs font-semibold text-slate-500">Top 10 des soldes restants les plus élevés</p>
                </div>
                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-black text-rose-700">{{ $debtors->count() }} débiteur(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left">
                    <thead class="bg-white text-[11px] font-black uppercase tracking-wide text-slate-400">
                        <tr class="border-b border-slate-200">
                            <th class="px-5 py-3">Élève</th>
                            <th class="px-4 py-3">Classe</th>
                            <th class="px-4 py-3 text-right">Dû</th>
                            <th class="px-4 py-3 text-right">Payé</th>
                            <th class="px-4 py-3 text-right">Reste</th>
                            <th class="px-5 py-3 text-center no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs font-semibold text-slate-700">
                        @foreach($debtors->take(10) as $d)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <p class="font-black text-slate-900">{{ $d['enrollment']->student->full_name }}</p>
                                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $d['enrollment']->student->matricule }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $d['enrollment']->classGroup->full_name }}</td>
                                <td class="px-4 py-4 text-right font-black text-slate-700">{{ number_format($d['due']) }} FCFA</td>
                                <td class="px-4 py-4 text-right font-black text-emerald-700">{{ number_format($d['paid']) }} FCFA</td>
                                <td class="px-4 py-4 text-right font-black text-rose-700">{{ number_format($d['remaining']) }} FCFA</td>
                                <td class="px-5 py-4 text-center no-print">
                                    <a href="{{ route('finances.student.receipt', $d['enrollment']) }}" class="inline-flex items-center justify-center rounded-[22px] border border-slate-200 bg-white px-3 py-2 text-[11px] font-black text-slate-900 transition hover:bg-slate-50">Voir dossier</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chart = document.getElementById('global-monthly-chart');
    const bars = document.querySelectorAll('.global-monthly-bar[data-pct]');
    if (!chart || !bars.length) return;

    const animateBars = () => {
        bars.forEach((bar) => {
            const pct = parseInt(bar.dataset.pct || '0', 10);
            const delay = parseInt(bar.dataset.delay || '0', 10);
            setTimeout(() => { bar.style.height = pct + '%'; }, delay);
        });
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                animateBars();
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.2 });
        observer.observe(chart);
    } else {
        animateBars();
    }
});
</script>
@endpush