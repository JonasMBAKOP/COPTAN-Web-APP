@php
    $compact = $compact ?? false;
    $docTitleFr = $docTitleFr ?? ($docTitle ?? '');
    $docTitleEn = $docTitleEn ?? '';
    $docSubtitle = $docSubtitle ?? null;
@endphp
<div class="bilingual-header {{ $compact ? 'bilingual-header--compact' : '' }}">
    <!-- Logo central -->
    <div class="bilingual-header__logo">
        @if($school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->short_name }}">
        @else
            <div class="bilingual-header__logo-placeholder">
                {{ strtoupper(substr($school->short_name ?? 'C', 0, 1)) }}
            </div>
        @endif
    </div>

    <!-- Informations établissement - Français à gauche -->
    <div class="bilingual-header__info bilingual-header__info--fr">
        <div class="bilingual-header__name">{{ strtoupper($school->full_name) }}</div>
        @if($school->motto)
            <div class="bilingual-header__motto">« {{ $school->motto }} »</div>
        @endif
        <div class="bilingual-header__meta">
            @if($school->address){{ $school->address }}@endif
            @if($school->postal_box) — BP {{ $school->postal_box }}@endif
            @if($school->city) — {{ $school->city }}@endif
            @if($phones->isNotEmpty())
                — Tél. {{ $phones->pluck('number')->join(' / ') }}
            @endif
            @if($school->email) — {{ $school->email }}@endif
        </div>
        @if($school->ministry)
            <div class="bilingual-header__ministry">{{ $school->ministry }}</div>
        @endif
        <!-- Numéros d'agréments -->
        @if(isset($agreements) && $agreements->isNotEmpty())
            <div class="bilingual-header__agreements">
                @foreach($agreements as $agreement)
                    <span class="bilingual-header__agreement">
                        N°{{ $agreement->number }} 
                        @if($agreement->cycle === 'premier_cycle')(1er Cycle)@elseif($agreement->cycle === 'second_cycle')(2nd Cycle)@endif
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Informations établissement - Anglais à droite -->
    <div class="bilingual-header__info bilingual-header__info--en">
        <div class="bilingual-header__name">{{ strtoupper($school->full_name_en ?? $school->full_name) }}</div>
        @if($school->motto_en)
            <div class="bilingual-header__motto">« {{ $school->motto_en }} »</div>
        @elseif($school->motto)
            <div class="bilingual-header__motto">« {{ $school->motto }} »</div>
        @endif
        <div class="bilingual-header__meta">
            @if($school->address_en){{ $school->address_en }}@elseif($school->address){{ $school->address }}@endif
            @if($school->postal_box) — P.O. Box {{ $school->postal_box }}@endif
            @if($school->city) — {{ $school->city }}@endif
            @if($phones->isNotEmpty())
                — Tel. {{ $phones->pluck('number')->join(' / ') }}
            @endif
            @if($school->email) — {{ $school->email }}@endif
        </div>
        @if($school->ministry_en)
            <div class="bilingual-header__ministry">{{ $school->ministry_en }}</div>
        @elseif($school->ministry)
            <div class="bilingual-header__ministry">{{ $school->ministry }}</div>
        @endif
        <!-- Numéros d'agréments en anglais -->
        @if(isset($agreements) && $agreements->isNotEmpty())
            <div class="bilingual-header__agreements">
                @foreach($agreements as $agreement)
                    <span class="bilingual-header__agreement">
                        No.{{ $agreement->number }} 
                        @if($agreement->cycle === 'premier_cycle')(1st Cycle)@elseif($agreement->cycle === 'second_cycle')(2nd Cycle)@endif
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Titre du document -->
    @if($docTitleFr || $docTitleEn)
    <div class="bilingual-header__title">
        @if($docTitleFr)<span class="bilingual-header__title--fr">{{ $docTitleFr }}</span>@endif
        @if($docTitleEn)<span class="bilingual-header__title--en">{{ $docTitleEn }}</span>@endif
    </div>
    @endif
    @if($docSubtitle)
    <div class="bilingual-header__subtitle">{{ $docSubtitle }}</div>
    @endif
</div>
