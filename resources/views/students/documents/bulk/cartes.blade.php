<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cartes scolaires — impression groupée</title>
    @include('students.documents.partials.card-styles')
    <style>
        @page { size: A4 portrait; margin: 5mm; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; margin: 0 !important; }
            .cards-sheet { page-break-after: always; box-shadow: none !important; margin: 0 !important; }
            .cards-sheet:last-child { page-break-after: auto; }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #eef1f5; padding: 8px; }
        
        .cards-sheet {
            width: 210mm;
            height: 297mm;
            margin: 0 auto 10px;
            background: #fff;
            padding: 5mm;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 3mm;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .cards-sheet .id-card {
            margin: 0;
        }
    </style>
</head>
<body>
    @include('students.documents.partials.print-toolbar')

    @foreach($students->chunk(8) as $chunk)
        <div class="cards-sheet">
            @foreach($chunk as $student)
                @include('students.documents.partials.card-item', [
                    'student' => $student,
                    'enrollment' => $student->printEnrollment,
                ])
            @endforeach
        </div>
    @endforeach

    <p class="no-print" style="text-align: center; font-size: 11px; color: #6B7280; margin: 12px;">
        {{ $students->count() }} carte(s) — {{ ceil($students->count() / 8) }} page(s) A4 (8 cartes par page en 2×4)
    </p>
</body>
</html>
</html>
