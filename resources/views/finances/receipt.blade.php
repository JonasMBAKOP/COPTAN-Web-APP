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
    color: #334155;
    background: #F7FAFC;
    padding: 16px;
}

.receipt {
    max-width: 268mm;
    margin: 0 auto;
    background: #fff;
    border: 3px solid #7FA6C4;
    border-radius: 4px;
    overflow: hidden;
}

/* ── HEADER ─ fix point 2 : header-right rapproché ───────────────── */
.header {
    background: #7FA6C4;
    color: #fff;
    display: flex;
    align-items: stretch;
}
.header-school {
    flex: 3;                    /* ← flex proportionnel au lieu de flex:1 */
    padding: 10px 14px;
    border-right: 2px solid #EDF6FC;
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.header-school img {
    height: 44px; width: 44px;
    flex-shrink: 0;
}
.header-school-placeholder {
    width: 44px; height: 44px;
    background: #EDF6FC; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 900; color: #fff; flex-shrink: 0;
}
.school-name { font-size: 13px; font-weight: 900; line-height: 1.3; }
.school-sub  { font-size: 9.5px; font-weight: 700; color: #F6FAFD; margin-top: 3px; }

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
    letter-spacing: 0.8px; color: #A87B24; text-transform: uppercase;
}
.receipt-num {
    font-size: 11px; font-weight: 900;
    color: #fff; font-family: 'Courier New', monospace;
}
.receipt-date-head { font-size: 9.5px; font-weight: 700; color: #F6FAFD; }

/* ── SECTION ÉTUDIANT ─ fix point 3 : student-right beaucoup rapproché */
.student-section {
    display: flex;
    border-bottom: 2px solid #7FA6C4;
}
.student-left {
    flex: 2.5;                  /* ← ratio plus équilibré */
    padding: 9px 14px;
    border-right: 2px solid #7FA6C4;
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
    color: #7FA6C4; background: #F4F9FD;
    padding: 2px 6px; border-radius: 2px;
    display: inline-block; margin-bottom: 6px;
}
.info-row {
    display: flex; align-items: baseline;
    gap: 5px; margin-bottom: 3px;
}
.info-label {
    font-size: 9px; font-weight: 900;
    color: #64748B; text-transform: uppercase;
    white-space: nowrap; min-width: 75px;
}
.info-value { font-size: 11px; font-weight: 900; color: #334155; }

/* ── BANDE OBJET ──────────────────────────────────────────────────── */
.object-bar {
    background: #F4F9FD; border-bottom: 2px solid #7FA6C4;
    padding: 7px 14px; display: flex; align-items: center; gap: 12px;
}
.object-label  { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #64748B; }
.object-value  { font-size: 12px; font-weight: 900; color: #7FA6C4; text-transform: uppercase; }
.mode-pill {
    margin-left: auto; background: #7FA6C4; color: #fff;
    font-size: 10px; font-weight: 900; padding: 3px 10px;
    border-radius: 3px; text-transform: uppercase; letter-spacing: 0.5px;
}

/* ── MONTANTS ──────────────────────────────────────────────────────── */
.amounts-table { width: 100%; border-collapse: collapse; }
.amounts-table tr { border-bottom: 1.5px solid #E4EEF7; }
.amounts-table tr:last-child { border-bottom: none; }
.amounts-table td { padding: 7px 14px; font-size: 11px; font-weight: 800; }
.amounts-table td:last-child {
    text-align: right; font-size: 13px; font-weight: 900;
    font-family: 'Courier New', monospace;
}
.row-total td:last-child { color: #7FA6C4; }
.row-paid { background: #7FA6C4; }
.row-paid td { color: #fff !important; }
.row-paid td:first-child::before { content: '▶ '; font-size: 9px; }
.row-paid td:last-child { color: #A87B24 !important; font-size: 15px !important; }
.row-remaining td:last-child {
    color: {{ $totalRemaining > 0 ? '#B76E79' : '#60906F' }} !important;
}

/* ── FOOTER ───────────────────────────────────────────────────────── */
.footer-bar {
    background: #7FA6C4; padding: 7px 14px;
    display: flex; align-items: center;
    justify-content: space-between; border-top: 2px solid #7FA6C4;
}
.footer-cashier { font-size: 10px; font-weight: 900; color: #F6FAFD; }
.footer-cashier span { color: #fff; font-size: 11px; }
.footer-signature {
    font-size: 9.5px; font-weight: 800; color: #F6FAFD;
    border-top: 1px solid #F6FAFD; padding-top: 2px;
    min-width: 120px; text-align: center;
}
.nb-bar {
    background: #FFF9EC; border-top: 2.5px solid #E4C978;
    padding: 6px 14px; text-align: center;
    font-size: 10px; font-weight: 700; color: black; letter-spacing: 0.5px;
}

/* ── BOUTON ───────────────────────────────────────────────────────── */
.print-btn { display:block; text-align:center; margin-bottom:14px; }
.print-btn button {
    background: #7FA6C4; color: #fff; border: none;
    padding: 10px 28px; border-radius: 8px; cursor: pointer;
    font-size: 13px; font-weight: 800;
}
</style>
</head>
<body>

<div class="print-btn no-print">
    <button onclick="window.print()"><svg style="width:14px;height:14px;vertical-align:-2px;margin-right:6px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 18H5a2 2 0 01-2-2v-5a2 2 0 012-2h14a2 2 0 012 2v5a2 2 0 01-2 2h-1M7 14h10v6H7z"/></svg>{{ $isEnglishReceipt ? 'Print Receipt' : 'Imprimer le reçu' }}</button>
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
                    {{ $isEnglishReceipt ? strtoupper($school->full_name_en) : strtoupper($school->full_name) }}
                </div>
                <div class="school-sub">
                    @if($phones->isNotEmpty())
                        {{ $isEnglishReceipt ? 'Phone' : 'Tél' }}
                        @foreach($phones->take(2) as $phone)
                            {{ $phone->number }}{{ !$loop->last ? ' / ' : '' }}
                        @endforeach
                    @elseif($school->phone_1)
                        {{ $isEnglishReceipt ? 'Phone' : 'Tél' }} : {{ $school->phone_1 }}
                    @endif
                    @if($school->address) &nbsp;|&nbsp; {{ $school->address }} @endif
                </div>
                <div class="school-sub">
                    @if($school->postal_box) {{ $isEnglishReceipt ? 'P.O. Box' : 'BP' }} : {{ $school->postal_box }} @endif
                </div>
                <div class="school-sub">
                    @if($school->ministry) 
                        {{ $isEnglishReceipt ? $school->ministry_en : $school->ministry }}
                    @endif
                </div>
            </div>
        </div>
        <div class="header-right" style="text-align: center;">
            <div class="receipt-title">{{ $isEnglishReceipt ? 'Official Payment Receipt' : 'Reçu de Paiement Officiel' }}</div>
            <div class="receipt-num">N° {{ $payment->receipt_number }}</div>
            <div class="receipt-date-head">
                {{ $isEnglishReceipt ? 'Student Copy' : 'Exemplaire Élève' }}
            </div>
            <div class="receipt-date-head">
                {{ $isEnglishReceipt ? 'Academic Year' : 'Année Scolaire' }} : {{ $payment->studentEnrollment->academicYear->label }}
                &nbsp;|&nbsp;
                {{ $isEnglishReceipt ? 'Date' : 'Date' }} : {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- ── INFOS ÉLÈVE ──────────────────────────────────────────────────── --}}
    <div class="student-section">
        <div class="student-left">
            <div class="section-label">{{ $isEnglishReceipt ? 'Student Identity' : 'Identité de l\'élève' }}</div>
            <div class="info-row">
                <span class="info-label">{{ $isEnglishReceipt ? 'Name' : 'Nom & prénom' }} :</span>
                <span class="info-value">
                    {{ strtoupper($payment->studentEnrollment->student->full_name) }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">{{ $isEnglishReceipt ? 'Registration N°' : 'Matricule' }} :</span>
                <span class="info-value">
                    {{ $payment->studentEnrollment->student->matricule }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">{{ $isEnglishReceipt ? 'Born On' : 'Né(e) le' }} :</span>
                <span class="info-value">
                    {{ $payment->studentEnrollment->student->date_of_birth?->format('d/m/Y') ?? '—' }}&nbsp;&nbsp;&nbsp;&nbsp;
                </span>
                @if($payment->studentEnrollment->student->place_of_birth)
                    <span class="info-label">&nbsp;&nbsp;&nbsp;&nbsp;{{ $isEnglishReceipt ? 'At' : 'à' }}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span class="info-value">
                        {{ strtoupper($payment->studentEnrollment->student->place_of_birth) }}
                    </span>
                @endif
            </div>
        </div>
        <div class="student-right">
            <div class="section-label">{{ $isEnglishReceipt ? 'Schooling' : 'Scolarité' }}</div>
            <div class="info-row">
                <span class="info-label">{{ $isEnglishReceipt ? 'Class' : 'Classe' }} :</span>
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
                <span class="info-label">{{ $isEnglishReceipt ? 'Payment Date' : 'Date paiement' }} :</span>
                <span class="info-value">
                    {{ $payment->payment_date->format('d/m/Y') }}
                    &nbsp;{{ $payment->created_at->format('H:i') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── OBJET ────────────────────────────────────────────────────────── --}}
    <div class="object-bar">
        <span class="object-label">{{ $isEnglishReceipt ? 'Payment purpose' : 'Objet du paiement' }} :</span>
        @php
            $paymentObjects = $payment->is_bulk
                ? $payment->allocations->loadMissing('feeInstallment')
                : collect([$payment]);
        @endphp
        @foreach($paymentObjects as $item)
            <span class="object-value" style="font-size:11px;">
                {{ $item->is_bulk ? 'Paiement en bloc' : ($item->feeInstallment?->label ?? '—') }}
                @if($payment->is_bulk && $item->feeInstallment)
                    ({{ number_format((int) $item->amount_paid, 0, ',', ' ') }} FCFA)
                @endif
            </span>
            @if(!$loop->last)
                <span class="object-label">•</span>
            @endif
        @endforeach
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
            <td>{{ $isEnglishReceipt ? 'Total School Fees' : 'Total des frais scolaires' }}</td>
            <td>{{ number_format($totalDue, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="row-paid">
            <td>{{ $isEnglishReceipt ? 'AMOUNT OF THIS PAYMENT' : 'MONTANT DU PRÉSENT PAIEMENT' }}</td>
            <td>{{ number_format($payment->amount_paid, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="row-remaining">
            <td>{{ $isEnglishReceipt ? 'Balance after this payment' : 'Reste à payer après ce paiement' }}</td>
            <td>{{ number_format($totalRemaining, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    {{-- ── FOOTER ───────────────────────────────────────────────────────── --}}
    <div class="footer-bar">
        <div class="footer-cashier">
            {{ $isEnglishReceipt ? 'Cashier' : 'Caissier(e)' }} :&nbsp;
            <span>{{ $payment->recordedBy?->name ?? 'Système' }}</span>
        </div>
        <div class="footer-signature">Signature &amp; Cachet</div>
    </div>
    <div class="nb-bar">
        <svg style="width:13px;height:13px;vertical-align:-2px;margin-right:5px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        {{ $isEnglishReceipt ? 'NB : NO FEES ARE REFUNDABLE !' : 'NB : AUCUN FRAIS N\'EST REMBOURSABLE !' }}
    </div>

</div>
</body>
</html>