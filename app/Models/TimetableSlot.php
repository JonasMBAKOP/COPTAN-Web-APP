<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    protected $fillable = [
        'academic_year_id',
        'class_group_id',
        'class_subject_id',
        'day_of_week',
        'period_index',
        'periods_count',
        'start_time',
        'end_time',
        'room',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_index' => 'integer',
            'periods_count' => 'integer',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function getDayNameAttribute(): string
    {
        return match ($this->day_of_week) {
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            default => '?',
        };
    }

    public function getEndPeriodAttribute(): int
    {
        return (int) $this->period_index + (int) $this->periods_count - 1;
    }
}