@php
    $showYearForm = $showYearForm ?? true;
@endphp
<div class="mb-6 space-y-4">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        @if($showYearForm && $years->isNotEmpty())
            <form method="GET"
                  action="{{ url()->current() }}"
                  class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                     style="background:#EBF3FB;">
                    <svg class="h-5 w-5" style="color:#1A3A6B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">
                        Année scolaire
                    </label>
                    <select name="year_id"
                            onchange="this.form.submit()"
                            class="mt-0.5 w-full border-0 bg-transparent p-0 text-sm font-bold outline-none focus:ring-0"
                            style="color:#1A3A6B;">
                        @foreach($years as $year)
                            <option value="{{ $year->id }}" {{ $selectedYear?->id == $year->id ? 'selected' : '' }}>
                                {{ $year->label }}{{ $year->is_active ? ' (Active)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @foreach(request()->except(['year_id', 'page']) as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
            </form>
        @endif

        @if(!empty($actions))
            <div class="flex flex-wrap items-center gap-2">
                {!! $actions !!}
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
        @include('finances.partials.management-nav', ['active' => $active])
    </div>
</div>
