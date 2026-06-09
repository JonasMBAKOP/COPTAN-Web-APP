<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineIncident extends Model
{
    protected $fillable = [
        'student_enrollment_id',
        'incident_date',
        'incident_time',
        'location',
        'incident_type',
        'description',
        'sanction_type',
        'sanction_duration_days',
        'decided_by',
        'parent_convoked',
        'convocation_date',
        'status',
        'reported_by',
    ];

    protected function casts(): array
    {
        return [
            'incident_date'    => 'date',
            'convocation_date' => 'date',
            'parent_convoked'  => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function studentEnrollment()
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // ── Méthodes utilitaires ───────────────────────────────────────────────
    public function getSanctionLabelAttribute(): string
    {
        return match($this->sanction_type) {
            'observation'            => 'Observation',
            'warning'                => 'Avertissement',
            'detention'              => 'Retenue',
            'temporary_suspension'   => 'Renvoi temporaire',
            'definitive_exclusion'   => 'Exclusion définitive',
            default                  => 'Inconnu',
        };
    }
}