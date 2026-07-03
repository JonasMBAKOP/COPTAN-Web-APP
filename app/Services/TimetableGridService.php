<?php

namespace App\Services;

use App\Models\TimetableSetting;

class TimetableGridService
{
    public function buildGrid(TimetableSetting $setting, array $days): array
    {
        $rows = [];

        for ($period = 1; $period <= $setting->max_periods_per_day; $period++) {
            $periods[$period] = 'P' . $period;
        }

        foreach ($days as $dayNumber => $dayName) {
            $dayRows = $this->buildDayRows($setting, $dayNumber);
            foreach ($dayRows as $row) {
                $key = $row['type'] === 'period' ? 'period_' . $row['period_index'] : 'break_' . $row['start'];
                $rows[$key]['type'] = $row['type'];
                $rows[$key]['label'] = $row['label'];
                $rows[$key]['period_index'] = $row['period_index'] ?? null;
                $rows[$key]['times'][$dayNumber] = $row;
            }
        }

        return [
            'rows' => array_values($rows),
            'periods' => $periods,
        ];
    }

    public function periodWindow(TimetableSetting $setting, int $dayNumber, int $periodIndex, int $periodsCount): ?array
    {
        $periodRows = collect($this->buildDayRows($setting, $dayNumber))
            ->where('type', 'period')
            ->keyBy('period_index');
        $lastPeriod = $periodIndex + $periodsCount - 1;

        if (! isset($periodRows[$periodIndex], $periodRows[$lastPeriod])) {
            return null;
        }

        if (! $periodRows[$periodIndex]['is_active'] || ! $periodRows[$lastPeriod]['is_active']) {
            return null;
        }

        return [
            'start' => $periodRows[$periodIndex]['start'],
            'end' => $periodRows[$lastPeriod]['end'],
        ];
    }

    private function buildDayRows(TimetableSetting $setting, int $dayNumber): array
    {
        $dayConfig = $this->normalizedDayConfigs($setting)[$dayNumber];
        $cursor = $this->minutesFromTime($dayConfig['start_time']);
        $activePeriods = (int) $dayConfig['active_periods'];
        $breaks = collect($setting->breaks ?: [])
            ->map(fn ($break) => [
                'start' => $this->minutesFromTime($break['start_time']),
                'duration' => (int) $break['duration_minutes'],
            ])
            ->sortBy('start')
            ->values();
        $breakIndex = 0;
        $rows = [];

        for ($period = 1; $period <= $setting->max_periods_per_day; $period++) {
            while (isset($breaks[$breakIndex]) && $breaks[$breakIndex]['start'] <= $cursor) {
                $breakStart = $cursor;
                $breakEnd = $breakStart + $breaks[$breakIndex]['duration'];
                $rows[] = [
                    'type' => 'break',
                    'label' => 'Pause',
                    'start' => $this->timeFromMinutes($breakStart),
                    'end' => $this->timeFromMinutes($breakEnd),
                    'is_active' => $period <= $activePeriods,
                ];
                $cursor = $breakEnd;
                $breakIndex++;
            }

            $start = $cursor;
            $end = $cursor + $setting->period_duration_minutes;
            $rows[] = [
                'type' => 'period',
                'label' => 'P' . $period,
                'period_index' => $period,
                'start' => $this->timeFromMinutes($start),
                'end' => $this->timeFromMinutes($end),
                'is_active' => $period <= $activePeriods,
            ];
            $cursor = $end;
        }

        return $rows;
    }

    private function normalizedDayConfigs(TimetableSetting $setting): array
    {
        $configs = $setting->day_configs ?: TimetableSetting::defaultDayConfigs();

        return collect($configs)->mapWithKeys(function ($config, $dayNumber) use ($setting) {
            return [
                (int) $dayNumber => [
                    'start_time' => $config['start_time'] ?? '07:30',
                    'active_periods' => min((int) ($config['active_periods'] ?? $setting->max_periods_per_day), $setting->max_periods_per_day),
                ],
            ];
        })->all();
    }

    private function minutesFromTime(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);

        return ((int) $hours * 60) + (int) $minutes;
    }

    private function timeFromMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }
}
