<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Préparation du bulletin de paie — {{ $staff->full_name }}</title>
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; color: #111827; background: #fff; }
        .panel { max-width: 720px; margin: 0 auto; border: 1px solid #cbd5e1; border-radius: 10px; padding: 22px; }
        .title { font-size: 22px; font-weight: 900; color: #1A3A6B; text-transform: uppercase; margin-bottom: 8px; }
        .subtitle { font-size: 12px; color: #475569; margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #0f172a; }
        input, select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
        .btn { display: inline-block; margin-top: 16px; padding: 10px 16px; border: none; border-radius: 6px; background: #1A3A6B; color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .meta { margin-bottom: 14px; font-size: 14px; font-weight: 500; color: #000; }
        .meta-value { font-weight: 700; color: #000; font-size: 16px; line-height: 2; }
    </style>
</head>
<body>
<div class="panel">
    <div class="title">Préparation du bulletin de paie</div>
    <div class="subtitle">Renseignez le montant réellement perçu pour ce mois avant l’impression.</div>

    <div class="meta">
        <div><strong>Employé :</strong> <span class="meta-value">{{ $staff->full_name }}</span></div>
        <div><strong>Contrat :</strong> <span class="meta-value">{{ $staff->contract_label }}</span></div>
    </div>

    <form method="POST" action="{{ route('staff.pay-slip.store', $staff) }}">
        @csrf
        <div style="display:flex;gap:12px;align-items:flex-end;margin-bottom:12px;">
            <div style="flex:1;">
                <label for="amount_received">Montant perçu pour le mois</label>
                <input id="amount_received" name="amount_received" type="number" step="0.01" min="0" placeholder="Ex. 250000" value="{{ old('amount_received', $amountReceived) }}">
            </div>
            <div style="width:220px;">
                <label for="period">Période</label>
                @php
                    $periodStart = $activeYear && $activeYear->start_date
                        ? \Carbon\Carbon::parse($activeYear->start_date)->startOfMonth()
                        : now()->startOfMonth();
                    $periodEnd = $activeYear && $activeYear->end_date
                        ? \Carbon\Carbon::parse($activeYear->end_date)->endOfMonth()
                        : now()->endOfMonth();
                    $periodCursor = $periodStart->copy();
                    $periodOptions = collect();
                    while ($periodCursor->lte($periodEnd)) {
                        $periodOptions->push([
                            'value' => $periodCursor->format('Y-m'),
                            'label' => $periodCursor->locale('fr')->translatedFormat('F Y'),
                        ]);
                        $periodCursor->addMonth();
                    }
                    $selectedPeriod = old('period', now()->format('Y-m'));
                    if (! $periodOptions->contains(fn ($option) => $option['value'] === $selectedPeriod)) {
                        $selectedPeriod = $periodOptions->first()['value'] ?? now()->format('Y-m');
                    }
                @endphp
                <select id="period" name="period">
                    @foreach($periodOptions as $option)
                        <option value="{{ $option['value'] }}" {{ $selectedPeriod === $option['value'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
            <button type="submit" class="btn" style="background:#0b5f3a;">Enregistrer</button>
            <button type="submit" formaction="{{ route('staff.pay-slip.preview', $staff) }}" formtarget="_blank" class="btn">Prévisualiser avant impression</button>
            <a href="{{ route('staff.pay-slip.annual', $staff) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-[#1A3A6B] text-sm font-semibold bg-white hover:bg-[#F8FAFC] btn" style="background:#0b5f3a; color:#fff;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="#fff" width="16" height="16"><path d="M6 2a1 1 0 00-1 1v2H3a1 1 0 00-1 1v1h16V6a1 1 0 00-1-1h-2V3a1 1 0 00-1-1H6z"/><path fill-rule="evenodd" d="M3 10h14v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4z" clip-rule="evenodd"/></svg>
                <span>Récapitulatif annuel</span>
            </a>
        </div>
    </form>
</div>
</body>
</html>
