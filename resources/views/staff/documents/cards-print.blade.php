<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartes professionnelles</title>
    @include('students.documents.partials.card-styles')
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { margin: 0; padding: 0; background: #fff; }
        .cards-container { display: flex; flex-wrap: wrap; gap: 6mm; justify-content: center; padding: 10mm; }
        .cards-container > div { page-break-inside: avoid; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="cards-container">
        @foreach($staff as $s)
            <div>
                @include('staff.documents.partials.card', ['staff' => $s])
            </div>
        @endforeach
    </div>
</body>
</html>
