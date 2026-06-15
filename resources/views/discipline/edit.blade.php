@extends('layouts.app')

@section('title', 'Modifier un incident')
@section('page-title', 'Modifier l\'incident disciplinaire')
@section('page-subtitle', $incident->studentEnrollment->student->full_name . ' · ' . $incident->incident_date->format('d/m/Y'))

@section('content')

<div class="max-w-3xl mx-auto">

    <a href="{{ route('discipline.show', $incident->id) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour au dossier
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50"
             style="background:linear-gradient(135deg,rgba(200,164,21,0.05),rgba(200,164,21,0.1));">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-sm"
                     style="background:#1A3A6B;">
                    {{ strtoupper(substr($incident->studentEnrollment->student->first_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-black text-base" style="color:#1A3A6B;">
                        {{ $incident->studentEnrollment->student->full_name }}
                    </h2>
                    <p class="text-xs text-gray-500">
                        {{ $incident->studentEnrollment->classGroup->full_name }}
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('discipline.update', $incident->id) }}" class="p-6 space-y-5">
            @csrf @method('PATCH')

            {{-- Date & Heure --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Date de l'incident *
                    </label>
                    <input type="date" name="incident_date" required
                           value="{{ old('incident_date', $incident->incident_date->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                  focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Heure
                    </label>
                    <input type="time" name="incident_time"
                           value="{{ old('incident_time', $incident->incident_time) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                  focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
                </div>
            </div>

            {{-- Type & Lieu --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Type d'incident *
                    </label>
                    <select name="incident_type" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                   focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
                        @foreach($incidentTypes as $key => $label)
                        <option value="{{ $key }}"
                                {{ old('incident_type', $incident->incident_type) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Lieu
                    </label>
                    <input type="text" name="location"
                           value="{{ old('location', $incident->location) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                  focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Description des faits *
                </label>
                <textarea name="description" required rows="4"
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none
                                 transition-all resize-none">{{ old('description', $incident->description) }}</textarea>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-xs font-black text-gray-500 uppercase tracking-wide mb-4">Sanction</h3>
            </div>

            {{-- Sanction & Durée --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Sanction
                    </label>
                    <select name="sanction_type"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                   focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
                        <option value="">— Aucune —</option>
                        @foreach($sanctionTypes as $key => $label)
                        <option value="{{ $key }}"
                                {{ old('sanction_type', $incident->sanction_type) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Durée (jours)
                    </label>
                    <input type="number" name="sanction_duration_days"
                           value="{{ old('sanction_duration_days', $incident->sanction_duration_days) }}"
                           min="1" max="365"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                  focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
                </div>
            </div>

            {{-- Convocation parents --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <input type="checkbox" name="parent_convoked" id="parent_convoked" value="1"
                           {{ old('parent_convoked', $incident->parent_convoked) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                    <label for="parent_convoked" class="text-sm font-semibold text-gray-700 cursor-pointer">
                        Parents convoqués
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Date de convocation
                    </label>
                    <input type="date" name="convocation_date"
                           value="{{ old('convocation_date', $incident->convocation_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold
                                  focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition-all">
                </div>
            </div>

            {{-- Statut --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Statut du dossier *
                </label>
                <div class="flex gap-3">
                    @foreach(['pending' => 'En attente', 'in_progress' => 'En cours', 'resolved' => 'Résolu'] as $val => $lbl)
                    <label class="flex items-center gap-2 flex-1 p-3 rounded-xl border cursor-pointer transition-all
                                  {{ old('status', $incident->status) === $val ? 'border-blue-300 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="radio" name="status" value="{{ $val }}"
                               {{ old('status', $incident->status) === $val ? 'checked' : '' }}
                               class="text-blue-600 focus:ring-blue-200">
                        <span class="text-xs font-bold text-gray-700">{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('discipline.show', $incident->id) }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold border border-gray-200 text-gray-600
                          hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white hover:opacity-90 transition-all shadow-sm"
                        style="background:#1A3A6B;">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
