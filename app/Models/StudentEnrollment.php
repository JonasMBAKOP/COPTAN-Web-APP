<?php

namespace App\Models;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
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