<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livrets Scolaires — {{ $classGroup->full_name }}</title>
    @include('livrets.partials.livret-styles')
</head>
<body class="has-toolbar">
    <div class="toolbar no-print">
        <span>Impression en Masse des Livrets — <strong>{{ $classGroup->full_name }}</strong> ({{ $livrets->count() }} élèves)</span>
        <button onclick="window.print()">🖨 Imprimer tout</button>
        <button onclick="window.close()">✕ Fermer</button>
    </div>

    @foreach($livrets as $livretData)
        @include('livrets.partials.livret-page', array_merge($livretData, ['forPdf' => false]))
    @endforeach
</body>
</html>
