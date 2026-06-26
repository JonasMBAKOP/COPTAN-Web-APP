<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>{{ $documentTitle ?? 'Bulletin' }} — {{ $enrollment->student->full_name }}</title>
@include('bulletins.partials.pdf-styles')
</head>
<body>
@include('bulletins.partials.pdf-page')
</body>
</html>
