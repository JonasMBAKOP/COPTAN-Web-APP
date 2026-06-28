@extends('layouts.app')
@section('title', 'Emploi du temps')
@section('page-title', 'Emploi du temps - Classes')
@section('page-subtitle', 'Configuration des cours par périodes')

@section('content')
@if(!$activeYear)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center">
        <p class="font-bold text-amber-800">Aucune année scolaire active.</p>
        <p class="mt-1 text-sm text-amber-700">Activez ou créez une année scolaire avant de gérer les emplois du temps.</p>
    </div>
@else
<div x-data="timetableManager()" class="space-y-5">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('timetable.index') }}" class="grid gap-4 lg:grid-cols-[1fr_1.5fr_auto] lg:items-end">
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Section</label>
                <select id="sectionFilter" onchange="filterClasses(this.value)" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                    <option value="">Toutes les sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Classe</label>
                <select name="class_id" id="classFilter" onchange="this.form.submit()" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                    <option value="">Choisir une classe</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" data-section="{{ $class->level?->section_id }}" {{ (int) $selectedClassId === $class->id ? 'selected' : '' }}>
                            {{ $class->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-wrap gap-2">
                @can('manage-timetable')
                    <a href="{{ route('timetable.settings') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Configurer
                    </a>
                @endcan
                @if($selectedClass)
                    <a href="{{ route('timetable.print', $selectedClass) }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#1A3A6B] px-4 py-2.5 text-sm font-bold text-[#1A3A6B] transition hover:bg-[#1A3A6B] hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimer
                    </a>
                    @can('manage-timetable')
                        <button type="button" @click="openCreate()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1A5C2A] px-4 py-2.5 text-sm font-bold text-white transition hover:shadow-md">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M12 4v16m8-8H4"/></svg>
                            Ajouter
                        </button>
                    @endcan
                @endif
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
        <strong>Grille active :</strong> {{ $setting->period_duration_minutes }} min par période, {{ $setting->max_periods_per_day }} période(s) maximum par jour. Les pauses sont intégrées automatiquement dans les horaires.
    </div>

    @if(!$selectedClass)
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center shadow-sm">
            <p class="font-bold text-gray-700">Sélectionnez une classe pour gérer son emploi du temps.</p>
            <p class="mt-1 text-sm text-gray-500">Les périodes, pauses, conflits et volumes horaires appara�tront ici.</p>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-[1.3fr_repeat(4,1fr)]">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Classe sélectionnée</p>
                <h2 class="mt-1 text-xl font-black text-[#1A3A6B]">{{ $selectedClass->full_name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $selectedClass->level?->section?->name }} - {{ $selectedClass->academicYear?->label }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Cours</p><p class="mt-2 text-2xl font-black text-gray-900">{{ $summary['courses'] }}</p></div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Heures prévues</p><p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($summary['expected_hours'], 1, ',', ' ') }}h</p></div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Programmées</p><p class="mt-2 text-2xl font-black text-[#1A5C2A]">{{ number_format($summary['scheduled_hours'], 1, ',', ' ') }}h</p></div>
            <div class="rounded-2xl border {{ $conflicts->isNotEmpty() ? 'border-red-200 bg-red-50' : 'border-gray-100 bg-white' }} p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide {{ $conflicts->isNotEmpty() ? 'text-red-500' : 'text-gray-400' }}">Conflits</p><p class="mt-2 text-2xl font-black {{ $conflicts->isNotEmpty() ? 'text-red-700' : 'text-gray-900' }}">{{ $conflicts->count() }}</p></div>
        </div>

        @if($conflicts->isNotEmpty())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">{{ $conflicts->count() }} conflit(s) d�enseignant détecté(s). Les créneaux concern�s sont surlignés en rouge.</div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h3 class="font-black text-[#1A3A6B]">Emploi du temps de la classe</h3>
                <p class="text-xs text-gray-500">Chaque cellule correspond à une période configur�e pour l’école.</p>
            </div>
            <div class="overflow-x-auto">
                @include('timetable.partials.grid', [
                    'mode' => 'class',
                    'printable' => false,
                    'days' => $days,
                    'gridRows' => $gridRows,
                    'slots' => $slots,
                    'conflicts' => $conflicts,
                ])
            </div>
        </div>

        @can('manage-timetable')
        <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeModal()">
            <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl" @click.stop>
                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="class_group_id" value="{{ $selectedClass->id }}">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                        <div><h3 class="font-black text-[#1A3A6B]" x-text="editMode ? 'Modifier le cours' : 'Nouveau cours'"></h3><p class="text-xs text-gray-500">{{ $selectedClass->full_name }}</p></div>
                        <button type="button" @click="closeModal()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700">×</button>
                    </div>
                    <div class="space-y-4 px-6 py-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Matière</label>
                            <select name="class_subject_id" x-model="form.classSubjectId" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                                <option value="">Choisir une matière</option>
                                @foreach($selectedClass->classSubjects as $classSubject)
                                    <option value="{{ $classSubject->id }}">{{ $classSubject->subject->code }} - {{ $classSubject->subject->name_fr }} ({{ $classSubject->teacherAssignments->first()?->staff?->full_name ?? 'Non assigné' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Jour</label>
                                <select name="day_of_week" x-model="form.day" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                                    @foreach($days as $number => $name)<option value="{{ $number }}">{{ $name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Début</label>
                                <select name="period_index" x-model="form.periodIndex" required class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                                    @foreach($periodOptions as $period => $label)<option value="{{ $period }}">{{ $label }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Durée</label>
                                <input type="number" name="periods_count" x-model="form.periodsCount" min="1" max="8" required class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Salle</label>
                            <input type="text" name="room" x-model="form.room" maxlength="50" placeholder="Ex: Salle B2" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-gray-100 px-6 py-4">
                        <button type="button" x-show="editMode" @click="deleteSlot()" class="rounded-xl border border-red-200 px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50">Supprimer</button>
                        <div class="ml-auto flex gap-2"><button type="button" @click="closeModal()" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">Annuler</button><button type="submit" class="rounded-xl bg-[#1A5C2A] px-5 py-2 text-sm font-bold text-white">Enregistrer</button></div>
                    </div>
                </form>
                <form :action="deleteAction" method="POST" id="deleteForm" class="hidden">@csrf @method('DELETE')</form>
            </div>
        </div>
        @endcan
    @endif
</div>
@endif
@endsection

@push('scripts')
<script>
function filterClasses(sectionId) {
    const select = document.getElementById('classFilter');
    if (!select) return;
    Array.from(select.options).forEach((option) => {
        if (!option.value) return;
        option.hidden = sectionId ? option.dataset.section !== sectionId : false;
    });
}

function timetableManager() {
    return {
        showModal: false,
        editMode: false,
        formAction: @js(route('timetable.store')),
        deleteAction: '',
        form: { id: null, classSubjectId: '', day: 1, periodIndex: 1, periodsCount: 1, room: '' },
        openCreate() {
            this.editMode = false;
            this.formAction = @js(route('timetable.store'));
            this.deleteAction = '';
            this.form = { id: null, classSubjectId: '', day: 1, periodIndex: 1, periodsCount: 1, room: '' };
            this.showModal = true;
        },
        openEdit(id, classSubjectId, day, periodIndex, periodsCount, room) {
            this.editMode = true;
            this.formAction = `/timetable/${id}`;
            this.deleteAction = `/timetable/${id}`;
            this.form = { id, classSubjectId, day, periodIndex, periodsCount, room: room || '' };
            this.showModal = true;
        },
        closeModal() { this.showModal = false; },
        deleteSlot() {
            if (!confirm('Supprimer ce cours ?')) return;
            document.getElementById('deleteForm').action = this.deleteAction;
            document.getElementById('deleteForm').submit();
        },
    }
}
</script>
@endpush
