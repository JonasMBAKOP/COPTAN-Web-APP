<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Reçu Global — {{ $enrollment->student->full_name }}</title>
<style>
    /* ── PRINT ──────────────────────────────────────────────────────────── */
    @page { size: A4 portrait; margin: 8mm 10mm; }
    @media print { .no-print { display:none!important; } }

    /* ── BASE ───────────────────────────────────────────────────────────── */
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        font-weight: 700;
        color: #050E1F;
        background: #f0f2f5;
        padding: 14px;
    }

    /* ── PAGE ────────────────────────────────────────────────────────────── */
    .page {
        max-width: 277mm;
        margin: 0 auto;
        background: #fff;
        border: 3px solid #0B2545;
        border-radius: 4px;
        overflow: hidden;
    }

    /* ── HEADER ─────────────────────────────────────────────────────────── */
    .header {
        background: #0B2545;
        display: flex;
        align-items: stretch;
    }
    .header-left {
        flex: 3;                    /* ← proportionnel */
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-right: 2px solid #1A4070;
        min-width: 0;
    }
    .logo-circle {
        width: 46px; height: 46px;
        background: #1A4070;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; font-weight: 900; color: #FFD080;
        flex-shrink: 0;
    }
    .school-name { font-size: 13.5px; font-weight: 900; color: #fff; }
    .school-sub  { font-size: 9.5px; font-weight: 700; color: #8BAFD4; margin-top: 2px; }
    .header-right {
        flex: 1.3;                  /* ← proportionnel, plus compact */
        padding: 10px 13px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 3px;
        min-width: 0;
    }
    .doc-title {
        font-size: 13px; font-weight: 900;
        color: #FFD080; text-transform: uppercase; letter-spacing: 1px;
    }
    .doc-sub  { font-size: 9.5px; font-weight: 700; color: #8BAFD4; }
    .doc-date { font-size: 10.5px; font-weight: 900; color: #fff; }

    /* ── BLOC ÉLÈVE ──────────────────────────────────────────────────────── */
    .student-block {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        border-bottom: 2.5px solid #0B2545;
    }
    .student-col {
        padding: 8px 13px;
        border-right: 1.5px solid #C8D8EC;
    }
    .student-col:last-child { border-right: none; }
    .col-badge {
        font-size: 8px; font-weight: 900; text-transform: uppercase;
        letter-spacing: 1.5px; color: #0B2545; background: #E8EFF8;
        padding: 2px 6px; border-radius: 2px;
        display: inline-block; margin-bottom: 5px;
    }
    .col-row { display: flex; gap: 6px; margin-bottom: 3px; align-items: baseline; }
    .col-lbl { font-size: 9px; font-weight: 900; color: #4A5568; min-width: 70px; }
    .col-val { font-size: 11px; font-weight: 900; color: #050E1F; }

    /* ── SECTION TITRE ──────────────────────────────────────────────────── */
    .section-title {
        background: #0B2545;
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 6px 13px;
    }

    /* ── TABLEAU VERSEMENTS ─────────────────────────────────────────────── */
    .t-versements {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5px;
    }
    .t-versements thead tr {
        background: #E8EFF8;
    }
    .t-versements thead th {
        padding: 6px 8px;
        text-align: left;
        font-size: 9.5px;
        font-weight: 900;
        color: #0B2545;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #0B2545;
        border-right: 1px solid #C8D8EC;
    }
    .t-versements thead th:last-child { border-right: none; }
    .t-versements tbody tr { border-bottom: 1px solid #D8E4F0; }
    .t-versements tbody tr:nth-child(even) { background: #F7FAFE; }
    .t-versements tbody td {
        padding: 5px 8px;
        font-weight: 800;
        color: #050E1F;
        border-right: 1px solid #D8E4F0;
        vertical-align: middle;
    }
    .t-versements tbody td:last-child {
        border-right: none;
        text-align: right;
        font-weight: 900;
        font-family: 'Courier New', monospace;
        font-size: 11px;
        color: #0B2545;
    }
    .receipt-id {
        font-family: 'Courier New', monospace;
        font-size: 9.5px;
        font-weight: 900;
        color: #1A5090;
        background: #EAF2FF;
        padding: 2px 5px;
        border-radius: 3px;
    }
    .mode-badge {
        font-size: 9px; font-weight: 900;
        padding: 2px 6px; border-radius: 3px;
        background: #E8EFF8; color: #0B2545;
    }
    .t-versements tfoot tr {
        background: #0B2545;
        border-top: 2px solid #0B2545;
    }
    .t-versements tfoot td {
        padding: 6px 8px;
        font-size: 11px;
        font-weight: 900;
        color: #fff;
        border-right: 1px solid #1A4070;
    }
    .t-versements tfoot td:last-child {
        border-right: none;
        text-align: right;
        color: #FFD080;
        font-size: 13px;
        font-family: 'Courier New', monospace;
    }

    /* ── TABLEAU ÉTAT GÉNÉRAL ────────────────────────────────────────────── */
    .t-etat {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    .t-etat thead tr { background: #E8EFF8; }
    .t-etat thead th {
        padding: 6px 10px;
        text-align: left;
        font-size: 10px;
        font-weight: 900;
        color: #0B2545;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #0B2545;
        border-right: 1px solid #C8D8EC;
    }
    .t-etat thead th:last-child { border-right: none; }
    .t-etat thead th.right,
    .t-etat tbody td.right,
    .t-etat tfoot td.right { text-align: right; }
    .t-etat tbody tr { border-bottom: 1px solid #D8E4F0; }
    .t-etat tbody tr:nth-child(even) { background: #F7FAFE; }
    .t-etat tbody td {
        padding: 6px 10px;
        font-weight: 800;
        color: #050E1F;
        border-right: 1px solid #D8E4F0;
    }
    .t-etat tbody td.right {
        font-family: 'Courier New', monospace;
        font-size: 11px;
        font-weight: 900;
    }
    .t-etat tbody td:last-child { border-right: none; }
    .status-ok  { color: #1A6B2A; font-weight: 900; }
    .status-due { color: #B22222; font-weight: 900; }
    .t-etat tfoot tr {
        background: #0B2545;
        border-top: 2.5px solid #0B2545;
    }
    .t-etat tfoot td {
        padding: 7px 10px;
        font-size: 11.5px;
        font-weight: 900;
        color: #fff;
        border-right: 1px solid #1A4070;
    }
    .t-etat tfoot td:last-child { border-right: none; }
    .t-etat tfoot td.right {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        text-align: right;
    }
    .t-etat tfoot td.right.green { color: #80FFB0; }
    .t-etat tfoot td.right.red   { color: #FFAAAA; }
    .t-etat tfoot td.right.gold  { color: #FFD080; }

    /* ── FOOTER ──────────────────────────────────────────────────────────── */
    .footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #0B2545;
        padding: 7px 14px;
        border-top: 2px solid #0B2545;
    }
    .footer-cashier {
        font-size: 10px; font-weight: 900; color: #8BAFD4;
    }
    .footer-cashier span { color: #fff; font-size: 11px; }
    .footer-sig {
        font-size: 9.5px; font-weight: 800; color: #8BAFD4;
        border-top: 1px solid #8BAFD4;
        padding-top: 3px; min-width: 120px; text-align: center;
    }
    .nb-bar {
        background: #FFF3CD;
        border-top: 2.5px solid #C8A415;
        padding: 6px 14px;
        text-align: center;
        font-size: 11.5px;
        font-weight: 900;
        color: #5C3D00;
        letter-spacing: 0.5px;
    }

    /* ── BOUTON ──────────────────────────────────────────────────────────── */
    .print-btn { text-align:center; margin-bottom: 14px; }
    .print-btn button {
        background: #0B2545; color: #fff; border: none;
        padding: 10px 28px; border-radius: 8px; cursor: pointer;
        font-size: 13px; font-weight: 800;
    }
</style>
</head>
<body>

<div class="print-btn no-print">
    <button onclick="window.print()">🖨 Imprimer le reçu global</button>
</div>

<div class="page">

    {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
    <div class="header">
        <div class="header-left">
            @if($school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo de l'établissement"
                     style="
                            flex-shrink:0;">
                {{-- <img src="{{ asset('storage/' . $school->logo) }}"
                     style="height:46px;width:46px;object-fit:contain;
                            border-radius:50%;background:#1A4070;flex-shrink:0;"> --}}
            @else
                <div class="logo-circle">
                    {{ strtoupper(substr($school->short_name ?? 'C', 0, 1)) }}
                </div>
            @endif
            <div>
                <div class="school-name">
                    {{ strtoupper($school->full_name ?? 'COLLÈGE POLYVALENT NTANKEU') }}
                </div>
                <div class="school-sub">
                    @if($phones->isNotEmpty())
                        Tél :
                        @foreach($phones->take(2) as $phone)
                            {{ $phone->number }}{{ !$loop->last ? ' / ' : '' }}
                        @endforeach
                    @elseif($school->phone_1)
                        Tél : {{ $school->phone_1 }}
                    @endif
                    @if($school->address) &nbsp;|&nbsp; {{ $school->address }} @endif
                </div>
                <div class="school-sub">
                    @if($school->postal_box) BP : {{ $school->postal_box }} @endif
                </div>
                <div class="school-sub">
                    @if($school->ministry) {{ $school->ministry }} @endif
                </div>
            </div>
        </div>
        <div class="header-right" style="text-align: center;">
            <div class="doc-title">Reçu de Versement Officiel</div>
            <div class="doc-sub">Exemplaire Élève</div>
            <div class="doc-date">
                Année Scolaire : {{ $enrollment->academicYear->label }}
                &nbsp;&nbsp;Date : {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- ── BLOC ÉLÈVE ───────────────────────────────────────────────────── --}}
    <div class="student-block">
        <div class="student-col">
            <div class="col-badge">Identité de l'élève</div>
            <div class="col-row">
                <span class="col-lbl">Nom et prénom :</span>
                <span class="col-val">
                    {{ strtoupper($enrollment->student->full_name) }}
                </span>
            </div>
            <div class="col-row">
                <span class="col-lbl">Matricule :</span>
                <span class="col-val">{{ $enrollment->student->matricule }}</span>
            </div>
        </div>
        <div class="student-col">
            <div class="col-badge">Naissance</div>
            <div class="col-row">
                <span class="col-lbl">Date :</span>
                <span class="col-val">
                    {{ $enrollment->student->date_of_birth?->format('d/m/Y') ?? '—' }}
                </span>
            </div>
            <div class="col-row">
                <span class="col-lbl">Lieu :</span>
                <span class="col-val">
                    {{ strtoupper($enrollment->student->place_of_birth ?? '—') }}
                </span>
            </div>
        </div>
        <div class="student-col">
            <div class="col-badge">Scolarité</div>
            <div class="col-row">
                <span class="col-lbl">Classe :</span>
                <span class="col-val">
                    {{ $enrollment->classGroup->full_name }}
                </span>
            </div>
            <div class="col-row">
                <span class="col-lbl">Section :</span>
                <span class="col-val">
                    {{ $enrollment->classGroup->level->section->name }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── TOUS LES VERSEMENTS ──────────────────────────────────────────── --}}
    <div class="section-title">Tous les Versements</div>

    @if($payments->isEmpty())
    <div style="padding:12px 14px; font-size:11px; color:#666; font-style:italic;">
        Aucun paiement enregistré.
    </div>
    @else
    <table class="t-versements">
        <thead>
            <tr>
                <th style="width:22%">N° Reçu</th>
                <th style="width:20%">Objet</th>
                <th style="width:13%">Mode</th>
                <th style="width:20%">Date / Heure</th>
                <th style="width:16%">Caissier(e)</th>
                <th style="width:9%; text-align:right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
            <tr>
                <td>
                    <span class="receipt-id">{{ $p->receipt_number }}</span>
                </td>
                <td style="font-weight:900;">
                    {{ $p->feeInstallment?->label ?? '—' }}
                </td>
                <td>
                    <span class="mode-badge">{{ $p->payment_method_label }}</span>
                </td>
                <td>
                    {{ $p->payment_date->format('d/m/Y') }}
                    {{ $p->created_at->format('H:i:s') }}
                </td>
                <td>{{ $p->recordedBy?->name ?? '—' }}</td>
                <td>{{ number_format($p->amount_paid, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="font-weight:900; letter-spacing:1px;">
                    TOTAL DES VERSEMENTS
                </td>
                <td>{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ── ÉTAT GÉNÉRAL DES FRAIS ──────────────────────────────────────── --}}
    <div class="section-title" style="margin-top:0; border-top:2px solid #1A4070;">
        État Général des Frais
    </div>

    @if($installmentSummary->isEmpty())
    <div style="padding:10px 14px; font-size:11px; color:#666; font-style:italic;">
        Aucune structure de frais configurée.
    </div>
    @else
    <table class="t-etat">
        <thead>
            <tr>
                <th style="width:30%">Désignation</th>
                <th style="width:18%">Échéance</th>
                <th class="right" style="width:17%">Montant Total</th>
                <th class="right" style="width:17%">Déjà Payé</th>
                <th class="right" style="width:18%">Restant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($installmentSummary as $row)
            <tr>
                <td style="font-weight:900;">{{ $row['label'] }}</td>
                <td>
                    {{ $row['due_date'] instanceof \Carbon\Carbon
                        ? $row['due_date']->format('d/m/Y')
                        : ($row['due_date'] ?? '—') }}
                </td>
                <td class="right">
                    {{ number_format($row['amount'], 0, ',', ' ') }} FCFA
                </td>
                <td class="right {{ $row['paid'] >= $row['amount'] ? 'status-ok' : '' }}">
                    {{ number_format($row['paid'], 0, ',', ' ') }} FCFA
                </td>
                <td class="right {{ $row['remaining'] > 0 ? 'status-due' : 'status-ok' }}">
                    {{ number_format($row['remaining'], 0, ',', ' ') }} FCFA
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="font-weight:900; letter-spacing:1px;">
                    TOTAL GÉNÉRAL
                </td>
                <td class="right gold">
                    {{ number_format($totalDue, 0, ',', ' ') }} FCFA
                </td>
                <td class="right green">
                    {{ number_format($totalPaid, 0, ',', ' ') }} FCFA
                </td>
                <td class="right {{ $totalRemaining > 0 ? 'red' : 'green' }}">
                    {{ number_format($totalRemaining, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ── FOOTER ───────────────────────────────────────────────────────── --}}
    <div class="footer-row">
        <div class="footer-cashier">
            Émis le : <span>{{ now()->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="footer-cashier">
            Par : <span>{{ auth()->user()?->name ?? 'Système' }}</span>
        </div>
        <div class="footer-sig">Signature &amp; Cachet Établissement</div>
    </div>
    <div class="nb-bar">
        ⚠&nbsp;&nbsp; NB : AUCUN FRAIS N'EST REMBOURSABLE !
    </div>

</div>
</body>
</html>