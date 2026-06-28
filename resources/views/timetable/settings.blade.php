@extends('layouts.app')
@section('title', 'Configuration emploi du temps')
@section('page-title', 'Configuration de l’emploi du temps')
@section('page-subtitle', 'Structure générale des périodes, horaires journaliers et pauses')

@section('content')
<form method="POST" action="{{ route('timetable.settings.update') }}" class="space-y-5">
    @csrf

    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div>
            <h2 class="text-lg font-black text-[#1A3A6B]">Structure de la grille</h2>
            <p class="mt-1 text-sm text-gray-500">Cette configuration sert aux emplois du temps des classes et des enseignants.</p>
        </div>
        <a href="{{ route('timetable.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">Retour</a>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
            Vérifiez les champs du formulaire avant d’enregistrer.
        </div>
    @endif

    <div class="grid gap-5 xl:grid-cols-[0.9fr_1.5fr]">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="font-black text-[#1A3A6B]">Périodes</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Durée d’une période</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="period_duration_minutes" min="30" max="120" value="{{ old('period_duration_minutes', $setting->period_duration_minutes) }}" class="w-32 rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                        <span class="text-sm font-semibold text-gray-500">minutes</span>
                    </div>
                    @error('period_duration_minutes')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Nombre maximal de périodes par jour</label>
                    <input type="number" name="max_periods_per_day" min="1" max="14" value="{{ old('max_periods_per_day', $setting->max_periods_per_day) }}" class="w-32 rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
                    <p class="mt-2 text-xs text-gray-500">Pour une demi-journée, réduisez seulement le nombre de périodes actives du jour concerné.</p>
                    @error('max_periods_per_day')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="font-black text-[#1A3A6B]">Début des cours par jour</h3>
            <p class="mt-1 text-sm text-gray-500">L’heure de fin est calculée automatiquement selon les périodes actives et les pauses.</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($days as $dayNumber => $dayName)
                    @php $config = $dayConfigs[$dayNumber]; @endphp
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <p class="mb-3 text-sm font-black text-gray-800">{{ $dayName }}</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-gray-500">Début</label>
                                <input type="time" name="days[{{ $dayNumber }}][start_time]" value="{{ old("days.$dayNumber.start_time", $config['start_time']) }}" class="w-full rounded-lg border border-gray-200 px-2 py-2 text-sm focus:border-[#1A3A6B] focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-gray-500">Périodes</label>
                                <input type="number" name="days[{{ $dayNumber }}][active_periods]" min="0" max="14" value="{{ old("days.$dayNumber.active_periods", $config['active_periods']) }}" class="w-full rounded-lg border border-gray-200 px-2 py-2 text-sm focus:border-[#1A3A6B] focus:outline-none">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm" x-data="{ breakCount: {{ max(1, old('break_count', count($breaks) ?: 1)) }} }">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h3 class="font-black text-[#1A3A6B]">Pauses</h3>
                <p class="mt-1 text-sm text-gray-500">Renseignez l’heure de début et la durée; l’heure de fin est calculée automatiquement.</p>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-gray-500">Nombre de pauses</label>
                <input type="number" name="break_count" min="0" max="5" x-model.number="breakCount" class="w-28 rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#1A3A6B] focus:outline-none">
            </div>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            @for($i = 0; $i < 5; $i++)
                @php $break = $breaks[$i] ?? ['start_time' => '', 'duration_minutes' => '']; @endphp
                <div x-show="{{ $i }} < breakCount" class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="mb-3 text-sm font-black text-gray-800">Pause {{ $i + 1 }}</p>
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-gray-500">Début</label>
                            <input type="time" name="breaks[{{ $i }}][start_time]" value="{{ old("breaks.$i.start_time", $break['start_time']) }}" class="w-full rounded-lg border border-gray-200 px-2 py-2 text-sm focus:border-[#1A3A6B] focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-gray-500">Durée</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="breaks[{{ $i }}][duration_minutes]" min="5" max="120" value="{{ old("breaks.$i.duration_minutes", $break['duration_minutes']) }}" class="w-full rounded-lg border border-gray-200 px-2 py-2 text-sm focus:border-[#1A3A6B] focus:outline-none">
                                <span class="text-xs font-semibold text-gray-500">min</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <div class="sticky bottom-0 flex justify-end border-t border-gray-100 bg-gray-50/95 py-4 backdrop-blur">
        <button type="submit" class="rounded-xl bg-[#1A5C2A] px-6 py-3 text-sm font-black text-white shadow-sm hover:shadow-md">Enregistrer la configuration</button>
    </div>
</form>
@endsection