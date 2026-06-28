@extends('layouts.app')
@section('title', 'Mon emploi du temps')
@section('page-title', 'Emploi du temps professeur')
@section('page-subtitle', 'Consultation personnelle ou administrative par enseignant')

@section('content')
@if(!$activeYear)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center">
        <p class="font-bold text-amber-800">Aucune année scolaire active.</p>
        <p class="mt-1 text-sm text-amber-700">L’emploi du temps enseignant sera disponible dès qu’une année sera active.</p>
    </div>
@else
<div class="space-y-5">
    @if($staffList->count() > 1)
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('timetable.teacher') }}" class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Enseignant</label>
                    <select name="staff_id" onchange="this.form.submit()" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ (int) $selectedStaffId === $staff->id ? 'selected' : '' }}>{{ $staff->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-[#1A3A6B] px-4 py-2.5 text-sm font-bold text-white">Afficher</button>
            </form>
        </div>
    @endif

    @if(!$selectedStaff)
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center shadow-sm">
            <p class="font-bold text-gray-700">Aucun dossier enseignant associé à votre compte.</p>
            <p class="mt-1 text-sm text-gray-500">Contactez l’administration pour rattacher votre compte à un personnel enseignant.</p>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-[1.4fr_1fr_1fr]">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Enseignant</p>
                <h2 class="mt-1 text-xl font-black text-[#1A3A6B]">{{ $selectedStaff->full_name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $activeYear?->label }}</p>
                @if($selectedStaff)
                    <a href="{{ route('timetable.teacher.print', $selectedStaff) }}" target="_blank" class="mt-3 inline-flex items-center gap-2 rounded-xl border border-[#1A3A6B] px-3 py-2 text-xs font-bold text-[#1A3A6B] hover:bg-[#1A3A6B] hover:text-white">Imprimer</a>
                @endif
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Cours par semaine</p><p class="mt-2 text-2xl font-black text-gray-900">{{ $slots->count() }}</p></div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Périodes</p><p class="mt-2 text-2xl font-black text-[#1A5C2A]">{{ $slots->sum('periods_count') }}</p></div>
        </div>

        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
            <strong>Structure commune :</strong> {{ $setting->period_duration_minutes }} min par période, pauses intégrées automatiquement.
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h3 class="font-black text-[#1A3A6B]">Emploi du temps du professeur</h3>
                <p class="text-xs text-gray-500">La classe est affichée en premier, la matière juste en dessous.</p>
            </div>
            <div class="overflow-x-auto">
                @include('timetable.partials.grid', [
                    'mode' => 'teacher',
                    'printable' => false,
                    'days' => $days,
                    'gridRows' => $gridRows,
                    'slots' => $slots,
                    'teacherSubjectCount' => $teacherSubjectCount,
                ])
            </div>
        </div>
    @endif
</div>
@endif
@endsection