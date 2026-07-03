@php
    $docTitleFr = $docTitleFr ?? ($docTitle ?? 'BORDEREAU EFFECTIF DE NOTES');
    $docTitleEn = $docTitleEn ?? '';
    $docSubtitle = $docSubtitle ?? null;
    $schoolNameFr = strtoupper($school->full_name);
    $schoolNameEn = strtoupper($school->full_name_en ?: $school->full_name);
    $ministryFr = strtoupper($school->ministry ?: 'MINISTERE DES ENSEIGNEMENTS SECONDAIRES');
    $ministryEn = strtoupper($school->ministry_en ?: $school->ministry ?: 'MINISTRY OF SECONDARY EDUCATION');
    $phoneLine = $phones->isNotEmpty() ? $phones->pluck('number')->join(' / ') : null;
    $agreementLines = isset($agreements) && $agreements->isNotEmpty()
        ? $agreements->map(fn ($agreement) => 'N° ' . $agreement->number)
        : collect();

    $logoSrc = null;
    if ($school->logo) {
        $logoPath = ltrim($school->logo, '/');
        $candidates = [
            public_path('storage/' . $logoPath),
            public_path($logoPath),
        ];
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $candidateAssetPath = trim(str_replace('\\', '/', str_replace(public_path(), '', $candidate)), '/');
                $logoSrc = $forPdf ? $candidate : asset($candidateAssetPath);
                break;
            }
        }
        if (! $logoSrc && str_starts_with($logoPath, 'storage/')) {
            $logoSrc = $forPdf ? public_path($logoPath) : asset($logoPath);
        }
    }
    if (! $logoSrc && file_exists(public_path('images/logo.jpg'))) {
        $logoSrc = $forPdf ? public_path('images/logo.jpg') : asset('images/logo.jpg');
    }
@endphp

<div class="bordereau-header">
    <div class="bordereau-header__brand">
        <div class="bordereau-header__logo">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo {{ $school->short_name }}">
            @else
                <div class="bordereau-header__logo-placeholder">{{ strtoupper(substr($school->short_name ?? 'C', 0, 1)) }}</div>
            @endif
        </div>
        <div class="bordereau-header__school-info">
            <div class="bordereau-header__school">{{ $schoolNameFr }}</div>
            @if($school->postal_box)
                <div class="bordereau-header__meta">B.P. {{ $school->postal_box }}</div>
            @endif
            @if($phoneLine)
                <div class="bordereau-header__meta">Tél : {{ $phoneLine }}</div>
            @endif
            <div class="bordereau-header__meta">{{ $ministryFr }}</div>
        </div>
    </div>

    <div class="bordereau-header__doc">
        <div class="bordereau-header__doc-title">DOCUMENT ADMINISTRATIF</div>
        <div class="bordereau-header__doc-copy">Exemplaire Scolarité</div>
        <div class="bordereau-header__doc-year">Année scolaire : {{ $classGroup->academicYear->label ?? '—' }}</div>
        <div class="bordereau-header__doc-date">{{ now()->locale('fr')->isoFormat('L, HH:mm') }}</div>
    </div>
</div>
<div class="bordereau-header__title-row">
    <div class="bordereau-header__title">{{ $docTitleFr }}</div>
    @if($docSubtitle)
        <div class="bordereau-header__subtitle">{{ $docSubtitle }}</div>
    @endif
</div>
