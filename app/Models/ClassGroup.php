<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    protected $fillable = [
        'academic_year_id',
        'level_id',
        'name',
        'sub_group',
        'series',
        'max_students',
        'titular_staff_id',
        'room',
    ];

    protected function casts(): array
    {
        return [
            'max_students' => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function titularStaff()
    {
        return $this->belongsTo(Staff::class, 'titular_staff_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function studentEnrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function timetableSlots()
    {
        return $this->hasMany(TimetableSlot::class);
    }

    public function gradeLocks()
    {
        return $this->hasMany(GradeLock::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    // Nom complet de la classe (ex: "3ème B — Série C")
    public function getFullNameAttribute(): string
    {
        $name = $this->name;
        if ($this->sub_group) $name .= ' ' . $this->sub_group;
        if ($this->series)    $name .= ' — ' . $this->series;
        return $name;
    }

    // Nombre d'élèves inscrits
    public function getStudentsCountAttribute(): int
    {
        return $this->studentEnrollments()
                    ->where('status', 'active')
                    ->count();
    }
}