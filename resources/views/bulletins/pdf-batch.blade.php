<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Bulletins — {{ $classGroup->full_name }}</title>
@include('bulletins.partials.pdf-styles')
</head>
<body>

@foreach($bulletins as $b)
    <div class="page-break">
        @include('bulletins.partials.pdf-page', $b)
    </div>
@endforeach

</body>
</html>