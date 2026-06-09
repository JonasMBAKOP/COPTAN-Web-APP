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
        'start_time',
        'end_time',
        'room',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
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

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function getDayNameAttribute(): string
    {
        return match($this->day_of_week) {
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            default => '?',
        };
    }
}