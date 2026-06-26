@extends('layouts.app')

@section('title', 'Rapports Financiers')
@section('page-title', 'Rapports Financiers')
@section('page-subtitle')
    @if($isAdmin)
        Vue directeur · Analyses complètes
    @else
        Mes rapports · {{ auth()->user()->name }}
    @endif
@endsection

@push('styles')
<style>
@keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes barW   { from{width:0} to{width:var(--w)} }
.r-card { animation: fadeUp .4s ease both; }
.bar-h  { animation: barW  .8s cubic-bezier(.22,.68,0,1.2) both; }
.r-card:nth-child(1){animation-delay:.05s}
.r-card:nth-child(2){animation-delay:.10s}
.r-card:nth-child(3){animation-delay:.15s}
.r-card:nth-child(4){animation-delay:.20s}
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- FILTRES                                                               --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('finances.reports') }}"
      id="filtersForm"
      class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">

    <div class="flex flex-wrap gap-3 items-end">

        {{-- Type --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase
                           tracking-wider mb-1.5">
                Type de rapport
            </label>
            <div class="flex rounded-xl overflow-hidden border border-gray-200">
                @foreach(['mensuel' => 'Mensuel', 'annuel' => 'Annuel'] as $val => $lbl)
                <button type="button"
                        onclick="setType('{{ $val }}')"
                        class="px-4 py-2 text-sm font-bold transition-colors
                               type-btn {{ $type === $val ? 'active' : '' }}"
                        data-type="{{ $val }}"
                        style="{{ $type === $val
                            ? 'background:#1A3A6B;color:#fff;'
                            : 'background:white;color:#6B7280;' }}">
                    {{ $lbl }}
                </button>
                @endforeach
            </div>
            <input type="hidden" name="type" id="type-input" value="{{ $type }}">
        </div>

        {{-- Année scolaire --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase
                           tracking-wider mb-1.5">
                Année scolaire
            </label>
            <select name="year_id"
                    class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                           focus:outline-none bg-white font-medium"
                    style="color:#1A3A6B;">
                @foreach($years as $yr)
                <option value="{{ $yr->id }}"
                        {{ $selectedYear?->id == $yr->id ? 'selected' : '' }}>
                    {{ $yr->label }} {{ $yr->is_active ? '(Active)' : '' }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Mois (si mensuel) --}}
        <div id="month-filter" style="{{ $type === 'annuel' ? 'display:none' : '' }}">
            <label class="block text-xs font-bold text-gray-500 uppercase
                           tracking-wider mb-1.5">
                Mois
            </label>
            <select name="month"
                    class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                           focus:outline-none bg-white font-medium"
                    style="color:#1A3A6B;">
                @foreach([
                    1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril',
                    5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août',
                    9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre'
                ] as $num => $name)
                <option value="{{ $num }}"
                        {{ $month == $num ? 'selected' : '' }}>
                    {{ $name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Responsable (directeur seulement) --}}
        @if($isAdmin)
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase
                           tracking-wider mb-1.5">
                Responsable
            </label>
            <select name="who"
                    class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                           focus:outline-none bg-white font-medium"
                    style="color:#1A3A6B;">
                <option value="global"  {{ $whoFilter === 'global'  ? 'selected' : '' }}>
                    Global (tous)
                </option>
                <option value="me"      {{ $whoFilter === 'me'      ? 'selected' : '' }}>
                    Moi ({{ auth()->user()->name }})
                </option>
                @foreach($economes as $eco)
                <option value="econome" {{ $whoFilter === 'econome' ? 'selected' : '' }}>
                    Économe — {{ $eco->name }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Boutons --}}
        <div class="flex gap-2 ml-auto">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl
                           text-white text-sm font-bold transition-all
                           hover:shadow-md"
                    style="background-color:#1A3A6B;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0
                             002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0
                             012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2
                             2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2
                             0 01-2-2z"/>
                </svg>
                Générer le rapport
            </button>

            {{-- Aperçu / Impression --}}
            <a href="{{ route('finances.reports.export', request()->query()) }}"
               target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm
                      font-bold border-2 transition-all hover:shadow-md"
               style="border-color:#E87722; color:#E87722;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2
                             2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                             01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Imprimer
            </a>
        </div>
    </div>
