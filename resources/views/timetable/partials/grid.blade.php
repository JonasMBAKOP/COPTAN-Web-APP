@php
    $bilingualDays = [
        1 => 'LUNDI / MONDAY',
        2 => 'MARDI / TUESDAY',
        3 => 'MERCREDI / WEDNESDAY',
        4 => 'JEUDI / THURSDAY',
        5 => 'VENDREDI / FRIDAY',
    ];
    $mode = $mode ?? 'class';
    $printable = $printable ?? false;
    $conflicts = $conflicts ?? collect();
    $teacherSubjectCount = $teacherSubjectCount ?? 0;
    $renderedUntil = [];
    $rowCount = count($gridRows);

    $rowInterval = function (array $row): string {
        $cell = collect($row['times'] ?? [])->first();
        return $cell ? $cell['start'] . ' - ' . $cell['end'] : '';
    };

    $slotStartingAt = function (int $dayNumber, int $periodIndex) use ($slots) {
        return $slots->first(fn ($slot) =>
            (int) $slot->day_of_week === $dayNumber
            && (int) $slot->period_index === $periodIndex
        );
    };

    $slotCoveringAfterBreak = function (int $dayNumber, int $periodIndex) use ($slots) {
        return $slots->first(function ($slot) use ($dayNumber, $periodIndex) {
            $start = (int) $slot->period_index;
            $end = $start + (int) $slot->periods_count - 1;
            return (int) $slot->day_of_week === $dayNumber && $start < $periodIndex && $end >= $periodIndex;
        });
    };

    $segmentSpan = function ($slot, int $rowIndex, int $dayNumber) use ($gridRows, $rowCount): int {
        $endPeriod = (int) $slot->period_index + (int) $slot->periods_count - 1;
        $span = 0;

        for ($i = $rowIndex; $i < $rowCount; $i++) {
            $candidate = $gridRows[$i];
            if (($candidate['type'] ?? null) !== 'period') {
                break;
            }

            $period = (int) $candidate['period_index'];
            if ($period > $endPeriod) {
                break;
            }

            $cell = $candidate['times'][$dayNumber] ?? null;
            if (! $cell || ! ($cell['is_active'] ?? false)) {
                break;
            }

            $span++;
        }

        return max($span, 1);
    };
@endphp

<table class="{{ $printable ? 'timetable-print' : 'w-full min-w-[1080px] border-separate border-spacing-0' }}">
    <thead>
        <tr class="{{ $printable ? '' : 'bg-gray-50' }}">
            <th class="{{ $printable ? 'period' : 'sticky left-0 z-10 w-36 border-b border-gray-100 bg-gray-50 px-4 py-3 text-left text-xs font-black uppercase tracking-wide text-gray-500' }}">PERIODS / HEURES</th>
            @foreach($days as $dayNumber => $dayName)
                <th class="{{ $printable ? '' : 'border-b border-gray-100 px-3 py-3 text-center text-xs font-black uppercase tracking-wide text-gray-500' }}">{{ $bilingualDays[$dayNumber] ?? strtoupper($dayName) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($gridRows as $rowIndex => $row)
            @if($row['type'] === 'break')
                <tr class="{{ $printable ? 'break-row' : 'bg-amber-50/60' }}">
                    <td class="{{ $printable ? 'period' : 'sticky left-0 z-10 border-b border-amber-100 bg-amber-50 px-4 py-2 text-xs font-black text-amber-700' }}">{{ $rowInterval($row) }}</td>
                    <td colspan="{{ count($days) }}" class="{{ $printable ? '' : 'border-b border-amber-100 px-3 py-3 text-center text-xs font-black uppercase tracking-wide text-amber-700' }}">PAUSE / BREAK TIME</td>
                </tr>
            @else
                <tr>
                    <td class="{{ $printable ? 'period' : 'sticky left-0 z-10 border-b border-gray-50 bg-white px-4 py-2 align-middle text-xs font-black text-gray-700' }}">{{ $rowInterval($row) }}</td>
                    @foreach($days as $dayNumber => $dayName)
                        @php
                            $periodIndex = (int) $row['period_index'];
                            $skipCell = ($renderedUntil[$dayNumber] ?? 0) >= $periodIndex;
                        @endphp
                        @continue($skipCell)

                        @php
                            $cell = $row['times'][$dayNumber] ?? null;
                            $slot = $slotStartingAt($dayNumber, $periodIndex) ?: $slotCoveringAfterBreak($dayNumber, $periodIndex);
                            $rowspan = $slot ? $segmentSpan($slot, $rowIndex, $dayNumber) : 1;

                            if ($slot) {
                                $renderedUntil[$dayNumber] = $periodIndex + $rowspan - 1;
                            }
                        @endphp
                        <td rowspan="{{ $rowspan }}" class="{{ $printable ? '' : 'border-b border-gray-50 px-1 py-0.5 text-center align-middle ' . (($cell && !($cell['is_active'] ?? false)) ? 'bg-gray-50' : '') }}" @if(!$printable) style="height:38px; vertical-align:middle; text-align:center;" @endif>
                            @if($slot)
                                @php
                                    $isConflict = $conflicts->contains($slot->id);
                                    $teacher = $slot->classSubject?->teacherAssignments?->first()?->staff;
                                    $subject = $slot->classSubject?->subject;
                                    $blockMinHeight = $rowspan * 34;
                                @endphp
                                @if($mode === 'teacher')
                                    <div class="{{ $printable ? 'slot' : 'flex h-full min-h-[34px] flex-col items-center justify-center rounded-xl border border-green-100 bg-green-50 p-1 text-center shadow-sm' }}" @if(!$printable) style="min-height: {{ $blockMinHeight }}px;" @endif>
                                        <strong class="{{ $printable ? '' : 'block text-xs font-black text-[#1A5C2A]' }}">{{ $slot->classGroup?->full_name }}</strong>
                                        @if($teacherSubjectCount !== 1)
                                            <span class="{{ $printable ? '' : 'mt-1 block text-[11px] font-semibold text-gray-600' }}">{{ $subject?->name_fr }}</span>
                                        @endif
                                        @if(!$printable && $slot->room)<p class="mt-1 text-[10px] font-bold text-gray-500">{{ $slot->room }}</p>@endif
                                    </div>
                                @else
                                    <div class="{{ $printable ? 'slot' : 'flex h-full min-h-[34px] flex-col items-center justify-center rounded-xl border p-1 text-center shadow-sm ' . ($isConflict ? 'border-red-300 bg-red-50' : 'border-blue-100 bg-blue-50') }}" @if(!$printable) style="min-height: {{ $blockMinHeight }}px;" @can('manage-timetable') role="button" @click="openEdit({{ $slot->id }}, {{ $slot->class_subject_id }}, {{ $slot->day_of_week }}, {{ $slot->period_index }}, {{ $slot->periods_count }}, @js($slot->room))" @endcan @endif>
                                        <strong class="{{ $printable ? '' : 'block text-xs font-black ' . ($isConflict ? 'text-red-800' : 'text-[#1A3A6B]') }}">{{ $subject?->name_fr ?? 'Matière' }}</strong>
                                        <span class="{{ $printable ? '' : 'mt-1 block text-[11px] font-semibold text-gray-600' }}">{{ $teacher?->full_name ?? 'Enseignant non assigné' }}</span>
                                        @if(!$printable && $slot->room)<p class="mt-1 text-[10px] font-bold text-gray-500">{{ $slot->room }}</p>@endif
                                    </div>
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endif
        @endforeach
    </tbody>
</table>