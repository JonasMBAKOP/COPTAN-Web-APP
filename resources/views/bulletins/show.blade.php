<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>{{ $documentTitle }} — {{ $enrollment->student->full_name }}</title>
@include('bulletins.partials.pdf-styles')
<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; padding: 0 !important; }
    .bulletin-page { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; }
}
body { background: #E5E7EB; }
.bulletin-page { margin: 20px auto; border: 1px solid #ddd; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
</style>
</head>
<body>
@include('students.documents.partials.print-toolbar')

@include('bulletins.partials.pdf-page')

</body>
</html>