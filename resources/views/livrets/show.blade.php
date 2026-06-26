<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livret Scolaire — {{ $enrollment->student->full_name }}</title>
    @include('livrets.partials.livret-styles')
</head>
<body class="has-toolbar">
    <div class="toolbar no-print">
        <span>Livret Scolaire — <strong>{{ $enrollment->student->full_name }}</strong></span>
        <button onclick="window.print()">🖨 Imprimer</button>
        <button onclick="window.close()">✕ Fermer</button>
    </div>
    @include('livrets.partials.livret-page', array_merge(get_defined_vars(), ['forPdf' => false]))
</body>
</html>
