<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin — {{ $bulletin->studentEnrollment->student->full_name }}</title>
    @php
        $forPdf = $forPdf ?? false;
        $logoSrc = null;
        if ($school->logo) {
            $logoPath = public_path('storage/' . $school->logo);
            if (file_exists($logoPath)) {
                $logoSrc = $forPdf ? $logoPath : asset('storage/' . $school->logo);
            }
        }
        $sealSrc = null;
        if ($school->signature_seal) {
            $sealPath = public_path('storage/' . $school->signature_seal);
            if (file_exists($sealPath)) {
                $sealSrc = $forPdf ? $sealPath : asset('storage/' . $school->signature_seal);
            }
        }
    @endphp
    <style>
        /* ── Variables couleurs établissement ── */
        :root {
            --bleu:  #1A3A6B;
            --vert:  #1A5C2A;
            --or:    #C8A415;
            --rouge: #DC2626;
            --gris:  #F8FAFC;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }
            html, body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .bulletin-page { box-shadow: none !important; border: none !important; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #E5E7EB;
            min-height: 100vh;
            padding: 24px;
        }

        .bulletin-page {
            width: 210mm;
            min-height: 295mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 10mm 12mm;
            position: relative;
            overflow: hidden;
        }

        /* ── Filigrane fond ── */
        .bulletin-page::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 260px; height: 260px;
            background: rgba(26, 58, 107, 0.04);
            border-radius: 50%;
            pointer-events: none;
        }

        /* ── EN-TÊTE ── */
        .bulletin-header {
            display: grid;
            grid-template-columns: 80px 1fr 80px;
            align-items: center;
            gap: 8px;
            border-bottom: 3px solid var(--bleu);
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .school-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }
        .school-logo-placeholder {
            width: 72px;
            height: 72px;
            border: 2px solid var(--bleu);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: var(--bleu);
            font-weight: 800;
            text-align: center;
        }
        .header-center {
            text-align: center;
        }
        .header-center .republic {
            font-size: 8px;
            font-weight: 700;
            color: #6B7280;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .header-center .ministry {
            font-size: 7px;
            color: #9CA3AF;
            margin-top: 1px;
        }
        .header-center .school-name {
            font-size: 14px;
            font-weight: 900;
            color: var(--bleu);
            margin-top: 4px;
            letter-spacing: -0.01em;
        }
        .header-center .school-short {
            font-size: 9px;
            color: #6B7280;
            font-weight: 600;
        }
        .bulletin-title {
            margin-top: 6px;
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: var(--bleu);
            color: white;
        }

        /* ── INFO ÉLÈVE ── */
        .student-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            background: var(--gris);
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 8px;
            font-size: 8.5px;
        }
        .info-row {
            display: flex;
            gap: 4px;
            align-items: baseline;
        }
        .info-label {
            font-weight: 700;
            color: #6B7280;
            font-size: 7.5px;
            text-transform: uppercase;
            white-space: nowrap;
            min-width: 70px;
        }
        .info-value {
            font-weight: 800;
            color: #111827;
        }

        /* ── TABLEAU DES NOTES ── */
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 8px;
        }
        .notes-table thead tr {
            background: var(--bleu);
            color: white;
        }
        .notes-table th {
            padding: 5px 6px;
            text-align: center;
            font-weight: 800;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .notes-table th.col-subject { text-align: left; }
        .notes-table tbody tr:nth-child(even) { background: #F9FAFB; }
        .notes-table tbody tr:hover { background: rgba(26,58,107,0.04); }
        .notes-table td {
            padding: 4.5px 6px;
            border-bottom: 1px solid #F3F4F6;
            font-weight: 600;
            color: #374151;
        }
        .notes-table td.col-subject {
            font-weight: 700;
            color: #1F2937;
        }
        .notes-table td.col-teacher {
            font-size: 7px;
            color: #9CA3AF;
        }
        .grade-cell {
            text-align: center;
            font-weight: 900;
        }
        .grade-good  { color: var(--vert); }
        .grade-avg   { color: var(--or); }
        .grade-bad   { color: var(--rouge); }
        .grade-absent { color: #9CA3AF; font-style: italic; font-size: 7px; }
        .coef-cell   { text-align: center; color: #6B7280; font-size: 7.5px; }
        .appr-cell   { text-align: center; font-size: 7.5px; font-weight: 700; color: #374151; }
        .total-cell  { text-align: center; font-weight: 800; color: var(--bleu); font-size: 8px; }

        /* ── Ligne catégorie ── */
        .category-row td {
            padding: 4px 6px 2px;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--bleu);
            background: rgba(26,58,107,0.05);
        }

        /* ── BILAN / STATS ── */
        .bilan-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px;
            margin-bottom: 8px;
        }
        .bilan-card {
            border-radius: 8px;
            padding: 6px 10px;
            text-align: center;
        }
        .bilan-card .bilan-value {
            font-size: 18px;
            font-weight: 900;
            line-height: 1;
        }
        .bilan-card .bilan-label {
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 2px;
            opacity: 0.75;
        }
        .bilan-main  { background: var(--bleu); color: white; }
        .bilan-green { background: rgba(26,92,42,0.1); color: var(--vert); }
        .bilan-gold  { background: rgba(200,164,21,0.1); color: var(--or); }

        /* ── OBSERVATIONS ── */
        .observations-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }
        .obs-box {
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 6px 8px;
            font-size: 8px;
        }
        .obs-title {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--bleu);
            margin-bottom: 3px;
            padding-bottom: 3px;
            border-bottom: 1px solid #E5E7EB;
        }
        .obs-content {
            color: #4B5563;
            font-weight: 600;
            min-height: 30px;
        }

        /* ── SIGNATURES ── */
        .signatures-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1.5px dashed #E5E7EB;
        }
        .sig-box {
            text-align: center;
            font-size: 7.5px;
        }
        .sig-title {
            font-weight: 800;
            text-transform: uppercase;
            color: var(--bleu);
            font-size: 7px;
            letter-spacing: 0.05em;
        }
        .sig-line {
            margin: 20px auto 4px;
            width: 80%;
            border-bottom: 1.5px solid #D1D5DB;
        }
        .sig-name {
            font-size: 7px;
            color: #9CA3AF;
        }

        /* ── PIED DE PAGE ── */
        .bulletin-footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1.5px solid var(--bleu);
            display: flex;
            justify-content: space-between;
            font-size: 7px;
            color: #9CA3AF;
            font-weight: 600;
        }
        .footer-left span { color: var(--bleu); font-weight: 800; }

        /* ── Bouton impression ── */
        .print-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            color: white;
            background: var(--bleu);
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(26,58,107,0.35);
            transition: all 0.2s;
            z-index: 100;
        }
        .print-btn:hover { opacity: 0.9; transform: translateY(-1px); }
    </style>
