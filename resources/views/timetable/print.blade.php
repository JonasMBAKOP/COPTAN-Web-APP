<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Emploi du temps - {{ $classGroup->full_name }}</title>
@include('students.documents.partials.base-styles')
<style>
@page { size: A4 landscape; margin: 6mm; }
body { background: #fff; padding: 0; color: #111827; }
.timetable-page { width: 100%; max-width: none; padding: 0; background: #fff; }
.no-print { margin: 0 0 8px; text-align: center; }
.print-btn { border: 0; border-radius: 8px; background: #1A3A6B; color: #fff; cursor: pointer; font-weight: 800; padding: 8px 18px; }
.cert-official-header { margin-bottom: 5px; }
.cert-official-header__columns { grid-template-columns: 1fr 30mm 1fr; gap: 5mm; }
.cert-official-header__side { min-height: 32mm; }
.cert-official-header__republic { font-size: 9.5px; }
.cert-official-header__motto { font-size: 8.5px; margin: 2px 0; }
.cert-official-header__stars { font-size: 8.5px; line-height: 1; margin: 1px 0; }
.cert-official-header__ministry,
.cert-official-header__school { font-size: 8.5px; }
.cert-official-header__meta,
.cert-official-header__email { font-size: 7.5px; line-height: 1.2; }
.cert-official-header__logo img,
.cert-official-header__logo-placeholder { width: 25mm; height: 25mm; }
.cert-official-header__agreements { display: none; }
.report-title { border-top: 2px solid #1A3A6B; border-bottom: 1px solid #D1D5DB; margin: 4px 0 5px; padding: 4px 0; text-align: center; }
.report-title h1 { color: #1A3A6B; font-size: 14px; font-weight: 900; letter-spacing: .2px; text-transform: uppercase; }
.report-title p { color: #4B5563; font-size: 9px; margin-top: 2px; }
.timetable-print { width: 100%; border-collapse: collapse; table-layout: fixed; }
.timetable-print th,
.timetable-print td { border: 1px solid #CBD5E1; padding: 2px; vertical-align: middle; text-align: center; }
.timetable-print th { background: #EEF2F7; color: #1A3A6B; font-size: 6.5px; font-weight: 900; text-transform: uppercase; }
.timetable-print .period { width: 22mm; color: #4B5563; font-size: 6.5px; font-weight: 800; text-align: center; vertical-align: middle; }
.timetable-print td { height: 7mm; text-align: center; vertical-align: middle; }
.break-row td { background: #FFF7ED; color: #9A3412; font-size: 7.5px; font-weight: 800; text-align: center; }
.slot { border-left: 3px solid #1A3A6B; background: #F8FAFC; padding: 2px; min-height: 6mm; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; }
.slot strong { color: #0F2748; display: block; font-size: 6.5px; line-height: 1.15; }
.slot span { color: #374151; display: block; font-size: 6.5px; line-height: 1.15; }
.slot small { color: #6B7280; display: block; font-size: 6.5px; line-height: 1.1; margin-top: 1px; }
.empty-half { color: #CBD5E1; font-size: 7px; text-align: center; }
.footer-line { margin-top: 6px; display: flex; justify-content: space-between; color: #6B7280; font-size: 7.5px; }
@media print { .no-print { display: none !important; } }
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
        <h1>Emploi du temps de la classe</h1>
        <p>{{ $classGroup->full_name }} - {{ $classGroup->level?->section?->name }} - Année scolaire {{ $classGroup->academicYear?->label }}</p>
    </div>

    @include('timetable.partials.grid', [
        'mode' => 'class',
        'printable' => true,
        'days' => $days,
        'gridRows' => $gridRows,
        'slots' => $slots,
        'conflicts' => collect(),
    ])

    <div class="footer-line">
        <span>Document généré le {{ now()->format('d/m/Y à H:i') }}</span>
        <span>{{ $school->short_name ?? 'COPTAN' }}</span>
    </div>
</div>
</body>
</html>