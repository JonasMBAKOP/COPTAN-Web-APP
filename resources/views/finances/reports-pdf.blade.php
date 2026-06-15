<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Rapport Financier</title>
        @include('students.documents.partials.base-styles')
        <style>
            @page { size: A4 portrait; margin: 2mm 3mm; }
            * { margin:0; padding:0; box-sizing:border-box; }
            body { font-family: Arial, sans-serif; font-size:10px; color:#111827; background:#fff; }

            .header { display:none !important; background:#0B2545; color:#fff; padding:10px 14px;
                    display:flex; justify-content:space-between; align-items:center;
                    border-radius:4px 4px 0 0; }
            .header h1 { font-size:14px; font-weight:900; }
            .header p  { font-size:9px; opacity:.7; margin-top:2px; }
            .header-right { text-align:right; }
            .header-right .title { font-size:11px; font-weight:900; color:#FFD080; }
            .header-right .sub   { font-size:9px; opacity:.7; }

            .kpi-row { display:flex; gap:8px; margin:10px 0; }
            .kpi-box { flex:1; background:#EBF3FB; border-radius:6px; padding:8px 10px;
                    border-left:3px solid #1A3A6B; }
            .kpi-box .label { font-size:8px; font-weight:700; color:#4A5568;
                            text-transform:uppercase; letter-spacing:.5px; }
            .kpi-box .value { font-size:15px; font-weight:900; color:#1A3A6B;
                            margin-top:2px; }

            .finance-report-title { text-align:center; margin:6px 0 10px; }
            .finance-report-title .main { font-size:15px; font-weight:900; color:#111827; text-transform:uppercase; }
            .finance-report-title .sub { font-size:9px; color:#4B5563; margin-top:2px; }
            .section-title { background:#F3F4F6; color:#111827; padding:5px 10px;
                            font-size:9px; font-weight:900; text-transform:uppercase;
                            letter-spacing:.7px; margin:10px 0 6px; border-radius:3px;
                            border:1px solid #D1D5DB; }

            .bar-row { display:flex; align-items:center; gap:6px; margin-bottom:5px; }
            .bar-label { font-size:9px; font-weight:700; width:130px;
                        white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
            .bar-track { flex:1; height:8px; background:#EBF3FB; border-radius:4px; overflow:hidden; }
            .bar-fill  { height:8px; border-radius:4px;
                        background:linear-gradient(to right,#0B2040,#2D6FD4); }
            .bar-value { font-size:9px; font-weight:900; color:#1A3A6B; white-space:nowrap; }

            table { width:100%; border-collapse:collapse; font-size:9px; }
            thead tr { background:#F3F4F6; color:#111827; }
            thead th { padding:5px 7px; text-align:left; font-weight:700;
                    font-size:8.5px; text-transform:uppercase; letter-spacing:.4px; }
            thead th.right { text-align:right; }
            tbody tr { border-bottom:1px solid #E5E7EB; }
            tbody tr:nth-child(even) { background:#F8FAFC; }
            tbody td { padding:4px 7px; }
            tbody td.right { text-align:right; font-weight:800; }
            tfoot tr { background:#EBF3FB; }
            tfoot td { padding:5px 7px; font-weight:900; font-size:10px; }
            tfoot td.right { text-align:right; color:#1A3A6B; }

            .two-col { display:flex; gap:10px; margin:8px 0; }
            .two-col > div { flex:1; }

            .footer { text-align:center; margin-top:8px; font-size:8px;
                    color:#9CA3AF; border-top:1px solid #E5E7EB; padding-top:6px; }
            .nb { background:#FFF3CD; border:1px solid #C8A415; color:#5C3D00;
                font-weight:900; font-size:9px; padding:5px 10px; border-radius:4px;
                text-align:center; margin-top:8px; }
        </style>
    </head>
    <body>
        @include('students.documents.partials.certificate-official-header', [
            'showCertificateTitle' => false,
        ])

        <div class="finance-report-title">
            <div class="main">
                Rapport financier {{ $type === 'mensuel' ? 'mensuel' : 'annuel' }}
            </div>
            <div class="sub">
                {{ $selectedYear?->label ?? '—' }}
                @if($type === 'mensuel')
                    · {{ ['Janvier','Février','Mars','Avril','Mai','Juin',
                        'Juillet','Août','Septembre','Octobre','Novembre','Décembre'][$month-1] }}
                @endif
                · Généré le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>

        {{-- HEADER --}}
        <div class="header">
            <div>
                <h1>{{ strtoupper($school->full_name ?? 'COLLÈGE POLYVALENT NTANKEU') }}</h1>
                <p>
                    @if($phones->isNotEmpty())
                        Tél : @foreach($phones->take(2) as $ph){{ $ph->number }}{{ !$loop->last ? ' / ' : '' }}@endforeach
                        &nbsp;|&nbsp;
                    @endif
                    {{ $school->address ?? '' }}
                </p>
            </div>
            <div class="header-right">
                <div class="title">
                    RAPPORT FINANCIER
                    {{ $type === 'mensuel' ? 'MENSUEL' : 'ANNUEL' }}
                </div>
                <div class="sub">
                    {{ $selectedYear?->label ?? '—' }}
                    @if($type === 'mensuel')
                        ·
                        {{ ['Janvier','Février','Mars','Avril','Mai','Juin',
                            'Juillet','Août','Septembre','Octobre','Novembre','Décembre'][$month-1] }}
                    @endif
                </div>
                <div class="sub">
                    @if($whoFilter === 'global') Rapport Global
                    @elseif($whoFilter === 'me') {{ $user->name }}
                    @else Économe @endif
                    &nbsp;·&nbsp; Généré le {{ now()->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        {{-- KPI --}}
        <div class="kpi-row">
            <div class="kpi-box">
                <div class="label">Total collecté</div>
                <div class="value">{{ number_format($totalCollected) }} FCFA</div>
            </div>
            <div class="kpi-box">
                <div class="label">Nb paiements</div>
                <div class="value">{{ $allPayments->count() }}</div>
            </div>
            <div class="kpi-box">
                <div class="label">Espèces</div>
                @php $cash = $allPayments->where('payment_method','cash')->sum('amount_paid'); @endphp
                <div class="value" style="color:#C8A415;">{{ number_format($cash) }} FCFA</div>
            </div>
            <div class="kpi-box">
                <div class="label">Mobile Money</div>
                @php $mm = $allPayments->whereIn('payment_method',['orange_money','mtn_momo'])->sum('amount_paid'); @endphp
                <div class="value" style="color:#7C3AED;">{{ number_format($mm) }} FCFA</div>
            </div>
            <div class="kpi-box">
                <div class="label">Virement</div>
                @php $vir = $allPayments->where('payment_method','bank_transfer')->sum('amount_paid'); @endphp
                <div class="value" style="color:#1A5C2A;">{{ number_format($vir) }} FCFA</div>
            </div>
        </div>

        {{-- 2 colonnes : par tranche + par mode --}}
        <div class="two-col">

            {{-- Par tranche --}}
            <div>
                <div class="section-title">Par tranche de paiement</div>
                @php $maxI = $byInstallment->max('total') ?: 1; @endphp
                @foreach($byInstallment as $inst)
                <div class="bar-row">
                    <span class="bar-label">{{ $inst['label'] }}</span>
                    <div class="bar-track">
                        <div class="bar-fill"
                            style="width:{{ round(($inst['total']/$maxI)*100) }}%;">
                        </div>
                    </div>
                    <span class="bar-value">
                        {{ number_format($inst['total']) }} F
                        <span style="font-weight:400;color:#9CA3AF;">({{ $inst['count'] }})</span>
                    </span>
                </div>
                @endforeach
            </div>

            {{-- Par mode --}}
            <div>
                <div class="section-title">Par mode de paiement</div>
                @php $maxM = $byMethod->max('total') ?: 1; @endphp
                @foreach($byMethod as $m)
                <div class="bar-row">
                    <span class="bar-label">{{ $m['label'] }}</span>
                    <div class="bar-track">
                        <div class="bar-fill"
                            style="width:{{ round(($m['total']/$maxM)*100) }}%;
                                    background:linear-gradient(to right,#E87722,#F59E0B);">
                        </div>
                    </div>
                    <span class="bar-value">
                        {{ number_format($m['total']) }} F
                        <span style="font-weight:400;color:#9CA3AF;">({{ $m['count'] }})</span>
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tableau détaillé --}}
        <div class="section-title">Détail des paiements</div>
        <table>
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Tranche</th>
                    <th class="right">Montant</th>
                    <th>Mode</th>
                    <th>Date</th>
                    <th>Caissier</th>
                    <th>N° Reçu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allPayments as $p)
                <tr>
                    <td>{{ $p->studentEnrollment?->student?->full_name }}</td>
                    <td>{{ $p->studentEnrollment?->classGroup?->full_name }}</td>
                    <td>{{ $p->feeInstallment?->label ?? '—' }}</td>
                    <td class="right">{{ number_format($p->amount_paid) }} FCFA</td>
                    <td>{{ $p->payment_method_label }}</td>
                    <td>{{ $p->payment_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $p->recordedBy?->name ?? '—' }}</td>
                    <td style="font-family:monospace;">{{ $p->receipt_number }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td class="right">{{ number_format($totalCollected) }} FCFA</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>

        <div class="nb">⚠ NB : AUCUN FRAIS N'EST REMBOURSABLE !</div>

        <div class="footer">
            {{ $school->full_name ?? 'COPTAN' }} &nbsp;·&nbsp;
            Rapport généré le {{ now()->format('d/m/Y à H:i') }} &nbsp;·&nbsp;
            Page 1
        </div>

    </body>
</html>
