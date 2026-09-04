@extends('layouts.app')

@section('title', 'Matières de ' . $enrollment->student->full_name)
@section('page-title', 'Matières choisies')
@section('page-subtitle', $enrollment->student->full_name . ' · ' . $enrollment->classGroup->full_name)

@section('content')
<div class="mx-auto max-w-6xl space-y-5">
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('students.show', $enrollment->student_id) }}"
               class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50"
               aria-label="Retour à la fiche élève">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <p class="text-sm font-bold text-slate-900">Choix des matières</p>
                <p class="text-xs text-slate-500">{{ $enrollment->classGroup->level->section->name }} · {{ $enrollment->academicYear->label }}</p>
            </div>
        </div>
        <div class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800">
            Minimum requis : {{ $minimumSubjects }} matières
        </div>
    </div>

    <form method="POST" action="{{ route('students.enrollments.subjects.update', $enrollment) }}" x-data="{ count: {{ count($selectedIds) }} }">
        @csrf
        @method('PUT')
        <div class="space-y-5">
            @foreach($subjectsByCategory as $category => $classSubjects)
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-4">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-wide text-[#1A3A6B]">{{ $category }}</h2>
                            <p class="mt-1 text-xs text-slate-500">{{ $classSubjects->count() }} matière(s) proposée(s)</p>
                        </div>
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                    </div>
                    <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($classSubjects as $classSubject)
                            @php $subject = $classSubject->subject; @endphp
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-[#1A3A6B] hover:bg-slate-50">
                                <input type="checkbox" name="class_subject_ids[]" value="{{ $classSubject->id }}"
                                       @checked(in_array($classSubject->id, $selectedIds))
                                       @change="count = $el.closest('form').querySelectorAll('input[type=checkbox]:checked').length"
                                       class="mt-1 h-4 w-4 rounded border-slate-300 text-[#1A3A6B] focus:ring-[#1A3A6B]">
                                <span class="min-w-0">
                                    <span class="block text-sm font-bold text-slate-800">{{ $subject?->name_fr ?: $subject?->name_en }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $subject?->code ?: 'Sans code' }} · Coef. {{ $classSubject->coefficient }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <div class="sticky bottom-4 mt-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600"><strong x-text="count"></strong> matière(s) sélectionnée(s)</p>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1A3A6B] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#132d54]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Enregistrer les matières
            </button>
        </div>
    </form>
</div>
@endsection
