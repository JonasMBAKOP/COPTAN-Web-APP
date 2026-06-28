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
        <button onclick="window.print()"><svg class="inline h-4 w-4 mr-1 align-[-2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 18H5a2 2 0 01-2-2v-5a2 2 0 012-2h14a2 2 0 012 2v5a2 2 0 01-2 2h-1M7 14h10v6H7z"/></svg>Imprimer</button>
        <button onclick="window.close()"><svg class="inline h-4 w-4 align-[-2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>Fermer</button>
    </div>
    @include('livrets.partials.livret-page', array_merge(get_defined_vars(), ['forPdf' => false]))
</body>
</html>
