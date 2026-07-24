@php
    $kpiColor    = $color    ?? '#1A3A6B';
    $kpiBg       = $bg       ?? '#EBF3FB';
    $kpiSuffix   = $suffix   ?? null;
    $kpiHint     = $hint     ?? null;
    $kpiProgress = $progress ?? null;
    $kpiDelay    = $delay    ?? '0s';
    $kpiIcon     = $icon     ?? null;
    $kpiFooter   = $footer   ?? null;
@endphp

<div class="fin-kpi bg-white rounded-xl shadow-sm border border-gray-100 p-4 relative overflow-hidden"
     style="animation-delay: {{ $kpiDelay }};">

    <div class="flex items-center gap-3 mb-3">
        @if($kpiIcon)
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" style="background:{{ $kpiBg }};">
                {!! $kpiIcon !!}
            </div>
        @endif
        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 leading-tight">{{ $label }}</p>
    </div>

    <p class="text-2xl font-black leading-tight" style="color:{{ $kpiColor }};">
        {{ $value }}
        @if($kpiSuffix)
            <span class="text-sm font-semibold text-gray-400">{{ $kpiSuffix }}</span>
        @endif
    </p>

    @if($kpiHint)
        <p class="mt-1 text-xs text-gray-400">{{ $kpiHint }}</p>
    @endif

    @if($kpiProgress !== null)
        <div class="relative mt-3 h-1 overflow-hidden rounded-full bg-gray-100">
            <div class="fin-progress h-full rounded-full"
                 style="background:{{ $kpiColor }}; width:0;"
                 data-width="{{ min(100, max(0, (int) $kpiProgress)) }}%"></div>
        </div>
    @endif

    @if($kpiFooter)
        <div class="relative mt-3 border-t border-gray-100 pt-3">
            {!! $kpiFooter !!}
        </div>
    @endif
</div>
