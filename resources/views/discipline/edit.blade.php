@extends('layouts.app')

@section('title', 'Modifier un incident')
@section('page-title', 'Modifier un incident disciplinaire')
@section('page-subtitle', $incident->studentEnrollment->student->full_name . ' · ' . $incident->incident_date->format('d/m/Y'))

@section('content')

<div x-data="disciplineEditForm()">

    <form method="POST" action="{{ route('discipline.update', $incident) }}" class="pb-24">
        @csrf @method('PATCH')

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-[10px]"
                      style="background:#1A3A6B;">1</span>
                Élève
                <span class="text-gray-300">→</span>
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-[10px]"
                      style="background:#2D6FD4;">2</span>
                Incident
                <span class="text-gray-300">→</span>
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-[10px]"
                      style="background:#E87722;">3</span>
                Sanction
            </div>
            <a href="{{ route('discipline.show', $incident) }}"
               class="text-sm font-semibold text-gray-500 hover:text-gray-700">
                ← Retour au dossier
            </a>
        </div>

        @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-bold mb-1">Veuillez corriger les erreurs suivantes :</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            <div class="xl:col-span-2 space-y-5">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3"
                         style="background:linear-gradient(90deg,#F8FAFC,#FFFFFF);">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-sm"
                             style="background:#1A3A6B;">1</div>
                        <div>
                            <h3 class="text-sm font-black" style="color:#1A3A6B;">Élève concerné</h3>
                            <p class="text-xs text-gray-400">Cette information ne peut pas être modifiée ici.</p>
                        </div>
                    </div>

                    <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <input type="hidden" name="student_enrollment_id" value="{{ $incident->student_enrollment_id }}">

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Section</label>
                            <input type="text" value="{{ $incident->studentEnrollment->classGroup->level->section->name ?? '—' }}"
                                   disabled
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-700 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Classe</label>
                            <input type="text" value="{{ $incident->studentEnrollment->classGroup->full_name }}"
                                   disabled
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-700 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Élève</label>
                            <input type="text" value="{{ $incident->studentEnrollment->student->full_name }}"
                                   disabled
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-700 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="mx-5 mb-5 px-4 py-3 rounded-xl flex items-center gap-3"
                         style="background:#EBF3FB;">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                             style="background:#1A3A6B;">
                            {{ strtoupper(substr($incident->studentEnrollment->student->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-800">{{ $incident->studentEnrollment->student->full_name }}</p>
                            <p class="text-xs text-gray-500">Élève verrouillé pour cette modification</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3"
                         style="background:linear-gradient(90deg,#F8FAFC,#FFFFFF);">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-sm"
                             style="background:#2D6FD4;">2</div>
                        <div>
                            <h3 class="text-sm font-black" style="color:#1A3A6B;">Détails de l'incident</h3>
                            <p class="text-xs text-gray-400">Modifiez la date, le type, le lieu et la description</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Date <span class="text-red-500">*</span></label>
                                <input type="date" name="incident_date"
                                       value="{{ old('incident_date', $incident->incident_date->format('Y-m-d')) }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Heure</label>
                                <input type="time" name="incident_time"
                                       value="{{ old('incident_time', $incident->incident_time) }}"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Lieu</label>
                                <select name="location"
                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="">— Non précisé —</option>
                                    @foreach(\App\Models\DisciplineIncident::LOCATIONS as $value => $label)
                                    <option value="{{ $value }}" {{ old('location', $incident->location) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Type d'incident <span class="text-red-500">*</span></label>
                            <input type="hidden" name="incident_type" x-model="incidentType">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                                @foreach(\App\Models\DisciplineIncident::INCIDENT_TYPES as $value => $label)
                                <button type="button"
                                        @click="incidentType = '{{ $value }}'"
                                        :class="incidentType === '{{ $value }}'
                                            ? 'border-blue-600 bg-blue-50 shadow-sm'
                                            : 'border-gray-200 bg-white hover:border-gray-300'"
                                        class="px-3 py-3 rounded-xl border text-left transition-all">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg"
                                          :class="incidentType === '{{ $value }}'
                                              ? 'bg-blue-100 text-blue-700'
                                              : 'bg-gray-100 text-gray-500'">
                                        @include('discipline.partials.incident-type-icon', ['type' => $value])
                                    </span>
                                    <p class="text-xs font-bold text-gray-800 mt-2 leading-tight">{{ $label }}</p>
                                </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Description des faits <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5"
                                      class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none">{{ old('description', $incident->description) }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Soyez factuel — cette description sera enregistrée dans le dossier.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden xl:sticky xl:top-4">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3"
                         style="background:linear-gradient(90deg,#FEF3EA,#FFFFFF);">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-sm"
                             style="background:#E87722;">3</div>
                        <div>
                            <h3 class="text-sm font-black" style="color:#1A3A6B;">Sanction</h3>
                            <p class="text-xs text-gray-400">Modifiez la sanction et la convocation</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Type de sanction</label>
                            <select name="sanction_type" x-model="sanctionType"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-100">
                                <option value="">Observation (par défaut)</option>
                                @foreach(\App\Models\DisciplineIncident::SANCTIONS as $value => $label)
                                @if($value !== 'observation')
                                <option value="{{ $value }}" {{ old('sanction_type', $incident->sanction_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div x-show="['detention','temporary_suspension'].includes(sanctionType)" x-cloak>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Durée (jours)</label>
                            <input type="number" name="sanction_duration_days"
                                   value="{{ old('sanction_duration_days', $incident->sanction_duration_days) }}"
                                   min="1" max="30"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none">
                        </div>

                        <div class="rounded-xl border border-gray-100 p-4 space-y-3"
                             style="background:#F8FAFC;">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="parent_convoked" value="1"
                                       x-model="parentConvoked"
                                       class="mt-0.5 w-4 h-4 rounded" style="accent-color:#E87722;"
                                       {{ old('parent_convoked', $incident->parent_convoked) ? 'checked' : '' }}>
                                <span>
                                    <span class="text-sm font-bold text-gray-800 block">Convoquer le parent / tuteur</span>
                                    <span class="text-xs text-gray-400">Cochez si une convocation est demandée</span>
                                </span>
                            </label>
                            <div x-show="parentConvoked" x-cloak>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Date de convocation</label>
                                <input type="date" name="convocation_date"
                                       value="{{ old('convocation_date', $incident->convocation_date?->format('Y-m-d')) }}"
                                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none">
                            </div>
                        </div>

                        <div class="rounded-xl px-4 py-3 text-xs text-gray-500 leading-relaxed"
                             style="background:#EBF3FB;">
                            L'incident restera <strong>Ouvert</strong> tant qu'il n'est pas clôturé depuis la fiche de détail.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 md:left-64 right-0 z-30 bg-white border-t border-gray-200 shadow-xl px-5 py-3.5 flex items-center justify-between gap-4">
            <p class="text-sm text-gray-500 hidden sm:block">Les champs marqués <span class="text-red-500">*</span> sont obligatoires</p>
            <div class="flex gap-2 ml-auto">
                <a href="{{ route('discipline.show', $incident) }}"
                   class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-white text-sm font-bold flex items-center gap-2 hover:shadow-md transition-all"
                        style="background-color:#E87722;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function disciplineEditForm() {
    return {
        incidentType: @json(old('incident_type', $incident->incident_type)),
        sanctionType: @json(old('sanction_type', $incident->sanction_type ?? '')),
        parentConvoked: {{ old('parent_convoked', $incident->parent_convoked) ? 'true' : 'false' }},
    };
}
</script>
@endpush
