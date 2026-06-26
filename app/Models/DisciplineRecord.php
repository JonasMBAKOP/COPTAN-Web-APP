<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'student_id',
        'class_id',
        'reported_by',
        'incident_date',
        'incident_type',
        'description',
        'sanction_type',
        'sanction_days',
        'sanction_start',
        'sanction_end',
        'status',
        'notes_internes',
        'convocation_parent',
        'convocation_date',
    ];

    protected $casts = [
        'incident_date'      => 'date',
        'sanction_start'     => 'date',
        'sanction_end'       => 'date',
        'convocation_date'   => 'date',
        'convocation_parent' => 'boolean',
    ];

    // ─── Labels statiques ────────────────────────────────────────────────────

    public static array $incidentTypes = [
        'retard'            => 'Retard répété',
        'insolence'         => 'Insolence / Manque de respect',
        'bagarre'           => 'Bagarre / Violence',
        'fraude'            => 'Fraude / Tricherie',
        'absenteisme'       => 'Absentéisme',
        'degradation'       => 'Dégradation de biens',
        'tenue_incorrecte'  => 'Tenue incorrecte',
        'autre'             => 'Autre',
    ];

    public static array $sanctionTypes = [
        'aucune'              => 'Aucune sanction',
        'avertissement'       => 'Avertissement',
        'blame'               => 'Blâme',
        'retenue'             => 'Retenue',
        'renvoi_temporaire'   => 'Renvoi temporaire',
        'exclusion_definitive'=> 'Exclusion définitive',
    ];

    public static array $statusLabels = [
        'ouvert'  => 'Ouvert',
        'resolu'  => 'Résolu',
        'classe'  => 'Classé',
    ];

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getIncidentTypeLabelAttribute(): string
    {
        return self::$incidentTypes[$this->incident_type] ?? $this->incident_type;
    }

    public function getSanctionTypeLabelAttribute(): string
    {
        return self::$sanctionTypes[$this->sanction_type] ?? $this->sanction_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getSanctionBadgeColorAttribute(): string
    {
        return match($this->sanction_type) {
            'aucune'               => 'gray',
            'avertissement'        => 'yellow',
            'blame'                => 'orange',
            'retenue'              => 'blue',
            'renvoi_temporaire'    => 'red',
            'exclusion_definitive' => 'red',
            default                => 'gray',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'ouvert'  => 'yellow',
            'resolu'  => 'green',
            'classe'  => 'gray',
            default   => 'gray',
        };
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function schoolYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classe()
    {
        return $this->belongsTo(AcademicYear::class, 'class_id');
    }

    public function reporter()
    {
        return $this->belongsTo(Staff::class, 'reported_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeCurrentYear($query)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        return $activeYear ? $query->where('school_year_id', $activeYear->id) : $query;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'ouvert');
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }
}