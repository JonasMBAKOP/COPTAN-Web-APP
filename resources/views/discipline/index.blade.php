@extends('layouts.app')

@section('title', 'Discipline')
@section('page-title', 'Discipline')
@section('page-subtitle', 'Gestion des incidents disciplinaires et des sanctions')

@section('content')

{{-- ── KPI CARDS ──────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #1A3A6B;">
        <div class="p-2.5 rounded-xl" style="background:rgba(26,58,107,0.07);">
            <svg class="w-5 h-5" style="color:#1A3A6B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#1A3A6B;">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 font-semibold">Total incidents</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #C8A415;">
        <div class="p-2.5 rounded-xl" style="background:rgba(200,164,21,0.07);">
            <svg class="w-5 h-5" style="color:#C8A415;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#C8A415;">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 font-semibold">En attente</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #1A5C2A;">
        <div class="p-2.5 rounded-xl" style="background:rgba(26,92,42,0.07);">
            <svg class="w-5 h-5" style="color:#1A5C2A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black" style="color:#1A5C2A;">{{ $stats['resolved'] }}</p>
            <p class="text-xs text-gray-400 font-semibold">Résolus</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-4"
         style="border-left:4px solid #EF4444;">
        <div class="p-2.5 rounded-xl" style="background:rgba(239,68,68,0.07);">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <div>
            <p class="text-xl font-black text-red-500">{{ $stats['exclusions'] }}</p>
            <p class="text-xs text-gray-400 font-semibold">Exclusions / Renvois</p>
        </div>
    </div>

</div>

{{-- ── BARRE FILTRES & ACTION ──────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('discipline.index') }}"
          class="flex flex-wrap items-center gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Rechercher un élève..."
               class="flex-1 min-w-[180px] px-3 py-2 border border-gray-200 rounded-xl text-sm font-semibold
                      focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">

        <select name="type"
                class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-semibold
                       focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
            <option value="">Tous les types</option>
            @foreach($incidentTypes as $key => $label)
            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="status"
                class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-semibold
                       focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
            <option value="">Tous les statuts</option>
            <option value="pending"     {{ request('status') === 'pending'      ? 'selected' : '' }}>En attente</option>
            <option value="in_progress" {{ request('status') === 'in_progress'  ? 'selected' : '' }}>En cours</option>
            <option value="resolved"    {{ request('status') === 'resolved'     ? 'selected' : '' }}>Résolu</option>
        </select>

        <select name="sanction"
                class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-semibold
                       focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
            <option value="">Toutes sanctions</option>
            @foreach($sanctionTypes as $key => $label)
            <option value="{{ $key }}" {{ request('sanction') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="px-4 py-2 rounded-xl text-sm font-bold text-white hover:opacity-90 transition-all shadow-sm"
                style="background:#1A3A6B;">
            Filtrer
        </button>

        @if(request()->hasAny(['search', 'type', 'status', 'sanction']))
        <a href="{{ route('discipline.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold border border-gray-200 text-gray-500 hover:bg-gray-50 transition-all">
            Réinitialiser
        </a>
        @endif

        @can('manage-discipline')
        <a href="{{ route('discipline.create') }}"
           class="ml-auto inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white hover:opacity-90 transition-all shadow-sm"
           style="background:#1A5C2A;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Signaler un incident
        </a>
        @endcan
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── TABLEAU DES INCIDENTS ───────────────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                <h3 class="font-black text-sm" style="color:#1A3A6B;">Incidents disciplinaires</h3>
                <span class="text-xs text-gray-400 font-semibold">
                    {{ $incidents->total() }} incident(s)
                </span>
            </div>

            @if($incidents->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-400 font-semibold">Aucun incident enregistré.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 text-3xs uppercase font-black tracking-wider"
                            style="background:#F8FAFC;">
                            <th class="text-left px-5 py-3.5">Élève</th>
                            <th class="text-center px-4 py-3.5">Date</th>
                            <th class="text-left px-4 py-3.5">Type</th>
                            <th class="text-left px-4 py-3.5">Sanction</th>
                            <th class="text-center px-4 py-3.5">Statut</th>
                            <th class="text-center px-5 py-3.5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($incidents as $inc)
                        @php
                            $statusColors = [
                                'pending'     => ['bg' => '#FEF3C7', 'text' => '#92400E', 'label' => 'En attente'],
                                'in_progress' => ['bg' => '#DBEAFE', 'text' => '#1D4ED8', 'label' => 'En cours'],
                                'resolved'    => ['bg' => 'rgba(26,92,42,0.1)', 'text' => '#1A5C2A', 'label' => 'Résolu'],
                            ];
                            $sc = $statusColors[$inc->status] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280', 'label' => $inc->status];
                            $sanctionSevere = in_array($inc->sanction_type, ['temporary_suspension', 'definitive_exclusion']);
                        @endphp
                        <tr class="hover:bg-gray-50/40 transition-colors font-semibold text-gray-700">
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-800">
                                    {{ $inc->studentEnrollment->student->full_name }}
                                </p>
                                <p class="text-3xs text-gray-400">
                                    {{ $inc->studentEnrollment->classGroup->full_name }}
                                </p>
                            </td>
                            <td class="px-4 py-4 text-center text-gray-600">
                                {{ $inc->incident_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="font-semibold text-gray-700">
                                    {{ $incidentTypes[$inc->incident_type] ?? $inc->incident_type }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($inc->sanction_type)
                                <span class="font-bold {{ $sanctionSevere ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $sanctionTypes[$inc->sanction_type] ?? $inc->sanction_type }}
                                    @if($inc->sanction_duration_days)
                                    <span class="text-gray-400 font-medium">({{ $inc->sanction_duration_days }}j)</span>
                                    @endif
                                </span>
                                @else
                                <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-3xs font-bold"
                                      style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }};">
                                    {{ $sc['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('discipline.show', $inc->id) }}"
                                       title="Voir le détail"
                                       class="p-1.5 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('manage-discipline')
                                    <a href="{{ route('discipline.edit', $inc->id) }}"
                                       title="Modifier"
                                       class="p-1.5 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('discipline.destroy', $inc->id) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Supprimer cet incident ?')"
                                                title="Supprimer"
                                                class="p-1.5 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($incidents->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $incidents->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>

    {{-- ── COLONNE DROITE : Récidivistes + Légende ────────────────────────── --}}
    <div class="space-y-6">

        {{-- Élèves récidivistes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50">
                <h3 class="font-black text-sm" style="color:#1A3A6B;">Élèves récidivistes</h3>
                <p class="text-xs text-gray-400 mt-0.5">Plus de 1 incident</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recidivists as $idx => $e)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-xs font-black text-gray-300 w-4">{{ $idx + 1 }}</span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-800 truncate">
                                {{ $e->student->full_name }}
                            </p>
                            <p class="text-3xs text-gray-400">
                                {{ $e->classGroup->full_name }}
                            </p>
                        </div>
                    </div>
                    <span class="text-sm font-black text-red-500 flex-shrink-0 ml-2">
                        {{ $e->discipline_incidents_count }}x
                    </span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 italic text-sm">
                    Aucun récidiviste.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Légende statuts --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-black text-sm mb-3" style="color:#1A3A6B;">Légende des sanctions</h3>
            <div class="space-y-2">
                @foreach($sanctionTypes as $key => $label)
                @php
                    $color = match($key) {
                        'observation'          => '#9CA3AF',
                        'warning'              => '#C8A415',
                        'detention'            => '#F59E0B',
                        'temporary_suspension' => '#EF4444',
                        'definitive_exclusion' => '#7F1D1D',
                        default                => '#6B7280',
                    };
                @endphp
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                          style="background:{{ $color }};"></span>
                    <span class="text-xs font-semibold text-gray-600">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
