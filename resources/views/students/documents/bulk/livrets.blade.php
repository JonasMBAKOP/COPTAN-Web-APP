<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Livrets scolaires — impression groupée</title>
@include('students.documents.partials.base-styles')
<style>
@media print {
    .livret-page { page-break-after: always; }
    .livret-page:last-child { page-break-after: auto; }
}
.livret-table { width: 100%; border-collapse: collapse; font-size: 8px; margin-top: 8px; }
.livret-table th, .livret-table td { border: 1px solid #9CA3AF; padding: 3px 4px; text-align: center; }
.livret-table th { background: #1A3A6B; color: #fff; }
.livret-table td.subject { text-align: left; font-weight: 600; }
.livret-note { background: #FEF3C7; border: 1px solid #FCD34D; padding: 6px; font-size: 8px; color: #92400E; margin-bottom: 8px; }
</style>
</head>
<body>
@include('students.documents.partials.print-toolbar')

@php
    $seqCols = $sequences->isNotEmpty()
        ? $sequences
        : collect(range(1, 6))->map(fn ($n) => (object)['number' => $n, 'label' => "Séq. $n"]);
@endphp

@foreach($students as $student)
@php
    $enrollment = $student->printEnrollment;
    $subjects   = $subjectsByEnrollment[$student->id] ?? collect();
    $section    = $enrollment?->classGroup?->level?->section;
@endphp
<div class="page livret-page">
    @include('students.documents.partials.school-header', [
        'docTitle' => 'Livret scolaire',
        'docSubtitle' => $student->full_name . ' — ' . ($year?->label ?? ''),
    ])

    <div class="livret-note">Structure préparatoire — notes à renseigner via le module d'évaluation.</div>

    <div class="info-grid" style="margin-bottom:8px;">
        <div class="info-row"><span class="label">Matricule</span><span class="value">{{ $student->matricule }}</span></div>
        @if($enrollment)
        <div class="info-row"><span class="label">Classe</span><span class="value">{{ $enrollment->classGroup->full_name }}</span></div>
        @endif
    </div>

    <table class="livret-table">
        <thead>
            <tr>
                <th style="text-align:left;">Matière</th>
                <th>Coef.</th>
                @foreach($seqCols as $seq)
                <th>{{ $seq->label ?? ('S'.$seq->number) }}</th>
                @endforeach
                <th>Moy.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $cs)
            <tr>
                <td class="subject">{{ app(\App\Services\StudentDocumentService::class)->subjectLabel($cs, $section) }}</td>
                <td>{{ $cs->coefficient }}</td>
                @foreach($seqCols as $seq)<td>—</td>@endforeach
                <td>—</td>
            </tr>
            @empty
            <tr><td colspan="{{ 3 + $seqCols->count() }}">Aucune matière</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach
</body>
</html>
