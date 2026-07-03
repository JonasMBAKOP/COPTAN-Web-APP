<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Emploi du temps individuel - {{ $selectedStaff->full_name }}</title>
@include('students.documents.partials.base-styles')
<style>
@page { size: A4 landscape; margin: 6mm; }
body { background:#fff; padding:0; color:#111827; }
.timetable-page { width:100%; max-width:none; padding:0; background:#fff; }
.no-print { margin:0 0 8px; text-align:center; }
.print-btn { border:0; border-radius:8px; background:#1A3A6B; color:#fff; cursor:pointer; font-weight:800; padding:8px 18px; }
.cert-official-header { margin-bottom:5px; }
.cert-official-header__columns { grid-template-columns:1fr 30mm 1fr; gap:5mm; }
.cert-official-header__side { min-height:32mm; }
.cert-official-header__republic { font-size:9.5px; }
.cert-official-header__motto { font-size:8.5px; margin:2px 0; }
.cert-official-header__stars { font-size:8.5px; line-height:1; margin:1px 0; }
.cert-official-header__ministry,
.cert-official-header__school { font-size:8.5px; }
.cert-official-header__meta,
.cert-official-header__email { font-size:7.5px; line-height:1.2; }
.cert-official-header__logo img,
.cert-official-header__logo-placeholder { width:25mm; height:25mm; }
.cert-official-header__agreements { display:none; }
.report-title { border-top:2px solid #1A3A6B; border-bottom:1px solid #D1D5DB; margin:4px 0 5px; padding:4px 0; text-align:center; }
.report-title h1 { color:#1A3A6B; font-size:13px; font-weight:900; text-transform:uppercase; }
.report-title p { color:#4B5563; font-size:9px; margin-top:2px; }
.teacher-meta { width:100%; border-collapse:collapse; margin:0 0 5px; }
.teacher-meta td { border:1px solid #D1D5DB; padding:3px 5px; font-size:6.5px; vertical-align:top; }
.teacher-meta .label { width:24%; background:#F8FAFC; color:#1A3A6B; font-weight:900; }
.teacher-meta .value { font-weight:800; color:#111827; }
.timetable-print { width:100%; border-collapse:collapse; table-layout:fixed; font-size:8px; }
.timetable-print th,
.timetable-print td { border:1px solid #CBD5E1; padding:4px 5px; vertical-align:middle; text-align:center; }
.timetable-print th { background:#EEF2F7; color:#1A3A6B; font-size:8.5px; font-weight:900; text-transform:uppercase; }
.timetable-print .period { width:22mm; color:#4B5563; font-size:8px; font-weight:800; text-align:center; }
.timetable-print td { min-height:9mm; text-align:center; vertical-align:middle; }
.break-row td { background:#FFF7ED; color:#9A3412; font-size:8.5px; font-weight:900; text-align:center; }
.slot { border-left:3px solid #1A5C2A; background:#F8FAFC; padding:4px; min-height:10mm; text-align:center; display:flex; flex-direction:column; justify-content:center; align-items:center; height:100%; }
.slot strong { color:#0F5132; display:block; font-size:8px; line-height:1.15; }
.slot span { color:#374151; display:block; font-size:7.5px; line-height:1.15; }
.footer-area { margin-top:26px; }
.footer-line { display:flex; justify-content:space-between; color:#6B7280; font-size:8px; }
.signatures { margin-top:18px; display:flex; justify-content:space-between; gap:12px; }
.signatures__box { width:48%; border-top:1px solid #CBD5E1; padding-top:8px; display:flex; flex-direction:column; gap:4px; }
.signatures__label { font-size:7.5px; color:#4B5563; font-weight:700; }
.signatures__line { border-bottom:1px solid #9CA3AF; height:0; margin-top:10px; }
@media print { .no-print { display:none !important; } }
</style>
</head>
<body>
<div class="no-print"><button class="print-btn" onclick="window.print()">Imprimer</button></div>

<div class="timetable-page">
    @include('students.documents.partials.certificate-official-header', [
        'school' => $school,
        'phones' => $phones,
        'agreements' => $agreements,
        'showCertificateTitle' => false,
        'forPdf' => false,
    ])

    <div class="report-title">
        <h1>Emploi du Temps Individuel de l'Enseignant / Teacher's Individual Timetable</h1>
        <p>{{ $selectedStaff->full_name }} - {{ $activeYear?->label }}</p>
    </div>

    <table class="teacher-meta">
        <tr>
            <td class="label">Teacher's Name / Nom de l'enseignant</td>
            <td class="value">{{ $selectedStaff->full_name }}</td>
            <td class="label">School Year / Année Scolaire</td>
            <td class="value">{{ $activeYear?->label ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Subject(s) Taught / Discipline(s) Enseignée(s)</td>
            <td class="value">{{ $teacherSubjects->join(' / ') ?: '' }}</td>
            <td class="label">Classes Taught / Classes Tenues</td>
            <td class="value">{{ $teacherClasses->join(' / ') ?: '' }}</td>
        </tr>
        <tr>
            <td class="label">Hours Done / Nombre d'Heures</td>
            <td class="value" colspan="3">{{ number_format($totalHours, 1, ',', ' ') }} h</td>
        </tr>
    </table>

    @include('timetable.partials.grid', [
        'mode' => 'teacher',
        'printable' => true,
        'days' => $days,
        'gridRows' => $gridRows,
        'slots' => $slots,
        'teacherSubjectCount' => $teacherSubjectCount,
    ])

    <div class="footer-area">
        <div class="footer-line">
            <span>Fait à ________________________ le ________________________</span>
            <span>{{ $school->short_name ?? 'COPTAN' }}</span>
        </div>
        <div class="signatures">
            <div class="signatures__box">
                <span class="signatures__label">Le Préfet des Etudes / The Dean</span>
                <div class="signatures__line"></div>
            </div>
            <div class="signatures__box">
                <span class="signatures__label">Le Principale / The Principal</span>
                <div class="signatures__line"></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>