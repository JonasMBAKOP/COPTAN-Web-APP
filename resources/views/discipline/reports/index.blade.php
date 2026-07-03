@extends('layouts.app')

@section('title', 'Rapport d’incidents')
@section('page-title', 'Rapport d’incidents')
@section('page-subtitle', 'Analyse disciplinaire filtrée')

@section('content')
<div class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
    <form method="GET" action="{{ route('discipline.reports') }}" class="space-y-5">
        <div>
            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Type de rapport</label>
            <div class="flex flex-wrap gap-2 rounded-2xl border border-gray-200 bg-white p-1">
                @php $reportTypes = ['journalier'=>'Journalier','hebdomadaire'=>'Hebdomadaire','mensuel'=>'Mensuel','annuel'=>'Annuel','entre-2-dates'=>'Entre 2 dates']; @endphp
                @foreach($reportTypes as $val => $lbl)
                    <button type="button"
                            onclick="setDisciplineReportType('{{ $val }}')"
                            class="rounded-xl px-4 py-2 text-sm font-bold transition-all {{ $type === $val ? 'bg-[#1A3A6B] text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}"
                            data-type="{{ $val }}">
                        {{ $lbl }}
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="type" id="discipline-report-type" value="{{ $type }}">
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Année scolaire</label>
                <select name="year_id" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm bg-white">
                    @foreach(App\Models\AcademicYear::orderByDesc('start_date')->get() as $year)
                        <option value="{{ $year->id }}" {{ $selectedYear?->id == $year->id ? 'selected' : '' }}>{{ $year->label }}</option>
                    @endforeach
                </select>
            </div>

            <div id="discipline-date-filter" style="display: {{ $type === 'journalier' ? 'block' : 'none' }};">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Date</label>
                <input type="date" name="date" value="{{ old('date', $date) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
            </div>

            <div id="discipline-week-filter" style="display: {{ $type === 'hebdomadaire' ? 'block' : 'none' }};">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Semaine</label>
                <input type="week" name="week" value="{{ old('week', $week) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
            </div>

            <div id="discipline-month-filter" style="display: {{ $type === 'mensuel' ? 'block' : 'none' }};">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Mois</label>
                <select name="month" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm bg-white">
                    @foreach(range(1,12) as $num)
                        <option value="{{ $num }}" {{ (int)$month === $num ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($num)->locale('fr')->monthName }}</option>
                    @endforeach
                </select>
            </div>

            <div id="discipline-range-filter" style="display: {{ $type === 'entre-2-dates' ? 'grid' : 'none' }}; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem;">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Début</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $startDate) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Fin</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $endDate) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-xl bg-[#1A3A6B] px-5 py-2.5 text-sm font-bold text-white">Générer</button>
        </div>
    </form>
</div>

<div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-black text-[#1A3A6B]">Rapport {{ ['journalier'=>'Journalier','hebdomadaire'=>'Hebdomadaire','mensuel'=>'Mensuel','annuel'=>'Annuel','entre-2-dates'=>'Entre 2 dates'][$type] ?? 'Discipline' }}</h3>
            <p class="text-sm text-gray-500">{{ $incidents->count() }} incident(s) trouvé(s)</p>
        </div>
        <a href="{{ route('discipline.print', $incidents->first()) }}" target="_blank" class="rounded-xl border border-[#E87722] px-4 py-2 text-sm font-bold text-[#E87722] {{ $incidents->isEmpty() ? 'pointer-events-none opacity-50' : '' }}">Imprimer</a>
    </div>

    @if($incidents->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-500">Aucun incident trouvé pour cette période.</div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Élève</th>
                        <th class="px-3 py-2">Classe</th>
                        <th class="px-3 py-2">Type</th>
                        <th class="px-3 py-2">Sanction</th>
                        <th class="px-3 py-2">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($incidents as $incident)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $incident->incident_date->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">{{ $incident->studentEnrollment?->student?->full_name }}</td>
                            <td class="px-3 py-2">{{ $incident->studentEnrollment?->classGroup?->full_name }}</td>
                            <td class="px-3 py-2">{{ $incident->incident_type_label }}</td>
                            <td class="px-3 py-2">{{ $incident->sanction_label }}</td>
                            <td class="px-3 py-2">{{ $incident->status_label }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
function setDisciplineReportType(value) {
    document.getElementById('discipline-report-type').value = value;
    const date = document.getElementById('discipline-date-filter');
    const week = document.getElementById('discipline-week-filter');
    const month = document.getElementById('discipline-month-filter');
    const range = document.getElementById('discipline-range-filter');
    [date, week, month, range].forEach((el) => el.style.display = 'none');
    if (value === 'journalier') date.style.display = 'block';
    if (value === 'hebdomadaire') week.style.display = 'block';
    if (value === 'mensuel') month.style.display = 'block';
    if (value === 'entre-2-dates') range.style.display = 'grid';
}
</script>
@endsection
