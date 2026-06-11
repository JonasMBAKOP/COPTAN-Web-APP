@php
    $compact = $compact ?? false;
@endphp
<div class="doc-header {{ $compact ? 'doc-header--compact' : '' }}">
    <div class="doc-header__school">
        @if($school->logo)
            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo">
        @else
            <div class="doc-header__logo-placeholder">
                {{ strtoupper(substr($school->short_name ?? 'C', 0, 1)) }}
            </div>
        @endif
        <div>
            <div class="doc-header__name">{{ strtoupper($school->full_name) }}</div>
            @if($school->motto)
                <div class="doc-header__motto">« {{ $school->motto }} »</div>
            @endif
            <div class="doc-header__meta">
                @if($school->address){{ $school->address }}@endif
                @if($school->postal_box) — BP {{ $school->postal_box }}@endif
                @if($school->city) — {{ $school->city }}@endif
                @if($phones->isNotEmpty())
                    — Tél. {{ $phones->pluck('number')->join(' / ') }}
                @endif
            </div>
            @if($school->ministry)
                <div class="doc-header__ministry">{{ $school->ministry }}</div>
            @endif
        </div>
    </div>
    @isset($docTitle)
    <div class="doc-header__title">{{ $docTitle }}</div>
    @endisset
    @isset($docSubtitle)
    <div class="doc-header__subtitle">{{ $docSubtitle }}</div>
    @endisset
</div>
