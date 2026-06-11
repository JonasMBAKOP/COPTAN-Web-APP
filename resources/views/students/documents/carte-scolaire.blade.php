<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Carte scolaire — {{ $student->full_name }}</title>
@include('students.documents.partials.card-styles')
<style>
@page { size: A4 portrait; margin: 12mm; }
@media print { .no-print { display: none !important; } body { background:#fff!important;padding:0!important; } }
* { margin:0;padding:0;box-sizing:border-box; }
body { font-family: Arial, sans-serif; background:#eef1f5; padding:16px; }
.single-card-wrap {
    width: fit-content;
    margin: 24px auto;
    padding: 6mm;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
}
</style>
</head>
<body>
@include('students.documents.partials.print-toolbar')

<div class="single-card-wrap">
    @include('students.documents.partials.card-item', [
        'student' => $student,
        'enrollment' => $enrollment,
    ])
</div>
<p style="text-align:center;font-size:10px;color:#6B7280;margin-top:12px;" class="no-print">
    Format carte d'identité scolaire (85,6 × 54 mm) — découpez le cadre après impression.
</p>
</body>
</html>