</head>
<body>

{{-- ── BOUTONS ACTIONS (pas imprimés) ── --}}
@if(!($forPdf ?? false))
<div class="no-print flex items-center gap-3 mb-5 max-w-[210mm] mx-auto">
    <a href="{{ route('bulletins.class', ['classGroup' => $bulletin->studentEnrollment->classGroup->id, 'sequence' => $bulletin->sequence->id]) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm transition-all hover:shadow-md">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour à la liste
    </a>
    <span class="text-gray-300">|</span>
    <span class="text-sm text-gray-500 font-medium">
        {{ $bulletin->studentEnrollment->student->full_name }} — {{ $bulletin->sequence->label }}
    </span>
    @can('print-bulletins')
    <a href="{{ route('bulletins.pdf', $bulletin) }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-white px-4 py-2 rounded-xl shadow-sm hover:opacity-90 transition-all ml-auto"
       style="background:#1A3A6B;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        </svg>
        Télécharger PDF
    </a>
    @endcan
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- BULLETIN PAGE                                                          --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="bulletin-page">

    {{-- ── EN-TÊTE ÉTABLISSEMENT ── --}}
    <div class="bulletin-header">
        {{-- Logo --}}
        @if($logoSrc)
        <img src="{{ $logoSrc }}" alt="Logo" class="school-logo">
        @else
        <div class="school-logo-placeholder">
            {{ strtoupper(substr($school->short_name ?? 'EC', 0, 3)) }}
        </div>
        @endif

        {{-- Centre --}}
        <div class="header-center">
            <div class="republic">République du Cameroun — Paix · Travail · Patrie</div>
            <div class="ministry">{{ $school->ministry ?? 'Ministère des Enseignements Secondaires' }}</div>
            <div class="school-name">{{ $school->full_name ?? 'Établissement Scolaire' }}</div>
            <div class="school-short">{{ $school->address }}{{ $school->city ? ' · ' . $school->city : '' }}</div>
            <div class="bulletin-title">Bulletin de Notes — {{ $bulletin->sequence->label }}</div>
        </div>

        {{-- Sceau / Signature établissement --}}
        @if($sealSrc)
        <img src="{{ $sealSrc }}"
             alt="Sceau" style="width:70px;height:70px;object-fit:contain;opacity:0.7;">
        @else
        <div class="school-logo-placeholder" style="font-size:7px;">CACHET OFFICIEL</div>
        @endif
    </div>

    {{-- ── INFORMATIONS ÉLÈVE ── --}}
    @php
        $enrollment = $bulletin->studentEnrollment;
        $student    = $enrollment->student;
        $classGroup = $enrollment->classGroup;
        $year       = $classGroup->academicYear;
    @endphp
    <div class="student-card">
        <div>
            <div class="info-row">
                <span class="info-label">Nom & Prénoms :</span>
                <span class="info-value" style="text-transform:uppercase;">{{ $student->last_name }}</span>
            </div>
            <div class="info-row" style="margin-top:2px;">
                <span class="info-label"></span>
                <span class="info-value">{{ $student->first_name }}</span>
            </div>
            <div class="info-row" style="margin-top:3px;">
                <span class="info-label">Matricule :</span>
                <span class="info-value" style="color:var(--bleu);">{{ $student->matricule }}</span>
            </div>
            <div class="info-row" style="margin-top:2px;">
                <span class="info-label">Date de naissance :</span>
                <span class="info-value">
                    {{ $student->birth_date?->format('d/m/Y') ?? '—' }}
                    {{ $student->birth_place ? 'à ' . $student->birth_place : '' }}
                </span>
            </div>
        </div>
        <div>
            <div class="info-row">
                <span class="info-label">Classe :</span>
                <span class="info-value" style="color:var(--bleu);">{{ $classGroup->full_name }}</span>
            </div>
            <div class="info-row" style="margin-top:2px;">
                <span class="info-label">Section :</span>
                <span class="info-value">{{ $classGroup->level?->section?->name ?? '—' }}</span>
            </div>
            <div class="info-row" style="margin-top:2px;">
                <span class="info-label">Année scolaire :</span>
                <span class="info-value">{{ $year->label ?? '—' }}</span>
            </div>
            <div class="info-row" style="margin-top:2px;">
                <span class="info-label">Trimestre :</span>
                <span class="info-value">{{ $bulletin->sequence->trimester?->label ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- ── TABLEAU DES NOTES ── --}}
    <table class="notes-table">
        <thead>
            <tr>
                <th class="col-subject" style="width:32%;">Matière</th>
                <th style="width:12%;">Enseignant</th>
                <th style="width:6%;">Coef.</th>
                <th style="width:9%;">Note /20</th>
                <th style="width:9%;">Moy×Coef</th>
                <th style="width:10%;">Rang</th>
                <th style="width:11%;">Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCoef = 0;
                $totalPts  = 0;
            @endphp

            @forelse($bulletin->subjectDetails->sortBy('subject_order') as $detail)
            @php
                $grade     = $detail->seq_grade ?? $detail->average;
                $coef      = $detail->coefficient;
                $total     = $detail->total ?? ($grade !== null ? round($grade * $coef, 2) : null);
                $totalCoef += $coef;
                if ($total !== null) $totalPts += $total;

                $gradeClass = '';
                if ($grade !== null) {
                    if ($grade >= 14)      $gradeClass = 'grade-good';
                    elseif ($grade >= 10)  $gradeClass = 'grade-avg';
                    else                   $gradeClass = 'grade-bad';
                }
            @endphp
            <tr>
                <td class="col-subject">
                    {{ $detail->classSubject?->subject?->name_fr ?? '—' }}
                </td>
                <td class="col-teacher">
                    {{ $detail->teacher_name ? \Str::limit($detail->teacher_name, 20) : '—' }}
                </td>
                <td class="coef-cell">{{ $coef }}</td>
                <td class="grade-cell {{ $gradeClass }}">
                    @if($grade !== null)
                        {{ number_format($grade, 2) }}
                    @else
                        <span class="grade-absent">ABS</span>
                    @endif
                </td>
                <td class="total-cell">
                    {{ $total !== null ? number_format($total, 2) : '—' }}
                </td>
                <td class="grade-cell" style="color:#6B7280;">
                    {{ $detail->rank_in_subject ?? '—' }}
                </td>
                <td class="appr-cell">
                    @if($detail->appreciation)
                        @php $apprColors = \App\Models\AppreciationScale::colorsForCode($detail->appreciation); @endphp
                        <span title="{{ $detail->appreciation_label }}"
                              style="display:inline-block;padding:1px 5px;border-radius:4px;font-size:7px;font-weight:800;
                                     background:{{ $apprColors['bg'] }};color:{{ $apprColors['color'] }};">
                            {{ $detail->appreciation }}
                        </span>
                    @else
                        <span style="color:#9CA3AF;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#9CA3AF; padding: 12px;">
                    Aucune matière enregistrée.
                </td>
            </tr>
            @endforelse

            {{-- ── Ligne TOTAUX ── --}}
            @if($bulletin->subjectDetails->isNotEmpty())
            <tr style="border-top: 2px solid var(--bleu); background: rgba(26,58,107,0.04);">
                <td colspan="2" style="font-weight:900; color:var(--bleu); font-size:8.5px; padding:5px 6px;">
                    TOTAUX &amp; MOYENNE GÉNÉRALE
                </td>
                <td class="coef-cell" style="font-weight:900; color:var(--bleu);">{{ $totalCoef }}</td>
                <td class="grade-cell" style="font-size:11px; font-weight:900; color:var(--bleu);">
                    {{ $bulletin->average_general !== null ? number_format($bulletin->average_general, 2) : '—' }}
                </td>
                <td class="total-cell" style="font-weight:900;">
                    {{ number_format($totalPts, 2) }}
                </td>
                <td class="grade-cell" style="font-weight:900; color:var(--bleu);">
                    {{ $bulletin->rank ?? '—' }}<span style="font-size:6.5px;font-weight:600;">e</span>/{{ $bulletin->class_size ?? '—' }}
                </td>
                <td class="appr-cell" style="font-weight:900; color:var(--bleu);">
                    {{ $bulletin->general_observation ?? '—' }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- ── BILAN STATISTIQUE ── --}}
    <div class="bilan-grid">
        <div class="bilan-card bilan-main">
            <div class="bilan-value">
                {{ $bulletin->average_general !== null ? number_format($bulletin->average_general, 2) : '—' }}<span style="font-size:10px;">/20</span>
            </div>
            <div class="bilan-label">Moyenne générale</div>
        </div>
        <div class="bilan-card bilan-green">
            <div class="bilan-value">
                {{ $bulletin->rank ?? '—' }}<span style="font-size:10px;">/{{ $bulletin->class_size ?? '—' }}</span>
            </div>
            <div class="bilan-label">Rang dans la classe</div>
        </div>
        <div class="bilan-card bilan-gold">
            <div class="bilan-value" style="font-size:11px;">
                {{ ($bulletin->unjustified_absences ?? 0) + ($bulletin->justified_absences ?? 0) > 0
                    ? number_format(($bulletin->unjustified_absences ?? 0) + ($bulletin->justified_absences ?? 0), 1) . 'h'
                    : '0h' }}
            </div>
            <div class="bilan-label">Absences totales</div>
        </div>
    </div>

    {{-- ── STATS CLASSE ── --}}
    <div style="display:flex; gap:16px; font-size:7.5px; color:#6B7280; margin-bottom:8px; font-weight:600;">
        <span>Moy. classe : <strong style="color:var(--bleu);">
            {{ $bulletin->class_average !== null ? number_format($bulletin->class_average, 2) . '/20' : '—' }}
        </strong></span>
        <span>Plus haute : <strong style="color:var(--vert);">
            {{ $bulletin->highest_average !== null ? number_format($bulletin->highest_average, 2) . '/20' : '—' }}
        </strong></span>
        <span>Plus basse : <strong style="color:var(--rouge);">
            {{ $bulletin->lowest_average !== null ? number_format($bulletin->lowest_average, 2) . '/20' : '—' }}
        </strong></span>
        <span>Effectif : <strong style="color:var(--bleu);">{{ $bulletin->class_size ?? '—' }}</strong></span>
    </div>

    {{-- ── OBSERVATIONS ── --}}
    <div class="observations-row">
        <div class="obs-box">
            <div class="obs-title">Observation du Conseil de Classe</div>
            <div class="obs-content">
                {{ $bulletin->councilDecision?->label ?? $bulletin->general_observation ?? 'Bonne continuation.' }}
            </div>
        </div>
        <div class="obs-box">
            <div class="obs-title">Distinction / Décision</div>
            <div class="obs-content">
                @if($bulletin->distinction)
                    <span style="color:var(--or); font-weight:800;">
                        {{ $bulletin->distinction->label }}
                    </span>
                @else
                    —
                @endif
            </div>
        </div>
    </div>

    {{-- ── SIGNATURES ── --}}
    <div class="signatures-row">
        <div class="sig-box">
            <div class="sig-title">Signature Parent / Tuteur</div>
            <div class="sig-line"></div>
            <div class="sig-name">Lu et approuvé</div>
        </div>
        <div class="sig-box">
            <div class="sig-title">Professeur Principal</div>
            <div class="sig-line"></div>
            <div class="sig-name">&nbsp;</div>
        </div>
        <div class="sig-box">
            <div class="sig-title">Le Censeur / Principal</div>
            <div class="sig-line"></div>
            <div class="sig-name">Cachet &amp; Signature</div>
        </div>
    </div>

    {{-- ── PIED DE PAGE ── --}}
    <div class="bulletin-footer">
        <div>
            <span>{{ $school->full_name ?? 'Établissement' }}</span> ·
            {{ $school->address }}
            @foreach($phones->take(2) as $phone)
                · {{ $phone->number }}
            @endforeach
        </div>
        <div>
            Généré le {{ $bulletin->generated_at?->format('d/m/Y à H:i') ?? now()->format('d/m/Y') }}
            @if($bulletin->generatedBy) par {{ $bulletin->generatedBy->name }} @endif
        </div>
    </div>

</div>{{-- /bulletin-page --}}

{{-- ── BOUTON IMPRESSION FLOTTANT ── --}}
@if(!($forPdf ?? false))
<button class="print-btn no-print" onclick="window.print()">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
    </svg>
    Imprimer / Sauvegarder PDF
</button>
@endif

</body>
</html>
