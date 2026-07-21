<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartes professionnelles</title>
    @include('students.documents.partials.card-styles')
    <style>
        @page { size: A4 portrait; margin: 2mm; }
        body { margin: 0; padding: 0; background: #fff; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }
        .print-toolbar-wrapper { padding: 2mm 2mm 0; }
        .cards-container { padding: 1mm 2mm 2mm; }
        .cards-page {
            width: 100%;
            max-width: 194mm;
            margin: 0 auto 2mm;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-template-rows: repeat(5, minmax(0, 1fr));
            gap: 1.5mm 2.5mm;
            page-break-after: always;
            page-break-inside: avoid;
        }
        .cards-page:last-child { page-break-after: auto; margin-bottom: 0; }
        .card-slot {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-slot .id-card {
            width: 90mm;
            height: 54mm;
            transform: none;
        }
        @media print {
            body { margin: 0; }
            .print-toolbar-wrapper { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-toolbar-wrapper">
        @include('students.documents.partials.print-toolbar')
    </div>

    <div class="cards-container">
        {{-- <div class="cards-intro">
            <div class="cards-intro__title">Cartes professionnelles — Personnel</div>
            <div class="cards-intro__meta">{{ $staff->count() }} carte(s) prête(s) à l’impression</div>
        </div> --}}
        @foreach($staff->chunk(10) as $chunk)
            <div class="cards-page">
                @foreach($chunk as $s)
                    <div class="card-slot">
                        @include('staff.documents.partials.card', ['staff' => $s])
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</body>
</html>
