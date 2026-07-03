<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport financier — {{ $selectedYear?->label ?? '' }}</title>
    @include('students.documents.partials.base-styles')
    <style>
        @page { size: A4 portrait; margin: 4mm 5mm; }

        .finance-doc-title {
            background: #d9d9d9;
            border-top: 1px solid #9CA3AF;
            border-bottom: 1px solid #9CA3AF;
            padding: 10px 12px;
            text-align: center;
            font-family: Georgia, 'Times New Roman', serif;
            margin: 12px 0 16px;
        }
        .finance-doc-title .main {
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.1;
            color: #111827;
        }
        .finance-doc-title .sub {
            font-size: 12px;
            color: #4B5563;
            margin-top: 6px;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 700;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }
        .kpi-box {
            background: #F8FAFC;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 10px 12px;
        }
        .kpi-box .label {
            font-size: 9px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .kpi-box .value {
            font-size: 12px;
            font-weight: 900;
            color: #1A3A6B;
            margin-top: 4px;
        }

        .section-title {
            background: #F3F4F6;
            color: #374151;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 12px 0 8px;
            border-radius: 3px;
            border: 1px solid #E5E7EB;
        }

        .bar-row { margin-bottom: 5px; }
        .bar-meta { display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 8.5px; }
        .bar-label { font-weight: 700; }
        .bar-value { font-weight: 800; color: #1A3A6B; }
        .bar-track { height: 6px; background: #EEF2F7; border-radius: 3px; overflow: hidden; }
        .bar-fill { height: 6px; background: #4A6FA5; border-radius: 3px; }

        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        thead tr { background: #F3F4F6; }
        thead th {
            padding: 4px 5px;
            text-align: left;
            font-weight: 700;
            font-size: 7.5px;
            text-transform: uppercase;
            border: 1px solid #E5E7EB;
        }
        thead th.right { text-align: right; }
        tbody td { padding: 3px 5px; border: 1px solid #E5E7EB; }
        tbody tr:nth-child(even) { background: #FAFAFA; }
        tbody td.right { text-align: right; font-weight: 700; }
        tfoot tr { background: #F3F4F6; }
        tfoot td { padding: 4px 5px; font-weight: 900; border: 1px solid #E5E7EB; }
        tfoot td.right { text-align: right; color: #1A3A6B; }

        .footer-note {
            text-align: center;
            margin-top: 12px;
            font-size: 8px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 6px;
        }
        .nb {
            background: #FFFBEB;
            border: 1px solid #E5E7EB;
            color: #78350F;
            font-weight: 700;
            font-size: 8.5px;
            padding: 5px 8px;
            border-radius: 3px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
@include('students.documents.partials.print-toolbar')

<div class="page cert-page">
    @include('students.documents.partials.certificate-official-header', [
        'showCertificateTitle' => false,
    ])

    <div class="finance-doc-title">
        <div class="main">
            Rapport financier {{ $type === 'mensuel' ? 'mensuel' : 'annuel' }}
        </div>
        <div class="sub">
            {{ $selectedYear?->label ?? '—' }}
            @if($type === 'mensuel')
                · {{ ['Janvier','Février','Mars','Avril','Mai','Juin',
                    'Juillet','Août','Septembre','Octobre','Novembre','Décembre'][$month-1] }}
            @elseif($selectedYear)
                · {{ $selectedYear->start_date?->locale('fr')->translatedFormat('F Y') }}
                — {{ $selectedYear->end_date?->locale('fr')->translatedFormat('F Y') }}
            @endif
            ·
            @if($whoFilter === 'global') Rapport global
            @elseif($whoFilter === 'me') {{ $user->name }}
            @else Économe @endif
            · Généré le {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>

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
            <div class="value">{{ number_format($cash) }} FCFA</div>
        </div>
        <div class="kpi-box">
            <div class="label">Paiements Mobiles</div>
            @php $mm = $allPayments->whereIn('payment_method',['orange_money','mtn_momo'])->sum('amount_paid'); @endphp
            <div class="value">{{ number_format($mm) }} FCFA</div>
        </div>
        <div class="kpi-box">
            <div class="label">Virement</div>
            @php $vir = $allPayments->where('payment_method','bank_transfer')->sum('amount_paid'); @endphp
            <div class="value">{{ number_format($vir) }} FCFA</div>
        </div>
    </div>

    <div class="two-col">
        <div>
            <div class="section-title">Par tranche de paiement</div>
            @php $maxI = $byInstallment->max('total') ?: 1; @endphp
            @foreach($byInstallment as $inst)
            <div class="bar-row">
                <div class="bar-meta">
                    <span class="bar-label">{{ $inst['label'] }}</span>
                    <span class="bar-value">
                        {{ number_format($inst['total']) }} F
                        <span style="font-weight:400;color:#9CA3AF;">({{ $inst['count'] }})</span>
                    </span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ round(($inst['total']/$maxI)*100) }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>

        <div>
            <div class="section-title">Par mode de paiement</div>
            @php $maxM = $byMethod->max('total') ?: 1; @endphp
            @foreach($byMethod as $m)
            <div class="bar-row">
                <div class="bar-meta">
                    <span class="bar-label">{{ $m['label'] }}</span>
                    <span class="bar-value">
                        {{ number_format($m['total']) }} F
                        <span style="font-weight:400;color:#9CA3AF;">({{ $m['count'] }})</span>
                    </span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ round(($m['total']/$maxM)*100) }}%; background:#6B7280;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

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
                <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                <td>{{ $p->recordedBy?->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                <td class="right">{{ number_format($totalCollected) }} FCFA</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <div class="nb">NB : AUCUN FRAIS N'EST REMBOURSABLE.</div>

    <div class="footer-note">
        {{ $school->full_name ?? 'COPTAN' }} · Rapport généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>
</body>
</html>
