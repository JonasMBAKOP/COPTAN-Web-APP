<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu {{ $payment->receipt_number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', sans-serif; font-size:13px;
               color:#1a1a1a; background:#f5f5f5; }
        .page { max-width:520px; margin:20px auto; background:#fff;
                border-radius:12px; overflow:hidden;
                box-shadow:0 2px 20px rgba(0,0,0,.1); }
        .header { background:#1A3A6B; color:#fff; padding:24px 28px;
                  text-align:center; }
        .header h1 { font-size:20px; font-weight:900; letter-spacing:.5px; }
        .header p { font-size:11px; opacity:.8; margin-top:4px; }
        .receipt-num { background:#E87722; color:#fff; text-align:center;
                       padding:10px; font-size:12px; font-weight:700;
                       letter-spacing:1px; }
        .body { padding:24px 28px; }
        .row { display:flex; justify-content:space-between;
               padding:8px 0; border-bottom:1px solid #f0f0f0; }
        .row:last-child { border-bottom:none; }
        .label { color:#666; font-size:11px; text-transform:uppercase;
                 letter-spacing:.5px; }
        .value { font-weight:600; color:#1a1a1a; text-align:right; }
        .amount-box { background:#1A3A6B; color:#fff; border-radius:10px;
                      padding:16px 20px; margin:20px 0;
                      display:flex; justify-content:space-between;
                      align-items:center; }
        .amount-box .amount { font-size:24px; font-weight:900; }
        .footer { padding:16px 28px; background:#f9f9f9;
                  border-top:1px solid #eee; text-align:center;
                  font-size:11px; color:#999; }
        .stamp { border:2px solid #1A5C2A; color:#1A5C2A; border-radius:50%;
                 width:80px; height:80px; display:flex; align-items:center;
                 justify-content:center; margin:0 auto 12px; font-weight:900;
                 font-size:11px; text-align:center; text-transform:uppercase; }
        @media print {
            body { background:#fff; }
            .page { box-shadow:none; margin:0; max-width:100%; }
            .no-print { display:none; }
        }
    </style>
</head>
<body>

{{-- Bouton imprimer --}}
<div class="no-print" style="text-align:center; padding:16px;">
    <button onclick="window.print()"
            style="background:#1A3A6B; color:#fff; border:none; padding:10px 24px;
                   border-radius:8px; cursor:pointer; font-size:13px;
                   font-weight:600;">
        🖨 Imprimer le reçu
    </button>
</div>

<div class="page">
    {{-- En-tête --}}
    <div class="header">
        @if($school->logo)
        <img src="{{ asset('storage/' . $school->logo) }}"
             style="height:40px; margin-bottom:8px;">
        @endif
        <h1>{{ $school->short_name ?? 'COPTAN' }}</h1>
        <p>{{ $school->full_name }}</p>
        @if($school->address)
        <p>{{ $school->address }}</p>
        @endif
    </div>

    {{-- Numéro de reçu --}}
    <div class="receipt-num">
        REÇU DE PAIEMENT — N° {{ $payment->receipt_number }}
    </div>

    <div class="body">

        {{-- Infos élève --}}
        <div style="margin-bottom:16px;">
            <div class="row">
                <span class="label">Élève</span>
                <span class="value">
                    {{ $payment->studentEnrollment?->student?->full_name }}
                </span>
            </div>
            <div class="row">
                <span class="label">Matricule</span>
                <span class="value">
                    {{ $payment->studentEnrollment?->student?->matricule }}
                </span>
            </div>
            <div class="row">
                <span class="label">Classe</span>
                <span class="value">
                    {{ $payment->studentEnrollment?->classGroup?->full_name }}
                </span>
            </div>
            <div class="row">
                <span class="label">Année scolaire</span>
                <span class="value">
                    {{ $payment->studentEnrollment?->academicYear?->label }}
                </span>
            </div>
        </div>

        {{-- Montant --}}
        <div class="amount-box">
            <div>
                <div style="font-size:11px;opacity:.7;text-transform:uppercase;
                            letter-spacing:.5px;">
                    {{ $payment->feeInstallment?->label }}
                </div>
                <div style="font-size:12px;opacity:.6;margin-top:2px;">
                    {{ $payment->payment_method_label }}
                    @if($payment->reference)
                    · Réf: {{ $payment->reference }}
                    @endif
                </div>
            </div>
            <div class="amount">
                {{ number_format($payment->amount_paid) }}
                <span style="font-size:14px;font-weight:400;">FCFA</span>
            </div>
        </div>

        {{-- Infos paiement --}}
        <div>
            <div class="row">
                <span class="label">Date de paiement</span>
                <span class="value">
                    {{ $payment->payment_date->format('d/m/Y') }}
                </span>
            </div>
            <div class="row">
                <span class="label">Enregistré par</span>
                <span class="value">
                    {{ $payment->recordedBy?->name ?? 'Système' }}
                </span>
            </div>
            <div class="row">
                <span class="label">Date d'émission</span>
                <span class="value">
                    {{ now()->format('d/m/Y à H:i') }}
                </span>
            </div>
        </div>

        {{-- Cachet --}}
        <div style="margin-top:20px; text-align:center;">
            <div class="stamp">Payé ✓</div>
        </div>

    </div>

    <div class="footer">
        Ce reçu est un document officiel de {{ $school->full_name }}.
        Conservez-le précieusement.
    </div>
</div>

</body>
</html>