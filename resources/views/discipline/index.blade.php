@extends('layouts.app')
@section('title', 'Discipline')
@section('page-title', 'Discipline')
@section('page-subtitle', 'Suivi des incidents disciplinaires')

@section('content')

{{-- ── STATS ───────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
    @foreach([
        ['label'=>'Total',       'value'=>$stats['total'],       'color'=>'#1A3A6B','bg'=>'#EBF3FB'],
        ['label'=>'En attente',  'value'=>$stats['pending'],     'color'=>'#C8A415','bg'=>'#FBF5E6'],
        ['label'=>'Résolus',     'value'=>$stats['resolved'],    'color'=>'#1A5C2A','bg'=>'#EAF5EA'],
        ['label'=>'Renvois',     'value'=>$stats['suspensions'], 'color'=>'#E87722','bg'=>'#FEF3EA'],
        ['label'=>'Exclusions',  'value'=>$stats['exclusions'],  'color'=>'#EF4444','bg'=>'#FEE2E2'],
    ] as $s)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-2xl font-black" style="color:{{ $s['color'] }}">
            {{ $s['value'] }}
        </p>
        <p class="text-xs text-gray-400 uppercase tracking-wider mt-0.5">
            {{ $s['label'] }}
        </p>
    </div>
    @endforeach
</div>

{{-- ── FILTRES ──────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('discipline.index') }}"
          class="flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-40">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher un élève..."
                   class="w-full pl-8 pr-4 py-2 border border-gray-200
                          rounded-lg text-sm focus:outline-none">
            <span class="absolute inset-y-0 left-2.5 flex items-center text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
        </div>
        <select name="class_id" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
            <option value="">Toutes les classes</option>
            @foreach($classes as $c)
            <option value="{{ $c->id }}" {{ request('class_id')==$c->id?'selected':'' }}>
                {{ $c->full_name }}
            </option>
            @endforeach
        </select>
        <select name="sanction" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
            <option value="">Toutes sanctions</option>
            @foreach(\App\Models\DisciplineIncident::SANCTIONS as $v => $l)
            <option value="{{ $v }}" {{ request('sanction')==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
            <option value="">Tous statuts</option>
            @foreach(\App\Models\DisciplineIncident::STATUSES as $v => $l)
            <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg text-white text-sm font-bold"
                style="background:#1A3A6B;">Filtrer</button>
        @can('manage-discipline')
        <a href="{{ route('discipline.create') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-bold"
           style="background:#E87722;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvel incident
        </a>
        @endcan
    </form>
</div>

{{-- ── TABLE ────────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($incidents->isEmpty())
    <div class="px-5 py-12 text-center text-sm text-gray-400 italic">
        Aucun incident disciplinaire enregistré.
    </div>
    @else
    <table class="w-full">
        <thead>
            <tr style="background:#F8FAFC; border-bottom:1px solid #E5E7EB;">
                @foreach(['Date','Élève','Type d\'incident','Sanction','Statut',''] as $th)
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-400
                           uppercase tracking-wider {{ $loop->last ? 'text-right' : '' }}">
                    {{ $th }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($incidents as $inc)
            @php
                $typeColors = [
                    'retard'        => ['bg'=>'#FEF3C7','text'=>'#92400E','label'=>'Retard'],
                    'comportement'  => ['bg'=>'#FEE2E2','text'=>'#991B1B','label'=>'Comportement'],
                    'fraude'        => ['bg'=>'#EDE9FE','text'=>'#6D28D9','label'=>'Fraude'],
                    'violence'      => ['bg'=>'#FEE2E2','text'=>'#7F1D1D','label'=>'Violence'],
                    'autre'         => ['bg'=>'#F3F4F6','text'=>'#374151','label'=>'Autre'],
                ];
                $sanctionConf = [
                    'observation'           => ['bg'=>'#EBF3FB','text'=>'#1A3A6B','label'=>'Observation'],
                    'warning'               => ['bg'=>'#FEF3C7','text'=>'#92400E','label'=>'Avertissement'],
                    'detention'             => ['bg'=>'#EDE9FE','text'=>'#6D28D9','label'=>'Retenue'],
                    'temporary_suspension'  => ['bg'=>'#FEE2E2','text'=>'#991B1B','label'=>'Renvoi temp.'],
                    'definitive_exclusion'  => ['bg'=>'#450A0A','text'=>'#fff',   'label'=>'Exclusion définitive'],
                ];
                $statusConf = [
                    'open'   => ['bg'=>'#FEF3C7','text'=>'#92400E','label'=>'Ouvert'],
                    'closed' => ['bg'=>'#D1FAE5','text'=>'#065F46','label'=>'Clôturé'],
                ];
                $tc = $typeColors[$inc->incident_type]    ?? $typeColors['autre'];
                $sc = $sanctionConf[$inc->sanction_type]  ?? null;
                $st = $statusConf[$inc->status]           ?? $statusConf['open'];
            @endphp
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-3.5">
                    <p class="text-sm font-bold text-gray-800">
                        {{ $inc->incident_date->format('d/m/Y') }}
                    </p>
                    @if($inc->incident_time)
                    <p class="text-xs text-gray-400">{{ $inc->incident_time }}</p>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <p class="text-sm font-bold text-gray-800">
                        {{ $inc->studentEnrollment?->student?->full_name }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $inc->studentEnrollment?->classGroup?->full_name }}
                    </p>
                </td>
                <td class="px-5 py-3.5">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                          style="background:{{ $tc['bg'] }};color:{{ $tc['text'] }};">
                        {{ $tc['label'] }}
                    </span>
                </td>
                <td class="px-5 py-3.5">
                    @if($sc)
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                          style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">
                        {{ $sc['label'] }}
                    </span>
                    @else
                    <span class="text-gray-300 text-xs">Aucune</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                          style="background:{{ $st['bg'] }};color:{{ $st['text'] }};">
                        {{ $st['label'] }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <a href="{{ route('discipline.show', $inc) }}"
                       class="text-xs font-bold hover:underline"
                       style="color:#1A3A6B;">Voir →</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($incidents->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $incidents->links() }}</div>
    @endif
    @endif
</div>

@endsection