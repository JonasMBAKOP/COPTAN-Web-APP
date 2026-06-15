@extends('layouts.app')

@section('title', 'Incident — ' . $incident->studentEnrollment->student->full_name)
@section('page-title', 'Dossier disciplinaire')
@section('page-subtitle', $incident->studentEnrollment->student->full_name . ' · ' . $incident->incident_date->format('d/m/Y'))

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- Navigation --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('discipline.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux incidents
        </a>
        @can('manage-discipline')
        <div class="flex items-center gap-2">
            <a href="{{ route('discipline.edit', $incident->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            <form method="POST" action="{{ route('discipline.destroy', $incident->id) }}" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Supprimer définitivement ce dossier ?')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </form>
        </div>
        @endcan
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── COLONNE PRINCIPALE ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Carte élève --}}
            @php
                $e = $incident->studentEnrollment;
                $statusColors = [
                    'pending'     => ['bg' => '#FEF3C7', 'text' => '#92400E', 'label' => 'En attente'],
                    'in_progress' => ['bg' => '#DBEAFE', 'text' => '#1D4ED8', 'label' => 'En cours'],
                    'resolved'    => ['bg' => 'rgba(26,92,42,0.1)', 'text' => '#1A5C2A', 'label' => 'Résolu'],
                ];
                $sc = $statusColors[$incident->status] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280', 'label' => $incident->status];
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-white text-lg flex-shrink-0"
                     style="background:#1A3A6B;">
                    {{ strtoupper(substr($e->student->first_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-black text-base text-gray-900">
                        {{ $e->student->full_name }}
                    </h2>
                    <p class="text-sm text-gray-500 font-medium">
                        {{ $e->classGroup->full_name }}
                        · {{ $e->classGroup->level?->section?->name }}
                        · Mat. {{ $e->student->matricule }}
                    </p>
                </div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black flex-shrink-0"
                      style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }};">
                    {{ $sc['label'] }}
                </span>
            </div>

            {{-- Détail de l'incident --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50"
                     style="background:linear-gradient(135deg,rgba(26,58,107,0.03),rgba(26,58,107,0.07));">
                    <h3 class="font-black text-sm" style="color:#1A3A6B;">Détail de l'incident</h3>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Date &amp; Heure</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ $incident->incident_date->format('d/m/Y') }}
                            @if($incident->incident_time) à {{ $incident->incident_time }} @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Lieu</p>
                        <p class="text-sm font-bold text-gray-800">{{ $incident->location ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Type d'incident</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ $incidentTypes[$incident->incident_type] ?? $incident->incident_type }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Rapporté par</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ $incident->reportedBy?->name ?? '—' }}
                        </p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Description des faits</p>
                        <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-700 font-semibold leading-relaxed border border-gray-100">
                            {{ $incident->description }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sanction & Suite --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <h3 class="font-black text-sm" style="color:#1A3A6B;">Sanction &amp; Suite disciplinaire</h3>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Sanction prononcée</p>
                        @if($incident->sanction_type)
                        <p class="text-sm font-black"
                           style="color:{{ in_array($incident->sanction_type, ['temporary_suspension', 'definitive_exclusion']) ? '#EF4444' : '#1A3A6B' }};">
                            {{ $sanctionTypes[$incident->sanction_type] ?? $incident->sanction_type }}
                            @if($incident->sanction_duration_days)
                            <span class="text-xs font-semibold text-gray-500">
                                ({{ $incident->sanction_duration_days }} jour(s))
                            </span>
                            @endif
                        </p>
                        @else
                        <p class="text-sm text-gray-400 italic">Aucune sanction prononcée</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Décision prise par</p>
                        <p class="text-sm font-bold text-gray-800">{{ $incident->decidedBy?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Parents convoqués</p>
                        <p class="text-sm font-bold {{ $incident->parent_convoked ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $incident->parent_convoked ? '✓ Oui' : 'Non' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Date de convocation</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ $incident->convocation_date?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── COLONNE DROITE : Historique ─────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Historique élève --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <h3 class="font-black text-sm" style="color:#1A3A6B;">Historique de l'élève</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Autres incidents passés</p>
                </div>
                @forelse($history as $h)
                <div class="px-5 py-3 border-b border-gray-50 last:border-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-xs font-bold text-gray-700">
                                {{ $incidentTypes[$h->incident_type] ?? $h->incident_type }}
                            </p>
                            <p class="text-3xs text-gray-400 mt-0.5">
                                {{ $h->incident_date->format('d/m/Y') }}
                                @if($h->reportedBy) · {{ $h->reportedBy->name }} @endif
                            </p>
                        </div>
                        @if($h->sanction_type)
                        <span class="text-3xs font-bold text-red-500 flex-shrink-0">
                            {{ $sanctionTypes[$h->sanction_type] ?? $h->sanction_type }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-5 py-6 text-center text-gray-400 italic text-sm">
                    Aucun antécédent disciplinaire.
                </div>
                @endforelse

                <div class="px-5 py-3">
                    <a href="{{ route('discipline.create', ['enrollment_id' => $incident->student_enrollment_id]) }}"
                       class="block text-center text-xs font-bold border border-gray-200 rounded-xl py-2
                              text-gray-500 hover:bg-gray-50 transition-all">
                        + Signaler un nouvel incident
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
