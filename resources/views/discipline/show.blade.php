@extends('layouts.app')
@section('title', 'Incident — ' . $disciplineIncident->studentEnrollment->student->full_name)
@section('page-title', 'Détail de l\'incident')
@section('page-subtitle'){{ $disciplineIncident->incident_date->format('d/m/Y') }}@endsection

@section('content')

@php
    $typeLabels   = \App\Models\DisciplineIncident::INCIDENT_TYPES;
    $statusLabels = \App\Models\DisciplineIncident::STATUSES;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Colonne principale ──────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold"
                      style="background:#FEE2E2;color:#991B1B;">
                    {{ $disciplineIncident->incident_type_label }}
                </span>
                @can('manage-discipline')
                <form method="POST"
                      action="{{ route('discipline.status', $disciplineIncident) }}"
                      class="flex items-center gap-2">
                    @csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()"
                            class="px-3 py-1.5 border border-gray-200 rounded-lg
                                   text-xs font-bold bg-white">
                        @foreach($statusLabels as $v => $l)
                        <option value="{{ $v }}"
                                {{ $disciplineIncident->status==$v?'selected':'' }}>
                            {{ $l }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @endcan
            </div>

            <h2 class="font-black text-lg mb-1" style="color:#1A3A6B;">
                {{ $disciplineIncident->studentEnrollment->student->full_name }}
            </h2>
            <p class="text-sm text-gray-500 mb-4">
                {{ $disciplineIncident->studentEnrollment->classGroup->full_name }}
                · {{ $disciplineIncident->incident_date->format('d/m/Y') }}
                @if($disciplineIncident->incident_time)
                à {{ $disciplineIncident->incident_time }}
                @endif
                @if($disciplineIncident->location)
                · {{ $disciplineIncident->location_label }}
                @endif
            </p>

            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ $disciplineIncident->description }}
                </p>
            </div>
        </div>

        {{-- Sanction --}}
        @if($disciplineIncident->sanction_type)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-black mb-3" style="color:#1A3A6B;">
                Sanction appliquée
            </h3>
            <p class="text-base font-bold text-gray-800">
                {{ $disciplineIncident->sanction_label }}
            </p>
            @if($disciplineIncident->sanction_duration_days)
            <p class="text-sm text-gray-500 mt-1">
                Durée : {{ $disciplineIncident->sanction_duration_days }} jour(s)
            </p>
            @endif
            @if($disciplineIncident->parent_convoked)
            <div class="mt-3 flex items-center gap-2 text-sm text-amber-700
                        bg-amber-50 px-3 py-2 rounded-lg">
                ⚠ Parent convoqué
                @if($disciplineIncident->convocation_date)
                le {{ $disciplineIncident->convocation_date->format('d/m/Y') }}
                @endif
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- ── Colonne droite ───────────────────────────────────────────────── --}}
    <div class="space-y-4">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-black mb-3" style="color:#1A3A6B;">
                Informations
            </h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Signalé par</dt>
                    <dd class="font-semibold text-gray-800">
                        {{ $disciplineIncident->reportedBy?->name ?? '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Décidé par</dt>
                    <dd class="font-semibold text-gray-800">
                        {{ $disciplineIncident->decidedBy?->name ?? '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Créé le</dt>
                    <dd class="font-semibold text-gray-800">
                        {{ $disciplineIncident->created_at->format('d/m/Y H:i') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Historique élève --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-black mb-3" style="color:#1A3A6B;">
                Autres incidents
                <span class="text-gray-400 font-normal text-xs ml-1">
                    ({{ $otherIncidents->count() }})
                </span>
            </h3>
            @if($otherIncidents->isEmpty())
            <p class="text-xs text-gray-400 italic">Aucun autre incident.</p>
            @else
            <div class="space-y-2">
                @foreach($otherIncidents as $oi)
                <a href="{{ route('discipline.show', $oi) }}"
                   class="block p-2.5 rounded-lg hover:bg-gray-50
                          transition-colors border border-gray-100">
                    <p class="text-xs font-bold text-gray-700">
                        {{ $typeLabels[$oi->incident_type] ?? 'Autre' }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $oi->incident_date->format('d/m/Y') }}
                    </p>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        <a href="{{ route('discipline.index') }}"
           class="block w-full py-2.5 rounded-xl text-center text-sm
                  font-medium text-gray-600 border border-gray-200
                  hover:bg-gray-50">
            ← Retour à la liste
        </a>
    </div>

</div>

@endsection