</form>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- EN-TÊTE DU RAPPORT                                                   --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h3 class="font-black text-base" style="color:#1A3A6B;">
            Rapport
            {{ $type === 'mensuel' ? 'Mensuel' : 'Annuel' }}
            @if($type === 'mensuel')
            — {{ ['Janvier','Février','Mars','Avril','Mai','Juin',
                   'Juillet','Août','Septembre','Octobre','Novembre','Décembre'][$month-1] }}
            @endif
            · {{ $selectedYear?->label ?? '—' }}
        </h3>
        <p class="text-xs text-gray-500 mt-0.5">
            @if($whoFilter === 'global') Tous les enregistrements
            @elseif($whoFilter === 'me') Mes enregistrements ({{ auth()->user()->name }})
            @else Enregistrements de l'économe
            @endif
            · Généré le {{ now()->format('d/m/Y à H:i') }}
        </p>
    </div>
    <div class="text-right">
        <p class="text-xs text-gray-400">{{ $allPayments->count() }} paiement(s)</p>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- KPI PRINCIPAUX                                                        --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total --}}
    <div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
            Total collecté
        </p>
        <p class="text-2xl font-black" style="color:#1A3A6B;">
            {{ number_format($totalCollected) }}
            <span class="text-sm font-normal text-gray-400">FCFA</span>
        </p>
    </div>

    {{-- Paiements --}}
    <div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
            Paiements
        </p>
        <p class="text-2xl font-black text-green-600">
            {{ $allPayments->count() }}
        </p>
        <p class="text-xs text-gray-400 mt-0.5">opérations</p>
    </div>

    {{-- Espèces --}}
    <div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
            Espèces
        </p>
        @php $cash = $allPayments->where('payment_method','cash')->sum('amount_paid'); @endphp
        <p class="text-2xl font-black" style="color:#C8A415;">
            {{ number_format($cash) }}
            <span class="text-sm font-normal text-gray-400">FCFA</span>
        </p>
    </div>

    {{-- Paiements Mobile (Mobile Money / Orange Money) --}}
    <div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
            Paiements Mobile
        </p>
        @php $mm = $allPayments->whereIn('payment_method',['orange_money','mtn_momo'])->sum('amount_paid'); @endphp
        <p class="text-2xl font-black" style="color:#7C3AED;">
            {{ number_format($mm) }}
            <span class="text-sm font-normal text-gray-400">FCFA</span>
        </p>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- ── Par tranche ───────────────────────────────────────────────── --}}
    <div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-black text-sm mb-4 pb-2 border-b border-gray-100"
            style="color:#1A3A6B;">
            Par tranche de paiement
        </h3>
        @php $maxInst = $byInstallment->max('total') ?: 1; @endphp
        @forelse($byInstallment as $inst)
        <div class="mb-4 last:mb-0">
            <div class="flex justify-between text-sm mb-1">
                <span class="font-semibold text-gray-700">{{ $inst['label'] }}</span>
                <div class="text-right">
                    <span class="font-black" style="color:#1A3A6B;">
                        {{ number_format($inst['total']) }} FCFA
                    </span>
                    <span class="text-xs text-gray-400 ml-1.5">
                        ({{ $inst['count'] }})
                    </span>
                </div>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="bar-h h-full rounded-full"
                     style="--w:{{ round(($inst['total']/$maxInst)*100) }}%;
                            background:linear-gradient(to right,#1A3A6B,#2D6FD4);
                            width:0; animation-delay:{{ $loop->index * 100 }}ms;">
                </div>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 italic">Aucune donnée.</p>
        @endforelse
    </div>

    {{-- ── Par mode de paiement ─────────────────────────────────────── --}}
    <div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-black text-sm mb-4 pb-2 border-b border-gray-100"
            style="color:#1A3A6B;">
            Par mode de paiement
        </h3>
        @php
            $maxMethod  = $byMethod->max('total') ?: 1;
            $mColors    = ['#E87722','#7C3AED','#1A5C2A','#1A3A6B','#6B7280'];
        @endphp
        @forelse($byMethod as $i => $m)
        <div class="mb-4 last:mb-0">
            <div class="flex justify-between text-sm mb-1">
                <span class="font-semibold text-gray-700">{{ $m['label'] }}</span>
                <div class="text-right">
                    <span class="font-black"
                          style="color:{{ $mColors[$i % count($mColors)] }};">
                        {{ number_format($m['total']) }} FCFA
                    </span>
                    <span class="text-xs text-gray-400 ml-1.5">
                        ({{ $m['count'] }})
                    </span>
                </div>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="bar-h h-full rounded-full"
                     style="--w:{{ round(($m['total']/$maxMethod)*100) }}%;
                            background:{{ $mColors[$i % count($mColors)] }};
                            width:0; animation-delay:{{ $loop->index * 100 }}ms;">
                </div>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 italic">Aucune donnée.</p>
        @endforelse
    </div>

</div>

{{-- ── Évolution mensuelle (mode annuel) ─────────────────────────────── --}}
@if($type === 'annuel' && count($evolution))
<div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-5 pb-2 border-b border-gray-100">
        <div>
            <h3 class="font-black text-sm" style="color:#1A3A6B;">
                Évolution mensuelle — {{ $selectedYear?->label }}
            </h3>
            @if($selectedYear)
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $selectedYear->start_date?->locale('fr')->translatedFormat('F Y') }}
                → {{ $selectedYear->end_date?->locale('fr')->translatedFormat('F Y') }}
            </p>
            @endif
        </div>
        @php
            $evoTotal = collect($evolution)->sum('total');
            $evoMax   = collect($evolution)->max('total') ?: 1;
        @endphp
        <div class="text-right">
            <p class="text-xs text-gray-400">Total période</p>
            <p class="text-sm font-black" style="color:#1A3A6B;">
                {{ number_format($evoTotal) }} <span class="text-xs font-normal text-gray-400">FCFA</span>
            </p>
        </div>
    </div>

    <div id="evo-chart-wrap" class="relative">
        <div class="flex items-end gap-1.5 pl-1" style="height:148px;" id="evo-chart-bars">
            @foreach($evolution as $i => $evo)
            @php $pct = round(($evo['total'] / $evoMax) * 100); @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group min-w-0"
                 title="{{ $evo['full_label'] ?? $evo['label'] }} : {{ number_format($evo['total']) }} FCFA ({{ $evo['count'] }})">
                <span class="text-gray-400 font-bold truncate w-full text-center"
                      style="font-size:8.5px; min-height:14px;">
                    @if($evo['total'] > 0)
                        @if($evo['total'] >= 1000000)
                            {{ number_format($evo['total']/1000000, 1) }}M
                        @elseif($evo['total'] >= 1000)
                            {{ number_format($evo['total']/1000, 0) }}k
                        @else
                            {{ number_format($evo['total'], 0) }}
                        @endif
                    @endif
                </span>
                <div class="w-full relative rounded-t-lg overflow-hidden flex-1"
                     style="background:#EBF3FB; min-height:100px;">
                    <div class="evo-bar absolute bottom-0 left-0 right-0 rounded-t-lg"
                         data-pct="{{ $pct }}"
                         data-delay="{{ $i * 70 }}"
                         style="height:0;
                                background:linear-gradient(to top,#0B2040,#2D6FD4);
                                transition:height .65s cubic-bezier(.22,.68,0,1.2);">
                    </div>
                </div>
                <span class="text-gray-500 group-hover:text-blue-700 transition-colors truncate w-full text-center"
                      style="font-size:9px; font-weight:700;">
                    {{ $evo['label'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Tableau détaillé des paiements ─────────────────────────────────── --}}
<div class="r-card bg-white rounded-2xl shadow-sm border border-gray-100
            overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center
                justify-between">
        <h3 class="font-black text-sm" style="color:#1A3A6B;">
            Détail des paiements
            <span class="text-gray-400 font-normal text-xs ml-1">
                ({{ $allPayments->count() }})
            </span>
        </h3>
        <span class="text-xs text-gray-400">
            Triés du plus récent au plus ancien
        </span>
    </div>

    @if($allPayments->isEmpty())
    <div class="px-5 py-10 text-center text-sm text-gray-400 italic">
        Aucun paiement pour la période sélectionnée.
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background:#F8FAFC; border-bottom:1px solid #E5E7EB;">
                    <th class="text-left px-5 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider">
                        Élève
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider
                               hidden sm:table-cell">
                        Tranche
                    </th>
                    <th class="text-right px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider">
                        Montant
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider
                               hidden md:table-cell">
                        Mode
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider
                               hidden lg:table-cell">
                        Date
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider
                               hidden lg:table-cell">
                        Caissier
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-bold
                               text-gray-400 uppercase tracking-wider
                               hidden xl:table-cell">
                        N° Reçu
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($allPayments as $p)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $p->studentEnrollment?->student?->full_name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $p->studentEnrollment?->classGroup?->full_name }}
                        </p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600
                               hidden sm:table-cell">
                        {{ $p->feeInstallment?->label ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="text-sm font-black text-green-600">
                            {{ number_format($p->amount_paid) }}
                        </span>
                        <span class="text-xs text-gray-400">F</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600
                               hidden md:table-cell">
                        {{ $p->payment_method_label }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600
                               hidden lg:table-cell">
                        {{ $p->payment_date->format('d/m/Y') }}
                        {{ $p->created_at->format('H:i') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600
                               hidden lg:table-cell">
                        {{ $p->recordedBy?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 hidden xl:table-cell">
                        <span class="font-mono text-xs font-bold"
                              style="color:#1A3A6B;">
                            {{ $p->receipt_number }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#F8FAFC; border-top:2px solid #E5E7EB;">
                    <td class="px-5 py-3 text-sm font-black uppercase text-gray-500"
                        colspan="2">
                        TOTAL
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="text-base font-black" style="color:#1A3A6B;">
                            {{ number_format($totalCollected) }}
                            <span class="text-xs font-normal text-gray-400">FCFA</span>
                        </span>
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Type toggle
function setType(val) {
    document.getElementById('type-input').value = val;
    document.querySelectorAll('.type-btn').forEach(btn => {
        const active = btn.dataset.type === val;
        btn.style.background = active ? '#1A3A6B' : 'white';
        btn.style.color      = active ? '#fff'    : '#6B7280';
    });
    document.getElementById('month-filter').style.display =
        val === 'mensuel' ? '' : 'none';
}

// Barres évolution mensuelle
document.addEventListener('DOMContentLoaded', () => {
    const bars = document.querySelectorAll('.evo-bar[data-pct]');
    const wrap = document.getElementById('evo-chart-bars');
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