<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Impression groupée — {{ $receiptsData->count() }} reçu(s)</title>
<style>
/* ── PRINT ──────────────────────────────────────────────────────────── */
@page {
    size: A4 portrait;     /* ← papier portrait */
    margin: 10mm 12mm;
}
@media print { .no-print { display: none !important; } }

* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    font-weight: 700;
    color: #050E1F;
    background: #e8edf2;
    padding: 10px;
}

/*
 * UNE PAGE A4 PORTRAIT = DEUX REÇUS EMPILÉS
 * A4 portrait utile : 190mm × 277mm
 * Chaque reçu : ~190mm large × ~132mm haut  (forme paysage)
 * 2 reçus + gap 5mm = 132 + 5 + 132 = 269mm  ✓ (< 277mm)
 */

/* ── CONTENEUR D'UNE PAGE (2 reçus empilés) ─────────────────────────── */
.receipt-page {
    display: flex;
    flex-direction: column;       /* ← empilés verticalement */
    gap: 10mm;
    page-break-after: always;
    margin-bottom: 20px;
    margin-top: 20px;
    /* En impression : hauteur gérée par le contenu */
}
.receipt-page:last-child { page-break-after: auto; }

/* ── CARTE REÇU (forme paysage dans un papier portrait) ──────────────── */
.receipt-card {
    width: 100%;
    background: #fff;
    border: 2.5px solid #0B2545;
    border-radius: 3px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.receipt-card.empty {
    height: 132mm;
    border: 2px dashed #C8D8EC;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #C8D8EC;
    font-size: 11px;
    font-style: italic;
}

/* ── HEADER ───────────────────────────────────────────────────────────── */
.card-header {
    background: #0B2545;
    color: #fff;
    display: flex;
    align-items: stretch;
    flex-shrink: 0;
}
.card-header-left {
    flex: 3;
    padding: 7px 10px;
    border-right: 2px solid #1A4070;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.logo-sm {
    width: 34px; height: 34px;
    background: #1A4070;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 900;
    color: #FFD080; flex-shrink: 0;
}
.school-nm { font-size: 10px; font-weight: 900; line-height: 1.3; }
.school-sm { font-size: 8px; font-weight: 700; color: #8BAFD4; margin-top: 1px; }

.card-header-right {
    flex: 1.3;
    padding: 7px 10px;
    display: flex; flex-direction: column;
    justify-content: center; gap: 3px;
    min-width: 0;
}
.card-title { font-size: 9.5px; font-weight: 900; color: #FFD080; text-transform: uppercase; }
.card-num   { font-size: 9.5px; font-weight: 900; color: #fff; font-family: 'Courier New', monospace; }
.card-year  { font-size: 8px; font-weight: 700; color: #8BAFD4; }

/* ── INFORMATIONS ÉLÈVE ──────────────────────────────────────────────── */
.card-student {
    display: grid;
    grid-template-columns: 1fr 1fr;  /* ← 2 colonnes côte à côte */
    border-bottom: 1.5px solid #0B2545;
    flex-shrink: 0;
}
.card-stu-col {
    padding: 6px 10px;
    border-right: 1.5px solid #C8D8EC;
}
.card-stu-col:last-child { border-right: none; }

.mini-badge {
    font-size: 7px; font-weight: 900; text-transform: uppercase;
    letter-spacing: 1px; color: #0B2545; background: #E8EFF8;
    padding: 1px 5px; border-radius: 2px;
    display: inline-block; margin-bottom: 4px;
}
.mini-row  { display: flex; gap: 4px; margin-bottom: 2px; align-items: baseline; }
.mini-lbl  { font-size: 7.5px; font-weight: 900; color: #4A5568; min-width: 60px; text-transform: uppercase; }
.mini-val  { font-size: 9px; font-weight: 900; color: #050E1F; }

/* ── OBJET ────────────────────────────────────────────────────────────── */
.card-object {
    background: #E8EFF8; border-bottom: 1.5px solid #0B2545;
    padding: 5px 10px; display: flex;
    align-items: center; gap: 10px;
    flex-shrink: 0;
}
.obj-lbl { font-size: 7.5px; font-weight: 900; text-transform: uppercase; color: #4A5568; }
.obj-val { font-size: 10.5px; font-weight: 900; color: #0B2545; text-transform: uppercase; }
.mode-sm {
    margin-left: auto;
    background: #0B2545; color: #fff;
    font-size: 8.5px; font-weight: 900;
    padding: 2px 8px; border-radius: 2px; text-transform: uppercase;
}

/* ── MONTANTS ────────────────────────────────────────────────────────── */
.card-amounts { flex: 1; }
.amt-row {
    display: flex; justify-content: space-between;
    align-items: center;
    padding: 5px 10px;
    border-bottom: 1px solid #C8D8EC;
}
.amt-row:last-child { border-bottom: none; }
.amt-lbl { font-size: 9px; font-weight: 800; color: #374151; }
.amt-val {
    font-size: 11.5px; font-weight: 900;
    font-family: 'Courier New', monospace;
    color: #0B2545;
}
.amt-row.highlight { background: #0B2545; }
.amt-row.highlight .amt-lbl { color: #fff; font-size: 9px; }
.amt-row.highlight .amt-lbl::before { content: '▶ '; }
.amt-row.highlight .amt-val { color: #FFD080; font-size: 14px; }

/* ── PIED DE CARTE ───────────────────────────────────────────────────── */
.card-footer {
    background: #0B2545;
    padding: 5px 10px;
    display: flex; align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.foot-cashier { font-size: 8.5px; font-weight: 900; color: #8BAFD4; }
.foot-cashier span { color: #fff; }
.foot-sig {
    font-size: 8px; font-weight: 800; color: #8BAFD4;
    border-top: 1px solid #8BAFD4; padding-top: 2px;
    min-width: 90px; text-align: center;
}
.card-nb {
    background: #FFF3CD; border-top: 2px solid #C8A415;
    padding: 4px 10px; text-align: center;
    font-size: 9px; font-weight: 900; color: #5C3D00;
    flex-shrink: 0;
}

/* ── SÉPARATEUR ENTRE LES 2 REÇUS (visible à l'écran, invisible à l'impression) */
.receipt-separator {
    height: 10px;
    background: repeating-linear-gradient(
        to right, #000 0, black 6px, transparent 6px, transparent 12px
    );
    margin: 0 0 2px 0;
}
@media print { .receipt-separator { display: none; } }

/* ── BOUTON IMPRIMER ─────────────────────────────────────────────────── */
.print-btn { text-align: center; margin-bottom: 12px; }
.print-btn button {
    background: #0B2545; color: #fff; border: none;
    padding: 10px 28px; border-radius: 8px;
    cursor: pointer; font-size: 13px; font-weight: 800;
}
.info-bar {
    text-align: center; margin-bottom: 8px;
    font-size: 12px; font-weight: 700; color: #374151;
}
</style>
</head>
<body>

<div class="print-btn no-print">
    <div class="info-bar">
        {{ $receiptsData->count() }} reçu(s) —
        {{ ceil($receiptsData->count() / 2) }} page(s) A4 portrait
        (2 reçus par page, empilés)
    </div>
    <button onclick="window.print()">
        🖨 Imprimer {{ $receiptsData->count() }} reçu(s)
    </button>
</div>

{{-- ── GROUPER PAR PAIRES (un en haut, un en bas) ─────────────────────── --}}
@foreach($receiptsData->chunk(3) as $pair)
<div class="receipt-page">

    @foreach($pair as $index => $item)
    @php
        $p   = $item['payment'];
        $enr = $p->studentEnrollment;
        $rem = $item['totalRemaining'];
    @endphp

    {{-- Ligne pointillée de coupe entre les 2 reçus --}}
    @if(!$loop->first)
    <div class="receipt-separator no-print"></div>
    @endif

    <div class="receipt-card">

        {{-- ── HEADER ──────────────────────────────────────────────────── --}}
        <div class="card-header">
            <div class="card-header-left">
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}"
                         style="width:34px;height:34px;border-radius:50%;
                                object-fit:contain;flex-shrink:0;">
                @else
                    <div class="logo-sm">
                        {{ strtoupper(substr($school->short_name ?? 'C', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="school-nm">
                        {{ strtoupper($school->full_name ?? 'COLLÈGE POLYVALENT NTANKEU') }}
                    </div>
                    <div class="school-sm">
                        @if($phones->isNotEmpty())
                            Tél :
                            @foreach($phones->take(2) as $ph)
                                {{ $ph->number }}{{ !$loop->last ? ' / ' : '' }}
                            @endforeach
                        @elseif($school->phone_1)
                            Tél : {{ $school->phone_1 }}
                        @endif
                        @if($school->address) &nbsp;|&nbsp; {{ $school->address }} @endif
                    </div>
                    <div class="school-sm">
                        Ministère des Enseignements Secondaires — République du Cameroun
                    </div>
                </div>
            </div>
            <div class="card-header-right">
                <div class="card-title">Reçu de Paiement Officiel</div>
                <div class="card-num">N° {{ $p->receipt_number }}</div>
                <div class="card-year">
                    Année : {{ $enr->academicYear->label }}
                    &nbsp;|&nbsp; Exemplaire Élève
                </div>
            </div>
        </div>

        {{-- ── INFOS ÉLÈVE (2 colonnes) ────────────────────────────────── --}}
        <div class="card-student">
            <div class="card-stu-col">
                <div class="mini-badge">Identité Élève</div>
                <div class="mini-row">
                    <span class="mini-lbl">Nom & prénom :</span>
                    <span class="mini-val">
                        {{ strtoupper($enr->student->full_name) }}
                    </span>
                </div>
                <div class="mini-row">
                    <span class="mini-lbl">Matricule :</span>
                    <span class="mini-val">{{ $enr->student->matricule }}</span>
                </div>
                <div class="mini-row">
                    <span class="mini-lbl">Né(e) le :</span>
                    <span class="mini-val">
                        {{ $enr->student->date_of_birth?->format('d/m/Y') ?? '—' }}
                        @if($enr->student->place_of_birth)
                            &nbsp;à&nbsp;
                            {{ strtoupper($enr->student->place_of_birth) }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="card-stu-col">
                <div class="mini-badge">Scolarité</div>
                <div class="mini-row">
                    <span class="mini-lbl">Classe :</span>
                    <span class="mini-val">
                        {{ $enr->classGroup->full_name }}
                    </span>
                </div>
                <div class="mini-row">
                    <span class="mini-lbl">Section :</span>
                    <span class="mini-val" style="font-size:8.5px;">
                        {{ $enr->classGroup->level->section->name }}
                    </span>
                </div>
                <div class="mini-row">
                    <span class="mini-lbl">Date paiement :</span>
                    <span class="mini-val">
                        {{ $p->payment_date->format('d/m/Y') }}
                        {{ $p->created_at->format('H:i') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ── OBJET ────────────────────────────────────────────────────── --}}
        <div class="card-object">
            <span class="obj-lbl">Objet :</span>
            <span class="obj-val">
                {{ $p->feeInstallment?->label ?? '—' }}
            </span>
            @if($p->reference)
                <span class="obj-lbl">Réf :</span>
                <span class="obj-val" style="font-size:9px;">
                    {{ $p->reference }}
                </span>
            @endif
            <span class="mode-sm">{{ $p->payment_method_label }}</span>
        </div>

        {{-- ── MONTANTS ─────────────────────────────────────────────────── --}}
        <div class="card-amounts">
            <div class="amt-row">
                <span class="amt-lbl">Total des frais scolaires</span>
                <span class="amt-val">
                    {{ number_format($item['totalDue'], 0, ',', ' ') }} FCFA
                </span>
            </div>
            <div class="amt-row highlight">
                <span class="amt-lbl">MONTANT DU PRÉSENT PAIEMENT</span>
                <span class="amt-val">
                    {{ number_format($p->amount_paid, 0, ',', ' ') }} FCFA
                </span>
            </div>
            <div class="amt-row">
                <span class="amt-lbl"
                      style="color:{{ $rem > 0 ? '#B22222' : '#1A6B2A' }};">
                    Reste à payer après ce paiement
                </span>
                <span class="amt-val"
                      style="color:{{ $rem > 0 ? '#B22222' : '#1A6B2A' }};">
                    {{ number_format($rem, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>

        {{-- ── PIED DE CARTE ────────────────────────────────────────────── --}}
        <div class="card-footer">
            <div class="foot-cashier">
                Caissier(e) :
                <span>{{ $p->recordedBy?->name ?? 'Système' }}</span>
            </div>
            <div class="foot-sig">Signature &amp; Cachet</div>
        </div>
        <div class="card-nb">
            ⚠&nbsp; NB : AUCUN FRAIS N'EST REMBOURSABLE !
        </div>

    </div>{{-- fin receipt-card --}}

    @endforeach

    {{-- Cellule vide si nombre impair --}}
    {{-- @if($pair->count() === 1)
    <div class="receipt-card empty">
        — Page libre —
    </div>
    @endif --}}

</div>{{-- fin receipt-page --}}
@endforeach

</body>
</html>