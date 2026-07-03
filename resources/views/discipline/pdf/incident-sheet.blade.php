<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche d'incident</title>
    @include('students.documents.partials.base-styles')
    <style>
        @page { size: A4 portrait; margin: 2mm 3mm; }
        body { font-family: 'DejaVu Sans', sans-serif; padding: 0; background: #fff; font-size: 16px; }
        .page { max-width: 100%; margin: 0; padding: 3mm 5mm; }
        .incident-title { font-size: 30px; font-weight: 900; color: #1A3A6B; margin: 10px 0 8px; text-transform: uppercase; }
        .incident-title span { display: block; font-size: 20px; color: #4B5563; margin-top: 6px; }
        .incident-subtitle { font-size: 18px; color: #4B5563; margin-bottom: 14px; }
        .incident-block { border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px; }
        .incident-block h2 { font-size: 20px; font-weight: 900; color: #1A3A6B; margin-bottom: 10px; }
        .incident-block p { font-size: 15px; color: #111827; line-height: 1.7; margin: 0; }
        .info-row { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
        .info-row .label { font-weight: 700; color: #4B5563; width: 45%; font-size: 15px; }
        .info-row .value { width: 55%; font-weight: 700; color: #111827; text-align: right; font-size: 15px; }
        .status-chip { display: inline-block; padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 800; text-transform: uppercase; }
        .status-open { background: #FEE2E2; color: #991B1B; }
        .status-closed { background: #DBEAFE; color: #1D4ED8; }
        .footer-notice { margin-top: 18px; font-size: 13px; color: #6B7280; }
        .student-card { border: 1px solid #E5E7EB; border-radius: 10px; background: #F8FAFC; padding: 16px 18px; margin-bottom: 14px; }
        .student-card .row { display: flex; justify-content: space-between; gap: 12px; font-size: 15px; margin-bottom: 8px; }
    </style>
</head>
<body>
@include('students.documents.partials.print-toolbar')

<div class="page cert-page">
    @include('students.documents.partials.certificate-official-header', [
        'showCertificateTitle' => false,
        'agreements' => $agreements ?? collect(),
        'phones' => $phones ?? collect(),
        'school' => $schoolSettings,
        'forPdf' => false,
    ])

    <div class="cert-official-header__title" style="margin-top: 10px;">
        <div>FICHE D'INCIDENT</div>
         {{-- / <span style="font-style: italic; color:#4B5563;">INCIDENT REPORT</span>  --}}
        <div style="font-size:23px; font-style: italic; margin-top:6px;">INCIDENT REPORT</div>
    </div>

    {{-- <div class="incident-subtitle">Élève : {{ $disciplineIncident->studentEnrollment->student->full_name }} — Classe : {{ $disciplineIncident->studentEnrollment->classGroup->full_name }}</div> --}}

    <div class="student-card" style="margin-top: 14px;">
        <div class="row"><span>Élève</span><strong>{{ $disciplineIncident->studentEnrollment->student->full_name }}</strong></div>
        <div class="row"><span>Matricule</span><strong>{{ $disciplineIncident->studentEnrollment->student->matricule ?? '—' }}</strong></div>
        <div class="row"><span>Classe</span><strong>{{ $disciplineIncident->studentEnrollment->classGroup->full_name }}</strong></div>
    </div>

    <div class="incident-block">
        <div class="info-row"><span class="label">Date incident</span><span class="value">{{ $disciplineIncident->incident_date->format('d/m/Y') }}</span></div>
        <div class="info-row"><span class="label">Heure</span><span class="value">{{ $disciplineIncident->incident_time ?? '—' }}</span></div>
        <div class="info-row"><span class="label">Lieu</span><span class="value">{{ $disciplineIncident->location_label }}</span></div>
        <div class="info-row"><span class="label">Type d'incident</span><span class="value">{{ $disciplineIncident->incident_type_label }}</span></div>
        {{-- <div class="info-row"><span class="label">Statut</span><span class="value"><span class="status-chip status-{{ $disciplineIncident->status }}">{{ $disciplineIncident->status_label }}</span></span></div> --}}
    </div>

    <div class="incident-block">
        <h2>Description des faits</h2>
        <p>{{ $disciplineIncident->description }}</p>
    </div>

    <div class="incident-block">
        <div class="info-row"><span class="label">Sanction</span><span class="value">{{ $disciplineIncident->sanction_label }}</span></div>
        <div class="info-row"><span class="label">Durée</span><span class="value">{{ $disciplineIncident->sanction_duration_days ? $disciplineIncident->sanction_duration_days . ' jour(s)' : '—' }}</span></div>
        <div class="info-row"><span class="label">Parents convoqués</span><span class="value">{{ $disciplineIncident->parent_convoked ? 'Oui' : 'Non' }}</span></div>
        <div class="info-row"><span class="label">Date convocation</span><span class="value">{{ $disciplineIncident->convocation_date?->format('d/m/Y') ?? '—' }}</span></div>
    </div>

    <div class="incident-block">
        <div class="info-row"><span class="label">Signalé par</span><span class="value">{{ $disciplineIncident->reportedBy?->name ?? '—' }}</span></div>
        {{-- <div class="info-row"><span class="label">Décidé par</span><span class="value">{{ $disciplineIncident->decidedBy?->name ?? '—' }}</span></div> --}}
        <div class="info-row"><span class="label">Établi le</span><span class="value">{{ now()->format('d/m/Y H:i') }}</span></div>
    </div>

    {{-- <div class="footer-notice">Document généré automatiquement. Vérifiez les informations avant distribution.</div> --}}
</div>
</body>
</html>
