<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convocation parents</title>
    @include('students.documents.partials.base-styles')
    <style>
        @page { size: A4 portrait; margin: 2mm 4mm; }
        body { font-family: 'DejaVu Sans', sans-serif; padding: 0; background: #fff; font-size: 15px; }
        .page { max-width: 100%; margin: 0; padding: 4mm 5mm; }
        .convocation-sheet { display: flex; flex-direction: column; justify-content: space-between; min-height: 132mm; border: 1px solid #E5E7EB; border-radius: 14px; padding: 16px 18px; background: #fff; box-sizing: border-box; }
        .convocation-block { border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px; margin-bottom: 12px; margin-top: 10px; }
        .convocation-block h2 { font-size: 20px; font-weight: 900; color: #1A3A6B; margin-bottom: 10px; }
        .convocation-block p { font-size: 16px; color: #111827; line-height: 1.6; margin: 0 0 10px; }
        .convocation-strong { font-size: 18px; }
        .info-row { display: flex; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
        .info-row .label { font-weight: 700; color: #4B5563; width: 40%; font-size: 15px; }
        .info-row .value { width: 60%; font-weight: 700; color: #111827; text-align: right; font-size: 15px; }
        .signature { margin-top: 18px; text-align: right; font-size: 15px; font-weight: 700; color: #111827; }
        .signature small { display: block; margin-top: 6px; font-size: 13px; font-weight: 400; color: #6B7280; }
        .notice { margin-top: 14px; font-size: 13px; color: #6B7280; }
    </style>
</head>
<body>
@include('students.documents.partials.print-toolbar')

<div class="page cert-page">
    <div class="convocation-sheet">
        @include('grades.partials.bordereau-header', [
            'school' => $schoolSettings,
            'phones' => $phones ?? collect(),
            'forPdf' => false,
            'classGroup' => $disciplineIncident->studentEnrollment->classGroup,
            'docTitle' => 'Convocation Parents',
            // 'docSubtitle' => 'Convocation disciplinaire',
        ])

        <div class="convocation-block">
            <p>Chers parents de l'élève <strong class="convocation-strong">{{ $disciplineIncident->studentEnrollment->student->full_name }}</strong>
                de la classe de <strong class="convocation-strong">{{ $disciplineIncident->studentEnrollment->classGroup->full_name }}</strong>,
                nous vous informons que vous êtes convoqués au <strong>{{ $schoolSettings->full_name }}</strong> pour une réunion disciplinaire concernant le comportement
                de votre enfant, qui se tiendra le <strong>{{ $disciplineIncident->convocation_date?->format('d/m/Y') ?? '—' }}</strong> à <strong>09h00</strong>.</p>
            <p>Motif de la convocation : <strong class="convocation-strong">{{ $disciplineIncident->incident_type_label }}</strong>.</p>
            <p>Merci de vous présenter à la surveillance générale à la date indiquée.</p>
        </div>

        <div class="signature">Service disciplinaire
            {{-- <small>Convocation générée le {{ now()->format('d/m/Y à H:i') }}</small> --}}
        </div>
    </div>
</div>
</body>
</html>
