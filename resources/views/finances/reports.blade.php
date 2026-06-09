@extends('layouts.app')

@section('title', 'Rapports financiers')
@section('page-title', 'Rapports Financiers')
@section('page-subtitle', 'Analyse détaillée des finances')

@section('content')

{{-- Sélecteur année --}}
<div class="flex items-center gap-3 mb-6">
    <form method="GET" action="{{ route('finances.reports') }}"
          class="flex items-center gap-2">
        <label class="text-sm text-gray-500">Année :</label>
        <select name="year_id" onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-200 rounded-lg text-sm
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
</div>

{{-- ── STATS GLOBALES ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
    @foreach([
        ['label' => 'Attendu',   'value' => number_format($globalStats['expected'])  . ' FCFA', 'color' => '#1A3A6B', 'bg' => '#EBF3FB'],
        ['label' => 'Collecté',  'value' => number_format($globalStats['collected']) . ' FCFA', 'color' => '#1A5C2A', 'bg' => '#EAF5EA'],
        ['label' => 'Restant',   'value' => number_format($globalStats['remaining']) . ' FCFA', 'color' => '#EF4444', 'bg' => '#FEE2E2'],
        ['label' => 'Taux',      'value' => $globalStats['rate'] . '%', 'color' => '#C8A415', 'bg' => '#FBF5E6'],
        ['label' => 'Débiteurs', 'value' => $globalStats['debtors'] . ' élèves', 'color' => '#7C3AED', 'bg' => '#F3E8FF'],
    ] as $s)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">
            {{ $s['label'] }}
        </p>
        <p class="text-sm font-black" style="color:{{ $s['color'] }}">
            {{ $s['value'] }}
        </p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- ── Par classe ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100
                overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-sm" style="color:#1A3A6B;">
                Collecte par classe
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($classeStats->sortByDesc('rate') as $row)
            <div class="px-5 py-3">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">
                            {{ $row['class']->full_name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $row['class']->level->section->name }}
                            · {{ $row['class']->enrolled_count }} élèves
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold
                                   {{ $row['rate'] >= 80 ? 'text-green-600'
                                       : ($row['rate'] >= 50 ? 'text-amber-600'
                                       : 'text-red-500') }}">
                            {{ $row['rate'] }}%
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ number_format($row['collected']) }}
                            / {{ number_format($row['expected']) }}
                        </p>
                    </div>
                </div>
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full"
                         style="width:{{ $row['rate'] }}%;
                                background-color:{{ $row['rate'] >= 80
                                    ? '#1A5C2A' : ($row['rate'] >= 50
                                    ? '#C8A415' : '#EF4444') }}">
                    </div>
                </div>
            </div>
            @empty
            <p class="px-5 py-8 text-center text-sm text-gray-400 italic">
                Aucune donnée disponible.
            </p>
            @endforelse
        </div>
    </div>

    {{-- ── Par mode de paiement + par tranche ─────────────────────────── --}}
    <div class="space-y-4">

        {{-- Par mode --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-sm mb-4 pb-2 border-b border-gray-100"
                style="color:#1A3A6B;">
                Par mode de paiement
            </h3>
            @php
                $methodLabels = [
                    'cash'          => ['label' => 'Espèces',       'icon' => '💵'],
                    'orange_money'  => ['label' => 'Orange Money',  'icon' => '🟠'],
                    'mtn_momo'      => ['label' => 'MTN MoMo',      'icon' => '🟡'],
                    'bank_transfer' => ['label' => 'Virement',      'icon' => '🏦'],
                    'other'         => ['label' => 'Autre',         'icon' => '💳'],
                ];
                $totalAll = $paymentMethods->sum('total') ?: 1;
            @endphp
            @forelse($paymentMethods->sortByDesc('total') as $pm)
            @php
                $pct = round(($pm->total / $totalAll) * 100);
                $info = $methodLabels[$pm->payment_method]
                    ?? ['label' => ucfirst($pm->payment_method), 'icon' => '💳'];
            @endphp
            <div class="mb-3">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="flex items-center gap-1.5 text-gray-700">
                        {{ $info['icon'] }} {{ $info['label'] }}
                        <span class="text-xs text-gray-400">
                            ({{ $pm->count }} paiement(s))
                        </span>
                    </span>
                    <span class="font-semibold" style="color:#1A3A6B;">
                        {{ number_format($pm->total) }} FCFA
                    </span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full"
                         style="width:{{ $pct }}%;
                                background-color:#1A3A6B;">
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 italic">Aucun paiement.</p>
            @endforelse
        </div>

        {{-- Par tranche --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-sm mb-4 pb-2 border-b border-gray-100"
                style="color:#1A3A6B;">
                Par tranche
            </h3>
            @forelse($installmentStats as $is)
            <div class="flex items-center justify-between text-sm mb-3">
                <span class="text-gray-700">{{ $is->label }}</span>
                <div class="text-right">
                    <p class="font-semibold text-green-600">
                        {{ number_format($is->collected ?? 0) }} FCFA
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $is->payers ?? 0 }} payeur(s)
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 italic">Aucune tranche configurée.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- ── LISTE DES DÉBITEURS ──────────────────────────────────────────────── --}}
@if($debtors->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center
                justify-between">
        <h3 class="font-semibold text-sm" style="color:#1A3A6B;">
            Élèves avec solde impayé
            <span class="text-gray-400 font-normal text-xs ml-1">
                ({{ $debtors->count() }})
            </span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background-color:#F8FAFC;"
                    class="border-b border-gray-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider">
                        Élève
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider
                               hidden sm:table-cell">
                        Classe
                    </th>
                    <th class="text-right px-4 py-3 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider">
                        Payé
                    </th>
                    <th class="text-right px-4 py-3 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider">
                        Restant dû
                    </th>
                    <th class="text-right px-5 py-3 text-xs font-semibold
                               text-gray-400 uppercase tracking-wider">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($debtors as $d)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="text-sm font-medium text-gray-800">
                            {{ $d['enrollment']->student->full_name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $d['enrollment']->student->matricule }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600
                               hidden sm:table-cell">
                        {{ $d['enrollment']->classGroup->full_name }}
                    </td>
                    <td class="px-4 py-3.5 text-right text-sm text-green-600
                               font-medium">
                        {{ number_format($d['paid']) }} FCFA
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <span class="text-sm font-bold text-red-500">
                            {{ number_format($d['remaining']) }} FCFA
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('finances.student',
                                         $d['enrollment']) }}"
                           class="text-xs font-medium hover:underline"
                           style="color:#1A3A6B;">
                            Voir compte →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection