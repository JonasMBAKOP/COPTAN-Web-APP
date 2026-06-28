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
    color: #334155;
    background: #F7FAFC;
    padding: 10px;
}

/*
 * UNE PAGE A4 PORTRAIT = DEUX REÇUS EMPILÉS
 * A4 portrait utile : 190mm × 277mm
 * Chaque reçu : ~190mm large × ~132mm haut  (forme paysage)
 * 2 reçus + gap 5mm = 132 + 5 + 132 = 269mm  OK (< 277mm)
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
    border: 2.5px solid #7FA6C4;
    border-radius: 3px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.receipt-card.empty {
    height: 132mm;
    border: 2px dashed #E4EEF7;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #E4EEF7;
    font-size: 11px;
    font-style: italic;
}

/* ── HEADER ───────────────────────────────────────────────────────────── */
.card-header {
    background: #7FA6C4;
    color: #fff;
    display: flex;
    align-items: stretch;
    flex-shrink: 0;
}
.card-header-left {
    flex: 3;
    padding: 7px 10px;
    border-right: 2px solid #EDF6FC;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.logo-sm {
    width: 34px; height: 34px;
    background: #EDF6FC;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 900;
    color: #A87B24; flex-shrink: 0;
}
.school-nm { font-size: 10px; font-weight: 900; line-height: 1.3; }
.school-sm { font-size: 8px; font-weight: 700; color: #F6FAFD; margin-top: 1px; }

.card-header-right {
    flex: 1.3;
    padding: 7px 10px;
    display: flex; flex-direction: column;
    justify-content: center; gap: 3px;
    min-width: 0;
}
.card-title { font-size: 9.5px; font-weight: 900; color: #A87B24; text-transform: uppercase; }
.card-num   { font-size: 9.5px; font-weight: 900; color: #fff; font-family: 'Courier New', monospace; }
.card-year  { font-size: 8px; font-weight: 700; color: #F6FAFD; }

/* ── INFORMATIONS ÉLÈVE ──────────────────────────────────────────────── */
.card-student {
    display: grid;
    grid-template-columns: 1fr 1fr;  /* ← 2 colonnes côte à côte */
    border-bottom: 1.5px solid #7FA6C4;
    flex-shrink: 0;
}
.card-stu-col {
    padding: 6px 10px;
    border-right: 1.5px solid #E4EEF7;
}
.card-stu-col:last-child { border-right: none; }

.mini-badge {
    font-size: 7px; font-weight: 900; text-transform: uppercase;
    letter-spacing: 1px; color: #7FA6C4; background: #F4F9FD;
    padding: 1px 5px; border-radius: 2px;
    display: inline-block; margin-bottom: 4px;
}
.mini-row  { display: flex; gap: 4px; margin-bottom: 2px; align-items: baseline; }
.mini-lbl  { font-size: 7.5px; font-weight: 900; color: #64748B; min-width: 60px; text-transform: uppercase; }
.mini-val  { font-size: 9px; font-weight: 900; color: #334155; }

/* ── OBJET ────────────────────────────────────────────────────────────── */
.card-object {
    background: #F4F9FD; border-bottom: 1.5px solid #7FA6C4;
    padding: 5px 10px; display: flex;
    align-items: center; gap: 10px;
    flex-shrink: 0;
}
.obj-lbl { font-size: 7.5px; font-weight: 900; text-transform: uppercase; color: #64748B; }
.obj-val { font-size: 10.5px; font-weight: 900; color: #7FA6C4; text-transform: uppercase; }
.mode-sm {
    margin-left: auto;
    background: #7FA6C4; color: #fff;
    font-size: 8.5px; font-weight: 900;
    padding: 2px 8px; border-radius: 2px; text-transform: uppercase;
}

/* ── MONTANTS ────────────────────────────────────────────────────────── */
.card-amounts { flex: 1; }
.amt-row {
    display: flex; justify-content: space-between;
    align-items: center;
    padding: 5px 10px;
    border-bottom: 1px solid #E4EEF7;
}
.amt-row:last-child { border-bottom: none; }
.amt-lbl { font-size: 9px; font-weight: 800; color: #374151; }
.amt-val {
    font-size: 11.5px; font-weight: 900;
    font-family: 'Courier New', monospace;
    color: #7FA6C4;
}
.amt-row.highlight { background: #7FA6C4; }
.amt-row.highlight .amt-lbl { color: #fff; font-size: 9px; }
.amt-row.highlight .amt-lbl::before { content: '▶ '; }
.amt-row.highlight .amt-val { color: #A87B24; font-size: 14px; }

/* ── PIED DE CARTE ───────────────────────────────────────────────────── */
.card-footer {
    background: #7FA6C4;
    padding: 5px 10px;
    display: flex; align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.foot-cashier { font-size: 8.5px; font-weight: 900; color: #F6FAFD; }
.foot-cashier span { color: #fff; }
.foot-sig {
    font-size: 8px; font-weight: 800; color: #F6FAFD;
    border-top: 1px solid #F6FAFD; padding-top: 2px;
    min-width: 90px; text-align: center;
}
.card-nb {
    background: #FFF9EC; border-top: 2px solid #E4C978;
    padding: 4px 10px; text-align: center;
    font-size: 9px; font-weight: 900; color: #7A5A16;
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
    background: #7FA6C4; color: #fff; border: none;
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
        <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:6px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 18H5a2 2 0 01-2-2v-5a2 2 0 012-2h14a2 2 0 012 2v5a2 2 0 01-2 2h-1M7 14h10v6H7z"/></svg>Imprimer {{ $receiptsData->count() }} reçu(s)
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
                      style="color:{{ $rem > 0 ? '#B76E79' : '#60906F' }};">
                    Reste à payer après ce paiement
                </span>
                <span class="amt-val"
                      style="color:{{ $rem > 0 ? '#B76E79' : '#60906F' }};">
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
            <svg style="width:13px;height:13px;vertical-align:-2px;margin-right:5px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> NB : AUCUN FRAIS N'EST REMBOURSABLE !
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