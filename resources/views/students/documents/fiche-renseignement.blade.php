<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiche de renseignement — {{ $student->full_name }}</title>
@include('students.documents.partials.base-styles')
@include('students.documents.partials.fiche-styles')
</head>
<body>
@include('students.documents.partials.print-toolbar')

<div class="page fiche-page">
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
</body>
</html>
