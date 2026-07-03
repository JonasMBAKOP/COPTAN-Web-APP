@php
    $schoolNameFr = strtoupper($school->full_name);
    $schoolNameEn = strtoupper($school->full_name_en ?: 'NTANKEU POLYVALENT COLLEGE');
    $ministryFr = strtoupper($school->ministry ?: 'MINISTERE DES ENSEIGNEMENTS SECONDAIRES');
    $ministryEn = strtoupper($school->ministry_en ?: 'MINISTRY OF SECONDARY EDUCATION');
    $phoneLine = $phones->isNotEmpty() ? $phones->pluck('number')->join(' / ') : null;
    $agreementLines = isset($agreements) && $agreements->isNotEmpty()
        ? $agreements->map(fn ($agreement) => 'N° ' . $agreement->number)
        : collect();
    $showCertificateTitle = $showCertificateTitle ?? true;
    $forPdf = $forPdf ?? false;

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

<header class="cert-official-header">
    <div class="cert-official-header__columns">
        <div class="cert-official-header__side cert-official-header__side--fr">
            <div class="cert-official-header__republic">REPUBLIQUE DU CAMEROUN</div>
            <div class="cert-official-header__motto">Paix-Travail-Patrie</div>
            <div class="cert-official-header__stars">********</div>
            <div class="cert-official-header__ministry">{{ $ministryFr }}</div>
            <div class="cert-official-header__stars">********</div>
            <div class="cert-official-header__school">{{ $schoolNameFr }}</div>
            <div class="cert-official-header__motto">{{ $school->motto ?: 'Paix-Travail-Patrie' }}</div>
            @if($phoneLine)
                <div class="cert-official-header__meta">Tél. {{ $phoneLine }}</div>
            @endif
            <div class="cert-official-header__meta">
                @if($school->postal_box) B.P. {{ $school->postal_box }} @endif
            </div>
            @if($school->email)
                <div class="cert-official-header__email"><span>E-mail :</span> {{ $school->email }}</div>
            @endif
        </div>

        <div class="cert-official-header__logo">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo {{ $school->short_name }}">
            @else
                <div class="cert-official-header__logo-placeholder">
                    {{ strtoupper(substr($school->short_name ?? 'C', 0, 1)) }}
                </div>
            @endif
        </div>

        <div class="cert-official-header__side cert-official-header__side--en">
            <div class="cert-official-header__republic">REPUBLIC OF CAMEROON</div>
            <div class="cert-official-header__motto">Peace-Work-Fatherland</div>
            <div class="cert-official-header__stars">********</div>
            <div class="cert-official-header__ministry">{{ $ministryEn }}</div>
            <div class="cert-official-header__stars">********</div>
            <div class="cert-official-header__school">{{ $schoolNameEn }}</div>
            <div class="cert-official-header__motto">{{ $school->motto_en ?: 'Peace-Work-Fatherland' }}</div>
            @if($phoneLine)
                <div class="cert-official-header__meta">Phone. {{ $phoneLine }}</div>
            @endif
            <div class="cert-official-header__meta">
                @if($school->postal_box) P.O. BOX {{ $school->postal_box }} @endif
            </div>
            @if($school->email)
                <div class="cert-official-header__email"><span>Email :</span> {{ $school->email }}</div>
            @endif
        </div>
    </div>

    @if($agreementLines->isNotEmpty())
        <div class="cert-official-header__agreements">
            @foreach($agreementLines as $agreementLine)
                <div>{{ $agreementLine }}</div>
            @endforeach
        </div>
    @endif

    @if($showCertificateTitle)
        <div class="cert-official-header__title">
            <div>CERTIFICAT DE SCOLARITE</div>
            <div>SCHOOL ATTESTATION</div>
        </div>
    @endif
</header>
