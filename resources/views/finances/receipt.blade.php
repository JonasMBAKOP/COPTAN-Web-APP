<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Reçu {{ $payment->receipt_number }}</title>
<style>
@page { size: A4 portrait; margin: 10mm 12mm; }
@media print { .no-print { display:none!important; } }

* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11.5px;
    font-weight: 700;
    color: #050E1F;
    background: #f0f2f5;
    padding: 16px;
}

.receipt {
    max-width: 268mm;
    margin: 0 auto;
    background: #fff;
    border: 3px solid #0B2545;
    border-radius: 4px;
    overflow: hidden;
}

/* ── HEADER ─ fix point 2 : header-right rapproché ───────────────── */
.header {
    background: #0B2545;
    color: #fff;
    display: flex;
    align-items: stretch;
}
.header-school {
    flex: 3;                    /* ← flex proportionnel au lieu de flex:1 */
    padding: 10px 14px;
    border-right: 2px solid #1A4070;
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.header-school img {
    height: 44px; width: 44px;
    object-fit: contain; flex-shrink: 0;
}
.header-school-placeholder {
    width: 44px; height: 44px;
    background: #1A4070; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 900; color: #fff; flex-shrink: 0;
}
.school-name { font-size: 13px; font-weight: 900; line-height: 1.3; }
.school-sub  { font-size: 9.5px; font-weight: 700; color: #8BAFD4; margin-top: 3px; }

.header-right {
    flex: 1.3;                  /* ← flex proportionnel, plus compact */
    padding: 10px 13px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 4px;
    min-width: 0;
}
.receipt-title {
    font-size: 12px; font-weight: 900;
    letter-spacing: 0.8px; color: #FFD080; text-transform: uppercase;
}
.receipt-num {
    font-size: 11px; font-weight: 900;
    color: #fff; font-family: 'Courier New', monospace;
}
.receipt-date-head { font-size: 9.5px; font-weight: 700; color: #8BAFD4; }

/* ── SECTION ÉTUDIANT ─ fix point 3 : student-right beaucoup rapproché */
.student-section {
    display: flex;
    border-bottom: 2px solid #0B2545;
}
.student-left {
    flex: 2.5;                  /* ← ratio plus équilibré */
    padding: 9px 14px;
    border-right: 2px solid #0B2545;
    min-width: 0;
}
.student-right {
    flex: 1.5;                  /* ← flex proportionnel, plus proche */
    padding: 9px 12px;
    min-width: 0;
}
.section-label {
    font-size: 8.5px; font-weight: 900;
    text-transform: uppercase; letter-spacing: 1.5px;
    color: #0B2545; background: #E8EFF8;
    padding: 2px 6px; border-radius: 2px;
    display: inline-block; margin-bottom: 6px;
}
.info-row {
    display: flex; align-items: baseline;
    gap: 5px; margin-bottom: 3px;
}
.info-label {
    font-size: 9px; font-weight: 900;
    color: #4A5568; text-transform: uppercase;
    white-space: nowrap; min-width: 75px;
}
.info-value { font-size: 11px; font-weight: 900; color: #050E1F; }

/* ── BANDE OBJET ──────────────────────────────────────────────────── */
.object-bar {
    background: #E8EFF8; border-bottom: 2px solid #0B2545;
    padding: 7px 14px; display: flex; align-items: center; gap: 12px;
}
.object-label  { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #4A5568; }
.object-value  { font-size: 12px; font-weight: 900; color: #0B2545; text-transform: uppercase; }
.mode-pill {
    margin-left: auto; background: #0B2545; color: #fff;
    font-size: 10px; font-weight: 900; padding: 3px 10px;
    border-radius: 3px; text-transform: uppercase; letter-spacing: 0.5px;
}

/* ── MONTANTS ──────────────────────────────────────────────────────── */
.amounts-table { width: 100%; border-collapse: collapse; }
.amounts-table tr { border-bottom: 1.5px solid #C8D8EC; }
.amounts-table tr:last-child { border-bottom: none; }
.amounts-table td { padding: 7px 14px; font-size: 11px; font-weight: 800; }
.amounts-table td:last-child {
    text-align: right; font-size: 13px; font-weight: 900;
    font-family: 'Courier New', monospace;
}
.row-total td:last-child { color: #0B2545; }
.row-paid { background: #0B2545; }
.row-paid td { color: #fff !important; }
.row-paid td:first-child::before { content: '▶ '; font-size: 9px; }
.row-paid td:last-child { color: #FFD080 !important; font-size: 15px !important; }
.row-remaining td:last-child {
    color: {{ $totalRemaining > 0 ? '#B22222' : '#1A6B2A' }} !important;
}

/* ── FOOTER ───────────────────────────────────────────────────────── */
.footer-bar {
    background: #0B2545; padding: 7px 14px;
    display: flex; align-items: center;
    justify-content: space-between; border-top: 2px solid #0B2545;
}
.footer-cashier { font-size: 10px; font-weight: 900; color: #8BAFD4; }
.footer-cashier span { color: #fff; font-size: 11px; }
.footer-signature {
    font-size: 9.5px; font-weight: 800; color: #8BAFD4;
    border-top: 1px solid #8BAFD4; padding-top: 2px;
    min-width: 120px; text-align: center;
}
.nb-bar {
    background: #FFF3CD; border-top: 2.5px solid #C8A415;
    padding: 6px 14px; text-align: center;
    font-size: 11.5px; font-weight: 900; color: #5C3D00; letter-spacing: 0.5px;
}

/* ── BOUTON ───────────────────────────────────────────────────────── */
.print-btn { display:block; text-align:center; margin-bottom:14px; }
.print-btn button {
    background: #0B2545; color: #fff; border: none;
    padding: 10px 28px; border-radius: 8px; cursor: pointer;
    font-size: 13px; font-weight: 800;
}
</style>
</head>
<body>

<div class="print-btn no-print">
    <button onclick="window.print()">🖨 Imprimer le reçu</button>
</div>

<div class="receipt">

    {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
    <div class="header">
        <div class="header-school">
            @if($school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo">
            @else
                <div class="header-school-placeholder">
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
            <div class="receipt-title">Reçu de Paiement Officiel</div>
            <div class="receipt-num">N° {{ $payment->receipt_number }}</div>
            <div class="receipt-date-head">
                Exemplaire Élève
            </div>
            <div class="receipt-date-head">
                Année Scolaire : {{ $payment->studentEnrollment->academicYear->label }}
                &nbsp;|&nbsp;
                Date : {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- ── INFOS ÉLÈVE ──────────────────────────────────────────────────── --}}
    <div class="student-section">
        <div class="student-left">
            <div class="section-label">Identité de l'élève</div>
            <div class="info-row">
                <span class="info-label">Nom & prénom :</span>
                <span class="info-value">
                    {{ strtoupper($payment->studentEnrollment->student->full_name) }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Matricule :</span>
                <span class="info-value">
                    {{ $payment->studentEnrollment->student->matricule }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Né(e) le :</span>
                <span class="info-value">
                    {{ $payment->studentEnrollment->student->date_of_birth?->format('d/m/Y') ?? '—' }}
                    @if($payment->studentEnrollment->student->place_of_birth)
                        &nbsp;à&nbsp;
                        {{ strtoupper($payment->studentEnrollment->student->place_of_birth) }}
                    @endif
                </span>
            </div>
        </div>
        <div class="student-right">
            <div class="section-label">Scolarité</div>
            <div class="info-row">
                <span class="info-label">Classe :</span>
                <span class="info-value">
                    {{ $payment->studentEnrollment->classGroup->full_name }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Section :</span>
                <span class="info-value">
                    {{ $payment->studentEnrollment->classGroup->level->section->name }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Date paiement :</span>
                <span class="info-value">
                    {{ $payment->payment_date->format('d/m/Y') }}
                    &nbsp;{{ $payment->created_at->format('H:i') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── OBJET ────────────────────────────────────────────────────────── --}}
    <div class="object-bar">
        <span class="object-label">Objet du paiement :</span>
        <span class="object-value">{{ $payment->feeInstallment?->label ?? '—' }}</span>
        @if($payment->reference)
            <span class="object-label">Réf :</span>
            <span class="object-value" style="font-size:11px;">
                {{ $payment->reference }}
            </span>
        @endif
        <span class="mode-pill">{{ $payment->payment_method_label }}</span>
    </div>

    {{-- ── MONTANTS ─────────────────────────────────────────────────────── --}}
    <table class="amounts-table">
        <tr class="row-total">
            <td>Total des frais scolaires</td>
            <td>{{ number_format($totalDue, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="row-paid">
            <td>MONTANT DU PRÉSENT PAIEMENT</td>
            <td>{{ number_format($payment->amount_paid, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="row-remaining">
            <td>Reste à payer après ce paiement</td>
            <td>{{ number_format($totalRemaining, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    {{-- ── FOOTER ───────────────────────────────────────────────────────── --}}
    <div class="footer-bar">
        <div class="footer-cashier">
            Caissier(e) :&nbsp;
            <span>{{ $payment->recordedBy?->name ?? 'Système' }}</span>
        </div>
        <div class="footer-signature">Signature &amp; Cachet</div>
    </div>
    <div class="nb-bar">
        ⚠&nbsp; NB : AUCUN FRAIS N'EST REMBOURSABLE !
    </div>

</div>
</body>
</html>