@props(['active' => ''])

@php
    $items = [
        'global' => [
            'route'  => 'finances.global',
            'label'  => 'Vue globale',
            'roles'  => ['super-admin', 'directeur', 'fondateur'],
        ],
        'index' => [
            'route' => 'finances.index',
            'label' => 'Par élève',
        ],
        'payments' => [
            'route' => 'finances.payments',
            'label' => 'Paiements',
        ],
        'scholarships' => [
            'route' => 'finances.scholarships',
            'label' => 'Bourses',
        ],
        // 'fees' => [
        //     'route' => 'finances.fees-list',
        //     'label' => 'Tranches',
        // ],
        // 'reports' => [
        //     'route' => 'finances.reports',
        //     'label' => 'Rapports',
        // ],
    ];
@endphp

<nav class="flex flex-wrap gap-2" aria-label="Navigation finances">
    @foreach($items as $key => $item)
        @if(!empty($item['roles']) && !auth()->user()->hasAnyRole($item['roles']))
            @continue
        @endif
        <a href="{{ route($item['route'], request()->only('year_id')) }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all
                  {{ $active === $key
                      ? 'text-white shadow-sm'
                      : 'border border-gray-200 bg-white text-gray-600 hover:border-[#1A3A6B]/30 hover:text-[#1A3A6B]' }}"
           @if($active === $key) style="background:#1A3A6B;" @endif>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
