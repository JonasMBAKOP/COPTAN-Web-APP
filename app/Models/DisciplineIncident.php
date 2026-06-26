<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineIncident extends Model
{
    public const LOCATIONS = [
        'classroom' => 'Salle de classe',
        'courtyard' => 'Cour de récréation',
        'corridor'  => 'Couloir',
        'cafeteria' => 'Cantine',
        'other'     => 'Autre lieu',
    ];

    public const INCIDENT_TYPES = [
        'retard'       => 'Retard',
        'comportement' => 'Comportement incorrect',
        'fraude'       => 'Fraude / Triche',
        'violence'     => 'Violence',
        'autre'        => 'Autre',
    ];

    public const SANCTIONS = [
        'observation'            => 'Observation',
        'warning'                => 'Avertissement',
        'detention'              => 'Retenue / Détention',
        'temporary_suspension'   => 'Renvoi temporaire',
        'definitive_exclusion'   => 'Exclusion définitive',
    ];

    public const STATUSES = [
        'open'   => 'Ouvert',
        'closed' => 'Clôturé',
    ];

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

    public function getSanctionLabelAttribute(): string
    {
        return self::SANCTIONS[$this->sanction_type] ?? '—';
    }

    public function getLocationLabelAttribute(): string
    {
        return self::LOCATIONS[$this->location] ?? '—';
    }

    public function getIncidentTypeLabelAttribute(): string
    {
        return self::INCIDENT_TYPES[$this->incident_type] ?? $this->incident_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
