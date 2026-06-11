<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiches de renseignement — impression groupée</title>
@include('students.documents.partials.base-styles')
@include('students.documents.partials.fiche-styles')
</head>
<body>
@include('students.documents.partials.print-toolbar')

@foreach($students as $student)
@php $enrollment = $student->printEnrollment; @endphp
<div class="page fiche-page fiche-print-page">
    @include('students.documents.partials.certificate-official-header', [
        'showCertificateTitle' => false,
    ])

    <div class="fiche-document-title">
        <div>FICHE DE RENSEIGNEMENT</div>
        @if($year?->label)
            <div style="font-size:12px;margin-top:3px;">Année scolaire {{ $year->label }}</div>
        @endif
    </div>

    @include('students.documents.partials.fiche-body', [
        'student' => $student,
        'enrollment' => $enrollment,
    ])

    @include('students.documents.partials.fiche-signature')

    {{-- <div class="footer-note">
        Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $school->short_name ?? 'COPTAN' }}
    </div> --}}
</div>
@endforeach
</body>
</html>
