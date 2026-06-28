<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableSetting extends Model
{
    protected $fillable = [
        'period_duration_minutes',
        'max_periods_per_day',
        'day_configs',
        'breaks',
    ];

    protected function casts(): array
    {
        return [
            'period_duration_minutes' => 'integer',
            'max_periods_per_day' => 'integer',
            'day_configs' => 'array',
            'breaks' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'period_duration_minutes' => 60,
            'max_periods_per_day' => 8,
            'day_configs' => self::defaultDayConfigs(),
            'breaks' => [
                ['start_time' => '10:00', 'duration_minutes' => 20],
            ],
        ]);
    }

    public static function defaultDayConfigs(): array
    {
        return collect(range(1, 5))->mapWithKeys(fn (int $day) => [
            (string) $day => [
                'start_time' => '07:30',
                'active_periods' => 8,
            ],
        ])->all();
    }
}
