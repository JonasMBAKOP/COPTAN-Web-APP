<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    protected $fillable = [
        'academic_year_id',
        'trimester_id',
        'number',
        'label',
        'start_date',
        'end_date',
        'is_grades_locked',
    ];

    protected function casts(): array
    {
        return [
            'start_date'       => 'date',
            'end_date'         => 'date',
            'number'           => 'integer',
            'is_grades_locked' => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function trimester()
    {
        return $this->belongsTo(Trimester::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function gradeLocks()
    {
        return $this->hasMany(GradeLock::class);
    }

    public function bulletinReports()
    {
        return $this->hasMany(BulletinReport::class);
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    // Vérifie si les notes sont verrouillées pour une classe donnée
    public function isLockedFor(int $classGroupId): bool
    {
        if ($this->is_grades_locked) return true;

        return $this->gradeLocks()
                    ->where('class_group_id', $classGroupId)
                    ->where('is_locked', true)
                    ->exists();
    }
}