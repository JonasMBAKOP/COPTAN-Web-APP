<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudentEnrollment extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE         = 'active';
    public const STATUS_INACTIVE       = 'inactive';
    public const STATUS_TRANSFERRED_OUT = 'transferred_out';
    public const STATUS_WITHDRAWN      = 'withdrawn';

    protected $fillable = [
        'student_id',
        'class_group_id',
        'academic_year_id',
        'enrollment_date',
        'is_repeating',
        'previous_class_group_id',
        'previous_class_label',
        'origin_school',
        'status',
        'transfer_date',
        'transfer_destination',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'transfer_date'   => 'date',
            'is_repeating'    => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function previousClassGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'previous_class_group_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function bulletinReports()
    {
        return $this->hasMany(BulletinReport::class);
    }

    public function payments()
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function disciplineIncidents()
    {
        return $this->hasMany(DisciplineIncident::class);
    }

    public function selectedClassSubjects()
    {
        return $this->belongsToMany(
            ClassSubject::class,
            'student_subjects',
            'student_enrollment_id',
            'class_subject_id'
        )->withTimestamps();
    }

    public function isEligibleForSubjectSelection(): bool
    {
        $section = $this->classGroup?->level?->section;
        $levelName = Str::lower((string) $this->classGroup?->level?->name);
        $sectionName = Str::lower((string) $section?->name);

        if (! $section || ! Str::contains($sectionName, 'anglophone')) {
            return false;
        }

        return (bool) preg_match('/\bform\s*([0-9]+)/i', $levelName, $matches)
            && (int) $matches[1] >= 4;
    }

    public function enrollmentAudit()
    {
        return $this->hasOne(AuditLog::class, 'model_id', 'id')
            ->where('model_type', 'StudentEnrollment')
            ->where('action', 'enrolled')
            ->latestOfMany();
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // Total des absences non justifiées en heures
    public function getUnjustifiedAbsencesHoursAttribute(): float
    {
        return $this->absences()
                    ->where('is_justified', false)
                    ->sum('hours');
    }
}
