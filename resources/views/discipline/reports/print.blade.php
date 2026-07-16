<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapport d’incidents</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 0; padding: 20px; background: #fff; }
        .doc-shell { border: 1px solid #d1d5db; border-radius: 12px; padding: 24px; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 20px; border-bottom: 2px solid #1A3A6B; padding-bottom: 10px; }
        .doc-title { font-size: 20px; font-weight: 900; color: #1A3A6B; margin: 0; }
        .doc-subtitle { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .school-block { text-align: right; }
        .school-name { font-size: 16px; font-weight: 900; color: #111827; }
        .school-meta { font-size: 12px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f9fafb; font-weight: 700; color: #374151; }
        .summary { margin-bottom: 16px; font-size: 13px; color: #374151; }
        .no-print { margin-top: 16px; }
        .btn { display: inline-block; padding: 8px 14px; border-radius: 8px; border: 1px solid #1A3A6B; color: #1A3A6B; text-decoration: none; font-weight: 700; }
        @media print { .no-print { display: none !important; } body { padding: 0; } .doc-shell { border: none; padding: 0; } }
    </style>
</head>
<body>
    <div class="doc-shell">
        <div class="no-print">
            <a href="javascript:window.print()" class="btn">Imprimer / Aperçu</a>
        </div>

        <div class="doc-header">
            <div>
                <h1 class="doc-title">Rapport d’incidents</h1>
                <p class="doc-subtitle">Document officiel de suivi disciplinaire</p>
            </div>
            <div class="school-block">
                <div class="school-name">{{ $schoolSettings?->full_name ?? 'Établissement' }}</div>
                <div class="school-meta">{{ $schoolSettings?->address ?? '' }} {{ $schoolSettings?->city ? '· ' . $schoolSettings->city : '' }}</div>
            </div>
        </div>

        <div class="summary">
            <strong>Type :</strong> {{ ['journalier'=>'Journalier','hebdomadaire'=>'Hebdomadaire','mensuel'=>'Mensuel','annuel'=>'Annuel','entre-2-dates'=>'Entre 2 dates'][$type] ?? 'Discipline' }}<br>
            <strong>Année scolaire :</strong> {{ $selectedYear?->label ?? 'Toutes' }}<br>
            @if($type === 'journalier')
                <strong>Date :</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}<br>
            @elseif($type === 'hebdomadaire')
                <strong>Semaine :</strong> {{ $week }}<br>
            @elseif($type === 'mensuel')
                <strong>Mois :</strong> {{ \Carbon\Carbon::create()->month($month)->locale('fr')->monthName }}<br>
            @elseif($type === 'entre-2-dates')
                <strong>Période :</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}<br>
            @endif
            <strong>Nombre d’incidents :</strong> {{ $incidents->count() }}
        </div>

        @if($incidents->isEmpty())
            <p style="font-size: 13px; color: #6b7280;">Aucun incident trouvé pour cette période.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Type</th>
                        <th>Sanction</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidents as $incident)
                        <tr>
                            <td>{{ $incident->incident_date->format('d/m/Y') }}</td>
                            <td>{{ $incident->studentEnrollment?->student?->full_name }}</td>
                            <td>{{ $incident->studentEnrollment?->classGroup?->full_name }}</td>
                            <td>{{ $incident->incident_type_label }}</td>
                            <td>{{ $incident->sanction_label }}</td>
                            <td>{{ $incident->status_label }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</body>
</html>
