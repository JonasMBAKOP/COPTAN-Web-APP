@extends('layouts.app')
@section('title', 'Mon emploi du temps')
@section('page-title', 'Emploi du temps professeur')
@section('page-subtitle', 'Consultation personnelle ou administrative par enseignant')
@push('styles')
<style>
.tt-shell { color:#1F2937; }
.tt-hero { background:linear-gradient(135deg,#F8FBFE 0%,#EEF6FC 100%); border:1px solid #DCE8F3; box-shadow:0 12px 32px rgba(26,58,107,.07); }
.tt-panel { background:#fff; border:1px solid #E5EDF5; box-shadow:0 10px 26px rgba(26,58,107,.055); }
.tt-panel-soft { background:#F8FBFE; border:1px solid #DCE8F3; }
.tt-field { border-color:#D6E2EE; background:#fff; transition:border-color .16s ease, box-shadow .16s ease; }
.tt-field:focus { border-color:#1A3A6B; box-shadow:0 0 0 3px rgba(26,58,107,.10); outline:none; }
.tt-btn-primary { background:#1A3A6B; color:#fff; box-shadow:0 8px 18px rgba(26,58,107,.16); }
.tt-btn-primary:hover { background:#122B50; }
.tt-btn-success { background:#1A5C2A; color:#fff; box-shadow:0 8px 18px rgba(26,92,42,.14); }
.tt-btn-success:hover { background:#12451F; }
.tt-btn-ghost { border:1px solid #DCE8F3; color:#334155; background:#fff; }
.tt-btn-ghost:hover { background:#F8FBFE; border-color:#C8D9EA; }
.tt-note { border:1px solid #CFE0EE; background:#F8FBFE; color:#1A3A6B; }
.tt-grid-wrap table th { background:#F8FBFE; color:#334155; }
.tt-grid-wrap table tbody tr:hover td { background-color:#FAFCFE; }
.tt-grid-wrap table td, .tt-grid-wrap table th { border-color:#E8EEF5; }
</style>
@endpush

@section('content')
@if(!$activeYear)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center">
        <p class="font-bold text-amber-800">Aucune année scolaire active.</p>
        <p class="mt-1 text-sm text-amber-700">L’emploi du temps enseignant sera disponible dès qu’une année sera active.</p>
    </div>
@else
<div class="tt-shell space-y-6">
    {{-- <section class="tt-hero overflow-hidden rounded-2xl p-5 lg:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <span class="inline-flex rounded-full bg-white px-3 py-1 text-[11px] font-black uppercase tracking-wide text-[#1A3A6B] shadow-sm">Consultation</span>
                <h2 class="mt-3 text-2xl font-black tracking-tight text-[#132F55]">Emploi du temps individuel</h2>
                <p class="mt-1 max-w-2xl text-sm font-medium text-slate-500">Vue enseignant avec classes, disciplines et periodes hebdomadaires synchronisees avec les emplois du temps des classes.</p>
            </div>
            <div class="rounded-2xl bg-white/80 p-4 text-right shadow-sm">
                <p class="text-[11px] font-black uppercase tracking-wide text-slate-400">Annee scolaire</p>
                <p class="mt-1 text-sm font-black text-[#1A5C2A]">{{ $activeYear?->label }}</p>
            </div>
        </div>
    </section> --}}
    @if($staffList->count() > 1)
        <div class="tt-panel rounded-2xl p-5">
            <form method="GET" action="{{ route('timetable.teacher') }}" class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Enseignant</label>
                    <select name="staff_id" onchange="this.form.submit()" class="tt-field w-full rounded-xl border px-3 py-2.5 text-sm">
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ (int) $selectedStaffId === $staff->id ? 'selected' : '' }}>{{ $staff->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="tt-btn-primary rounded-xl px-4 py-2.5 text-sm font-bold">Afficher</button>
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
            <div class="tt-panel rounded-2xl p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Enseignant</p>
                <h2 class="mt-1 text-xl font-black text-[#1A3A6B]">{{ $selectedStaff->full_name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $activeYear?->label }}</p>
                @if($selectedStaff)
                    <a href="{{ route('timetable.teacher.print', $selectedStaff) }}" target="_blank" class="tt-btn-ghost mt-3 inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-bold transition">Imprimer</a>
                @endif
            </div>
            <div class="tt-panel rounded-2xl p-5"><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Cours par semaine</p><p class="mt-2 text-2xl font-black text-gray-900">{{ $slots->count() }}</p></div>
            <div class="tt-panel rounded-2xl p-5"><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Périodes</p><p class="mt-2 text-2xl font-black text-[#1A5C2A]">{{ $slots->sum('periods_count') }}</p></div>
        </div>

        <div class="tt-note rounded-2xl p-4 text-sm font-semibold">
            <strong>Structure commune :</strong> {{ $setting->period_duration_minutes }} min par période, pauses intégrées automatiquement.
        </div>

        <div class="tt-panel overflow-hidden rounded-2xl">
            <div class="border-b border-gray-100 px-5 py-4">
                <h3 class="font-black text-[#1A3A6B]">Emploi du temps du professeur</h3>
                <p class="text-xs text-gray-500">La classe est affichée en premier, la matière juste en dessous.</p>
            </div>
            <div class="tt-grid-wrap overflow-x-auto">
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