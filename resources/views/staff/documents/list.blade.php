<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $titles[$type] }} - {{ $school->short_name ?? 'Etablissement' }}</title>
    @include('students.documents.partials.base-styles')
    <style>
        @page { size: A4 portrait; margin: 5mm; }
        .staff-list-page { max-width: 200mm; margin: 0 auto; padding: 2mm 1mm; }
        /* .staff-list-title { margin: 7mm 0 4mm; text-align: center; font-size: 17px; font-weight: 800; color: #1A3A6B; letter-spacing: .04em; } */
        .staff-list-title { background: #E5E7EB; color: #000; border: 1px solid #4B5563; padding: 8px 10px; margin-bottom: 12px; text-align: center; font-family: Georgia, 'Times New Roman', serif; font-size: 21px; font-weight: 900; }
        .staff-list-subtitle { margin-bottom: 5mm; text-align: center; font-size: 14px; color: #1A3A6B; font-weight: 600; }
        .staff-list-table { width: 100%; border-collapse: collapse; font-size: 9px; }
        .staff-list-table th, .staff-list-table td { border: 1px solid #94A3B8; padding: 6px 5px; vertical-align: middle; }
        .staff-list-table th { background: #EFF6FF; color: #000; text-align: center; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .staff-list-table td { color: #000; font-size: 10px; font-weight: 600; text-transform: uppercase; }
        .staff-list-table td:first-child, .staff-list-table td:nth-child(3), .staff-list-table td:nth-child(4) { text-align: center; }
        .staff-list-table .staff-list-name { font-weight: 700; color: #172554; }
        .empty-row { padding: 12px !important; text-align: center !important; color: #64748B; font-style: italic; }
        .bordereau-header { display: grid; grid-template-columns: 1.45fr .9fr; gap: 18px; align-items: start; padding-bottom: 8px; margin-bottom: 8px; border-bottom: 1px solid #CBD5E1; }
        .bordereau-header__brand { display: flex; gap: 12px; align-items: center; }
        .bordereau-header__logo { width: 68px; min-width: 68px; display: flex; align-items: center; justify-content: center; }
        .bordereau-header__logo img { max-width: 64px; max-height: 64px; object-fit: contain; }
        .bordereau-header__logo-placeholder { width: 62px; height: 62px; border-radius: 14px; background: #1A3A6B; color: #fff; display: grid; place-items: center; font-size: 24px; font-weight: 800; }
        .bordereau-header__school-info { display: grid; gap: 2px; }
        .bordereau-header__school { font-size: 11px; font-weight: 800; color: #1A3A6B; }
        .bordereau-header__meta { font-size: 8px; color: #475569; }
        .bordereau-header__doc { display: grid; gap: 2px; justify-items: end; text-align: right; }
        .bordereau-header__doc-title { font-size: 11px; font-weight: 800; color: #1A3A6B; }
        .bordereau-header__doc-copy { font-size: 9px; font-weight: 700; color: #A35200; }
        .bordereau-header__doc-year { font-size: 9px; color: #1A3A6B; font-weight: 700; }
        .bordereau-header__title-row { margin: 7px 0 10px; text-align: center; }
        .bordereau-header__title { font-size: 17px; font-weight: 800; color: #1A3A6B; text-decoration: underline; }
        .bordereau-header__subtitle { margin-top: 2px; font-size: 9px; color: #475569; }
        @media print { .staff-list-page { max-width: none; padding: 0; } }
    </style>
</head>
<body>
@include('students.documents.partials.print-toolbar')

<main class="page staff-list-page">
    
    @if($type === 'administrative')
        @include('students.documents.partials.certificate-official-header', ['showCertificateTitle' => false])
        <div class="staff-list-title">
            {{-- <h1 class="staff-list-title">{{ $titles[$type] }}</h1> --}}
            <div>{{ $titles[$type] }}</div>
            <div style="margin-top: 3px; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: 700; text-transform: none;">Annee scolaire : {{ $activeYear?->label ?? now()->format('Y') }} — {{ $staff->count() }} membre(s)</div>
        </div>
    @else
        @include('grades.partials.bordereau-header', [
            'docTitleFr' => $titles[$type],
            // 'docSubtitle' => 'Personnel actif',
            'forPdf' => false,
        ])
        <div class="staff-list-subtitle">Annee scolaire : {{ $activeYear?->label ?? now()->format('Y') }} — {{ $staff->count() }} membre(s)</div>
    @endif

    {{-- <div class="staff-list-subtitle">Annee scolaire : {{ $activeYear?->label ?? now()->format('Y') }}</div> --}}

    <table class="staff-list-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Noms et Prenoms</th>
                <th>Date de Naissance</th>
                <th>Sexe</th>
                <th>Poste</th>
                <th>Grade</th>
                <th>Contact</th>
                @if($type === 'administrative')
                    <th>Type de contrat</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($staff as $index => $member)
                @php
                    $gender = match (strtolower((string) $member->gender)) {
                        'm', 'male', 'masculin', 'homme' => 'M',
                        'f', 'female', 'feminin', 'femme' => 'F',
                        default => $member->gender ?: '-',
                    };
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ mb_strtoupper($member->full_name) }}</td>
                    <td>{{ $member->date_of_birth?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $gender }}</td>
                    <td>{{ $member->primaryPosition?->position_label ?? 'Personnel' }}</td>
                    <td>{{ $member->diploma_label ?? '-' }}</td>
                    <td>{{ $member->phone ?? '-' }}</td>
                    @if($type === 'administrative')
                        <td>{{ $member->contract_label }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $type === 'administrative' ? 8 : 7 }}" class="empty-row">Aucun membre du personnel ne correspond a cette liste.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</main>
</body>
</html>